<?php
/**
 * Pure-PHP EmailJS REST API Mailer Client
 * 
 * Directly dispatches emails through the official EmailJS REST API v1.0
 * Endpoint: https://api.emailjs.com/api/v1.0/email/send
 * Zero external Composer dependencies required!
 * Developer: Purrfect Cattery Team 🐾
 */

class EmailJsMailer {
    const API_ENDPOINT = 'https://api.emailjs.com/api/v1.0/email/send';

    /**
     * Send email via EmailJS REST API
     *
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $htmlBody
     * @param array  $config ['service_id', 'template_id', 'public_key', 'private_key', 'from_name', 'from_email']
     * @param array  $extraParams Additional template parameters
     * @return array ['success' => bool, 'message' => string, 'code' => int, 'logs' => array, 'response' => string]
     */
    public static function send($toEmail, $toName, $subject, $htmlBody, $config, $extraParams = []) {
        $logs = [];
        $log = function($dir, $msg) use (&$logs) {
            $logs[] = [
                'time' => date('H:i:s'),
                'direction' => $dir,
                'message' => $msg
            ];
        };

        $serviceId = trim($config['service_id'] ?? '');
        $templateId = trim($config['template_id'] ?? '');
        $publicKey = trim($config['public_key'] ?? ($config['user_id'] ?? ''));
        $privateKey = trim($config['private_key'] ?? ($config['access_token'] ?? ''));

        if (empty($serviceId) || empty($templateId) || empty($publicKey)) {
            $log('ERROR', 'Missing required EmailJS parameters: service_id, template_id, or public_key');
            return [
                'success' => false,
                'message' => 'ข้อมูลตั้งค่า EmailJS ไม่ครบถ้วน (ต้องการ Service ID, Template ID และ Public Key)',
                'code' => 400,
                'logs' => $logs,
                'response' => ''
            ];
        }

        $log('INFO', 'Connecting to EmailJS REST API: ' . self::API_ENDPOINT);
        $log('INFO', 'Service ID: ' . $serviceId . ' | Template ID: ' . $templateId);
        $log('INFO', 'Recipient: ' . $toName . ' (' . $toEmail . ')');

        $conciseText = mb_strimwidth(strip_tags($htmlBody), 0, 800, '...');

        // Minify HTML to stay well under EmailJS 50KB total variables payload limit
        $minifiedHtml = preg_replace('/>\s+</', '><', $htmlBody);
        $minifiedHtml = preg_replace('/\s{2,}/', ' ', $minifiedHtml);
        $safeHtmlBody = (strlen($minifiedHtml) > 28000)
            ? mb_strimwidth($minifiedHtml, 0, 28000, '') . '</td></tr></table></body></html>'
            : $minifiedHtml;

        // Build template params compatible with all common EmailJS variable patterns
        $templateParams = array_merge([
            'to_email' => $toEmail,
            'email' => $toEmail,
            'user_email' => $toEmail,
            'recipient_email' => $toEmail,
            'to' => $toEmail,
            'send_to' => $toEmail,
            'reply_to' => $toEmail,
            'to_name' => $toName,
            'name' => $toName,
            'user_name' => $toName,
            'recipient_name' => $toName,
            'user' => $toName,
            'from_name' => $config['from_name'] ?? 'Purrfect Cattery & Boutique 🐾',
            'from_email' => $config['from_email'] ?? 'purrfect.cattery.shop@gmail.com',
            'subject' => $subject,
            'message' => $safeHtmlBody,
            'message_html' => $safeHtmlBody,
            'message_text' => $conciseText,
            'plain_text' => $conciseText,
            'voucher_code' => $extraParams['voucher_code'] ?? 'WELCOME15',
            'member_id' => $extraParams['member_id'] ?? strtoupper(substr(md5($toEmail), 0, 8)),
            'sent_at' => date('Y-m-d H:i:s')
        ], is_array($extraParams) ? $extraParams : []);


        $payload = [
            'service_id' => $serviceId,
            'template_id' => $templateId,
            'user_id' => $publicKey,
            'template_params' => $templateParams
        ];

        if (!empty($privateKey)) {
            $payload['accessToken'] = $privateKey;
            $log('INFO', 'Attaching Private Key (accessToken) for strict authentication');
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $log('CLIENT', 'POST ' . self::API_ENDPOINT . ' (' . strlen($jsonPayload) . ' bytes payload)');

        $httpCode = 0;
        $responseBody = '';
        $errorMsg = '';

        $browserOrigin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
        $browserUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

        if (function_exists('curl_init')) {
            $ch = curl_init(self::API_ENDPOINT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json, text/plain, */*',
                'Origin: ' . $browserOrigin,
                'Referer: ' . $browserOrigin . '/',
                'User-Agent: ' . $browserUserAgent
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errorMsg = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n" .
                                "Accept: application/json, text/plain, */*\r\n" .
                                "Origin: {$browserOrigin}\r\n" .
                                "Referer: {$browserOrigin}/\r\n" .
                                "User-Agent: {$browserUserAgent}\r\n",
                    'content' => $jsonPayload,
                    'timeout' => 15,
                    'ignore_errors' => true
                ]
            ]);
            $responseBody = @file_get_contents(self::API_ENDPOINT, false, $context);
            if (isset($http_response_header) && preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $m)) {
                $httpCode = intval($m[1]);
            }
        }

        $log('SERVER', 'HTTP ' . $httpCode . ' Response: ' . (string)$responseBody);

        if ($httpCode === 200 || trim((string)$responseBody) === 'OK') {
            $log('INFO', 'EmailJS Delivery Success: 200 OK (Email accepted by EmailJS API)');
            return [
                'success' => true,
                'message' => 'ส่งผ่าน EmailJS สำเร็จ 100% (200 OK - Email Accepted)',
                'code' => 200,
                'response' => (string)$responseBody,
                'logs' => $logs
            ];
        } else {
            $detail = !empty($responseBody) ? (string)$responseBody : (!empty($errorMsg) ? $errorMsg : ('HTTP Error ' . $httpCode));
            
            $friendlyHint = '';
            if (stripos($detail, 'corrupted') !== false) {
                $friendlyHint = ' 💡 สาเหตุ: รูปแบบอีเมลผู้รับไม่ถูกต้อง (ตรวจสอบว่าในหน้าเว็บคุณกรอกเป็น "อีเมลจริง" เช่น oavatan@gmail.com และใน EmailJS Dashboard ช่อง To Email ใส่ {{to_email}} ถูกต้อง)';
            } elseif (stripos($detail, 'empty') !== false) {
                $friendlyHint = ' 💡 สาเหตุ: EmailJS ไม่พบอีเมลผู้รับ (ตรวจสอบว่าใน EmailJS Dashboard ช่อง "To Email" ใส่ {{to_email}} และกดปุ่ม Save เรียบร้อยแล้วหรือยัง)';
            } elseif ($httpCode === 403) {
                $friendlyHint = ' 💡 สาเหตุ: โดนบล็อก Non-browser (ตรวจสอบ EmailJS Dashboard -> Account -> Security ว่าเปิดสวิตช์ "Allow EmailJS API for non-browser applications" หรือยัง)';
            }

            $log('ERROR', 'EmailJS Delivery Failed: ' . $detail . $friendlyHint);
            return [
                'success' => false,
                'message' => 'EmailJS ส่งไม่สำเร็จ (รหัส HTTP ' . $httpCode . '): ' . $detail . $friendlyHint,
                'code' => $httpCode,
                'response' => (string)$responseBody,
                'logs' => $logs
            ];
        }
    }
}