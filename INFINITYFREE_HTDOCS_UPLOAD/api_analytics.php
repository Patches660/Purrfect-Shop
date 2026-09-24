<?php
// api_analytics.php - Realtime Behavior Tracking API
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

$action = $_REQUEST['action'] ?? 'track';

if ($action === 'track') {
    $event_type = trim($_REQUEST['event_type'] ?? 'click');
    $target = trim($_REQUEST['target'] ?? 'unknown');
    $category = trim($_REQUEST['category'] ?? 'general');
    $details = $_REQUEST['details'] ?? [];

    $res = trackUserBehavior($event_type, $target, $category, $details);
    echo json_encode(['status' => 'success', 'tracked' => $res], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'get_summary') {
    if (!isAdmin()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $summary = getAnalyticsSummary();
    echo json_encode(['status' => 'success', 'data' => $summary], JSON_UNESCAPED_UNICODE);
    exit;
}
