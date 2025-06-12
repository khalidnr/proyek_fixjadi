<?php

function writeLog($level, $message, $context = []) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = [
        'timestamp' => $timestamp,
        'level' => $level,
        'message' => $message,
        'context' => $context,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
}

function logDatabaseError($error, $query = '') {
    writeLog('ERROR', 'Database error occurred', [
        'error' => $error,
        'query' => $query
    ]);
}

writeLog('INFO', 'Database connection established');

$con = mysqli_connect('db', '123', '123', 'phplogin'); 

if (!$con) {
    die('❌ Koneksi MySQL gagal: ' . mysqli_connect_error());
} else {
}

?>
