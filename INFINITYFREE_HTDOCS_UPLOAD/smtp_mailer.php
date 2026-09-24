<?php
/**
 * Purrfect Cat Cyber Shop - Pure PHP SMTP Mailer
 * 
 * Lightweight, zero-dependency SMTP client supporting:
 * - STARTTLS (Port 587) and SSL/TLS (Port 465)
 * - AUTH LOGIN authentication (RFC 4954)
 * - UTF-8 MIME headers and HTML body (RFC 2045, RFC 5322)
 * - Detailed communication logging for diagnostics
 */

class SmtpMailer {
    private array $logs = [];
    private string $lastError = '';

    public function getLogs(): array {
        return $this->logs;
    }

    public function getLastError(): string {
        return $this->lastError;
    }

    private function log(string $direction, string $message): void {
        // Redact passwords from logs for security
        $safeMessage = preg_replace('/(AUTH LOGIN\s*\r?\n)([A-Za-z0-9+\/=]+)(\r?\n)([A-Za-z0-9+\/=]+)/i', '$1[USER_BASE64]$3[PASS_REDACTED]', $message);
        $this->logs[] = [
            'time' => date('H:i:s'),
            'direction' => $direction, // 'CLIENT', 'SERVER', 'INFO', 'ERROR'
            'message' => trim($safeMessage)
        ];
    }

    /**
     * Send an HTML email via SMTP
     * 
     * @param string $toEmail Recipient email
     * @param string $toName Recipient display name
     * @param string $subject Email subject
     * @param string $htmlBody HTML body content
     * @param array|null $config SMTP configuration array
     * @return array ['success' => bool, 'message' => string, 'response' => string, 'logs' => array]
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?array $config = null): array {
        $mailer = new self();
        return $mailer->dispatch($toEmail, $toName, $subject, $htmlBody, $config);
    }

    /**
     * Test SMTP connection and authentication without sending email body
     * 
     * @param array $config SMTP configuration array
     * @return array ['success' => bool, 'message' => string, 'logs' => array]
     */
    public static function testConnection(array $config): array {
        $mailer = new self();
        return $mailer->verifyAuth($config);
    }

