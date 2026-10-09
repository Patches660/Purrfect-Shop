<?php
// api_behavior.php - Member Behavioral Analytics & Quiz Engine API
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

$action = $_REQUEST['action'] ?? 'get_behavior';

// 1. Log Quiz Submission
if ($action === 'log_quiz') {
    // Read JSON body or POST form data
    $raw_input = file_get_contents('php://input');
    $json = json_decode($raw_input, true);
    
    $payload = is_array($json) ? $json : $_POST;
    
    $curr_user = getCurrentUser();
    $user_name = $payload['user_name'] ?? ($curr_user ? ($curr_user['fullname'] ?? $curr_user['username']) : 'ผู้เยี่ยมชม (Guest)');
    
    $sub_data = [
        'user_name' => $user_name,
        'matched_breed' => trim($payload['matched_breed'] ?? 'Scottish Fold'),
        'match_score' => floatval($payload['match_score'] ?? 98.6),
        'answers' => $payload['answers'] ?? [],
        'trait_scores' => $payload['trait_scores'] ?? [
            'living_match' => 98,
            'time_match' => 95,
            'personality_match' => 99,
            'grooming_match' => 94,
            'exp_match' => 90
        ]
    ];

    $saved = recordQuizSubmission($sub_data);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'บันทึกผลการทำแบบทดสอบ AI Matchmaker สำเร็จ!',
        'data' => $saved
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Log User Behavioral Event (Clicks, Search, Filter)
if ($action === 'log_event') {
    $event_type = trim($_REQUEST['event_type'] ?? 'cat_click');
    $target = trim($_REQUEST['target'] ?? 'general');
    $category = trim($_REQUEST['category'] ?? 'general');
    
    trackUserBehavior($event_type, $target, $category);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'บันทึกพฤติกรรมเรียบร้อย'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3. Get Behavior Analytics Summary
if ($action === 'get_behavior') {
    $summary = getAnalyticsSummary();
    $data = $summary['analytics'];
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'summary' => $summary
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
