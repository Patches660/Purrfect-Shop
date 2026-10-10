<?php
/**
 * config_secrets.example.php - Template for Secret Configurations
 * 
 * วิธีใช้งาน:
 * 1. คัดลอกไฟล์นี้แล้วเปลี่ยนชื่อเป็น config_secrets.php
 * 2. นำ Channel Access Token, Channel Secret และ Dify API Key มาใส่
 * 3. ไฟล์ config_secrets.php จะถูกป้องกันไม่ให้อัปขึ้น GitHub โดยอัตโนมัติ
 */

return [
    // LINE Developers Messaging API Credentials
    'line_channel_access_token' => 'YOUR_LINE_CHANNEL_ACCESS_TOKEN_HERE',
    'line_channel_secret'       => 'YOUR_LINE_CHANNEL_SECRET_HERE',

    // Dify AI Cloud API Key
    'dify_api_key'              => 'YOUR_DIFY_API_KEY_HERE',
];