    /**
     * Internal dispatch implementation
     */
    public function dispatch(string $toEmail, string $toName, string $subject, string $htmlBody, ?array $config = null): array {
        $this->logs = [];
        $this->lastError = '';

        if ($config === null && function_exists('getSmtpConfig')) {
            $config = getSmtpConfig();
        }

        $host = trim($config['host'] ?? 'smtp.gmail.com');
        $port = intval($config['port'] ?? 587);
        $encryption = strtolower(trim($config['encryption'] ?? 'tls'));
        $username = trim($config['username'] ?? '');
        $password = trim($config['password'] ?? '');
        $fromEmail = trim($config['from_email'] ?? $username);
        $fromName = trim($config['from_name'] ?? 'Purrfect Cat Boutique 🐾');
        $timeout = intval($config['timeout'] ?? 15);

        if (empty($host) || empty($port)) {
            $this->lastError = 'ไม่ได้ระบุ SMTP Host หรือ Port';
            $this->log('ERROR', $this->lastError);
            return ['success' => false, 'message' => $this->lastError, 'logs' => $this->logs];
        }

        if (empty($username) || empty($password)) {
            $this->lastError = 'ไม่ได้ระบุ Username หรือ App Password ของผู้ส่ง';
            $this->log('ERROR', $this->lastError);
            return ['success' => false, 'message' => $this->lastError, 'logs' => $this->logs];
        }

        // Determine socket protocol
        $remoteAddress = ($encryption === 'ssl' ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}");
        $this->log('INFO', "Connecting to {$remoteAddress} (timeout: {$timeout}s)...");

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client($remoteAddress, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            $this->lastError = "เชื่อมต่อ SMTP Server ล้มเหลว ({$errno}): {$errstr}";
            $this->log('ERROR', $this->lastError);
            return ['success' => false, 'message' => $this->lastError, 'logs' => $this->logs];
        }

        stream_set_timeout($socket, $timeout);

        try {
            // 1. Read Server Greeting (220)
            $response = $this->readResponse($socket);
            if (!$this->checkCode($response, '220')) {
                throw new Exception("Greeting ผิดพลาด: {$response}");
            }

            // 2. EHLO Handshake
            $clientHost = gethostname() ?: 'localhost';
            $response = $this->sendCommand($socket, "EHLO {$clientHost}");
            if (!$this->checkCode($response, '250')) {
                // Fallback to HELO
                $response = $this->sendCommand($socket, "HELO {$clientHost}");
                if (!$this->checkCode($response, '250')) {
                    throw new Exception("EHLO/HELO ผิดพลาด: {$response}");
                }
            }

            // 3. STARTTLS if configured
            if ($encryption === 'tls') {
                $response = $this->sendCommand($socket, "STARTTLS");
                if (!$this->checkCode($response, '220')) {
                    throw new Exception("STARTTLS ปฏิเสธการเชื่อมต่อ: {$response}");
                }

                $this->log('INFO', "Upgrading connection to TLS crypto stream...");
                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                }

                $cryptoSuccess = @stream_socket_enable_crypto($socket, true, $cryptoMethod);
                if (!$cryptoSuccess) {
                    throw new Exception("TLS Handshake ล้มเหลว (ไม่สามารถสร้างช่องสัญญาณเข้ารหัสได้)");
                }

                // Resend EHLO after TLS is established
                $response = $this->sendCommand($socket, "EHLO {$clientHost}");
                if (!$this->checkCode($response, '250')) {
                    throw new Exception("EHLO หลังเริ่ม TLS ผิดพลาด: {$response}");
                }
            }

            // 4. AUTH LOGIN
            $response = $this->sendCommand($socket, "AUTH LOGIN");
            if (!$this->checkCode($response, '334')) {
                throw new Exception("Server ไม่รองรับ AUTH LOGIN: {$response}");
            }

            // Send Username (Base64)
            $this->log('CLIENT', "AUTH Username [Base64: " . base64_encode($username) . "]");
            fwrite($socket, base64_encode($username) . "\r\n");
            $response = $this->readResponse($socket);
            if (!$this->checkCode($response, '334')) {
                throw new Exception("Username ไม่ถูกต้อง หรือเซิร์ฟเวอร์ปฏิเสธ: {$response}");
            }

            // Send Password (Base64)
            $this->log('CLIENT', "AUTH Password [Base64: ********]");
            fwrite($socket, base64_encode($password) . "\r\n");
            $response = $this->readResponse($socket);
            if (!$this->checkCode($response, '235')) {
                if (strpos($host, 'gmail') !== false) {
                    throw new Exception("ยืนยันตัวตน Gmail ไม่ผ่าน ({$response}) กรุณาใช้ 'รหัสผ่านสำหรับแอป 16 หลัก' (Google App Password) แทนรหัสผ่านปกติ");
                }
                throw new Exception("รหัสผ่านไม่ถูกต้อง หรือ Authentication ล้มเหลว: {$response}");
            }

            // 5. MAIL FROM
            $response = $this->sendCommand($socket, "MAIL FROM:<{$fromEmail}>");
            if (!$this->checkCode($response, '250')) {
                throw new Exception("MAIL FROM ปฏิเสธ: {$response}");
            }

            // 6. RCPT TO
            $response = $this->sendCommand($socket, "RCPT TO:<{$toEmail}>");
            if (!$this->checkCode($response, '250')) {
                throw new Exception("RCPT TO ปฏิเสธ (ที่อยู่อีเมลผู้รับไม่ถูกต้อง): {$response}");
            }

            // 7. DATA
            $response = $this->sendCommand($socket, "DATA");
            if (!$this->checkCode($response, '354')) {
                throw new Exception("DATA ปฏิเสธ: {$response}");
            }

            // 8. Build RFC 5322 MIME Content
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
            $encodedToName = '=?UTF-8?B?' . base64_encode($toName) . '?=';
            $messageId = sprintf("<%s.%s@%s>", uniqid(date('YmdHis_')), bin2hex(random_bytes(4)), $host);
            $rfcDate = date('r');

            $headers = [];
            $headers[] = "Date: {$rfcDate}";
            $headers[] = "From: {$encodedFromName} <{$fromEmail}>";
            $headers[] = "To: {$encodedToName} <{$toEmail}>";
            $headers[] = "Subject: {$encodedSubject}";
            $headers[] = "Message-ID: {$messageId}";
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "Content-Transfer-Encoding: base64";
            $headers[] = "X-Mailer: PurrfectShop Live SMTP Client 2.0";

            $headersStr = implode("\r\n", $headers);
            $bodyStr = chunk_split(base64_encode($htmlBody));

            $emailPayload = $headersStr . "\r\n\r\n" . $bodyStr . "\r\n.\r\n";

            $this->log('CLIENT', "Sending RFC 5322 MIME Email Payload (" . strlen($emailPayload) . " bytes)...");
            fwrite($socket, $emailPayload);

            $response = $this->readResponse($socket);
            if (!$this->checkCode($response, '250')) {
                throw new Exception("การส่งอีเมลไม่สำเร็จ: {$response}");
            }

            $successResponse = trim($response);

            // 9. QUIT
            $this->sendCommand($socket, "QUIT");
            fclose($socket);

            $this->log('INFO', "Email successfully accepted by SMTP server! Response: {$successResponse}");

            return [
                'success' => true,
                'message' => 'ส่งเข้าอีเมลจริงสำเร็จผ่าน SMTP Server เรียบร้อยแล้ว',
                'response' => $successResponse,
                'logs' => $this->logs
            ];

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->log('ERROR', $this->lastError);

            if (is_resource($socket)) {
                @fwrite($socket, "QUIT\r\n");
                @fclose($socket);
            }

            return [
                'success' => false,
                'message' => $this->lastError,
                'logs' => $this->logs
            ];
        }
    }

    /**
     * Test connection and auth
     */
    public function verifyAuth(array $config): array {
        $this->logs = [];
        $this->lastError = '';

        $host = trim($config['host'] ?? 'smtp.gmail.com');
        $port = intval($config['port'] ?? 587);
        $encryption = strtolower(trim($config['encryption'] ?? 'tls'));
        $username = trim($config['username'] ?? '');
        $password = trim($config['password'] ?? '');
        $timeout = intval($config['timeout'] ?? 10);

        if (empty($host) || empty($port)) {
            return ['success' => false, 'message' => 'ไม่ได้ระบุ Host หรือ Port', 'logs' => $this->logs];
        }

        $remoteAddress = ($encryption === 'ssl' ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}");
        $this->log('INFO', "Testing connection to {$remoteAddress}...");

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client($remoteAddress, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            $this->lastError = "เชื่อมต่อพอร์ตล้มเหลว ({$errno}): {$errstr}";
            $this->log('ERROR', $this->lastError);
            return ['success' => false, 'message' => $this->lastError, 'logs' => $this->logs];
        }

        stream_set_timeout($socket, $timeout);

        try {
            $response = $this->readResponse($socket);
            if (!$this->checkCode($response, '220')) {
                throw new Exception("Greeting ผิดพลาด: {$response}");
            }

            $clientHost = gethostname() ?: 'localhost';
            $response = $this->sendCommand($socket, "EHLO {$clientHost}");

            if ($encryption === 'tls') {
                $response = $this->sendCommand($socket, "STARTTLS");
                if (!$this->checkCode($response, '220')) {
                    throw new Exception("STARTTLS ปฏิเสธ: {$response}");
                }

                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                }

                $cryptoSuccess = @stream_socket_enable_crypto($socket, true, $cryptoMethod);
                if (!$cryptoSuccess) {
                    throw new Exception("TLS Handshake ล้มเหลว");
                }

                $response = $this->sendCommand($socket, "EHLO {$clientHost}");
            }

            if (!empty($username) && !empty($password)) {
                $response = $this->sendCommand($socket, "AUTH LOGIN");
                if (!$this->checkCode($response, '334')) {
                    throw new Exception("Server ไม่รองรับ AUTH LOGIN: {$response}");
                }

                $this->log('CLIENT', "AUTH Username [Base64: " . base64_encode($username) . "]");
                fwrite($socket, base64_encode($username) . "\r\n");
                $response = $this->readResponse($socket);
                if (!$this->checkCode($response, '334')) {
                    throw new Exception("Username ไม่ถูกต้อง: {$response}");
                }

                $this->log('CLIENT', "AUTH Password [Base64: ********]");
                fwrite($socket, base64_encode($password) . "\r\n");
                $response = $this->readResponse($socket);
                if (!$this->checkCode($response, '235')) {
                    if (strpos($host, 'gmail') !== false) {
                        throw new Exception("รหัสผ่านไม่ผ่าน ({$response}) Gmail ต้องใช้ App Password 16 หลัก");
                    }
                    throw new Exception("รหัสผ่านไม่ถูกต้อง ({$response})");
                }
            }

            $this->sendCommand($socket, "QUIT");
            fclose($socket);

            return [
                'success' => true,
                'message' => 'การเชื่อมต่อและการยืนยันตัวตน SMTP สำเร็จสมบูรณ์ พร้อมส่งอีเมลจริงได้ทันที!',
                'logs' => $this->logs
            ];

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->log('ERROR', $this->lastError);
            if (is_resource($socket)) {
                @fwrite($socket, "QUIT\r\n");
                @fclose($socket);
            }
            return [
                'success' => false,
                'message' => $this->lastError,
                'logs' => $this->logs
            ];
        }
    }

    private function sendCommand($socket, string $cmd): string {
        $this->log('CLIENT', $cmd);
        fwrite($socket, $cmd . "\r\n");
        return $this->readResponse($socket);
    }

    private function readResponse($socket): string {
        $response = '';
        while (!feof($socket)) {
            $line = fgets($socket, 512);
            if ($line === false) break;
            $response .= $line;
            // In RFC 5321, multi-line responses have '-' at index 3 (e.g. 250-SIZE), final has ' ' or newline
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
            if (strlen(trim($line)) === 3) {
                break;
            }
        }
        $this->log('SERVER', trim($response));
        return $response;
    }

    private function checkCode(string $response, string $expectedCode): bool {
        return substr(trim($response), 0, strlen($expectedCode)) === $expectedCode;
    }
}
