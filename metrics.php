<?php
// File: metrics.php
require_once 'logger.php';

header('Content-Type: text/plain');

// Simple metrics untuk Prometheus
$logs = Logger::getInstance()->getRecentLogs('combined', 1000);
$errorCount = count(array_filter($logs, fn($log) => $log['level'] === 'ERROR'));
$requestCount = count($logs);

echo "# HELP http_requests_total Total number of HTTP requests\n";
echo "# TYPE http_requests_total counter\n";
echo "http_requests_total $requestCount\n\n";

echo "# HELP http_errors_total Total number of HTTP errors\n";
echo "# TYPE http_errors_total counter\n";
echo "http_errors_total $errorCount\n";
?>
