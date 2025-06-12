<?php
// File: health.php
require_once 'logger.php';

header('Content-Type: application/json');

$health = Logger::getInstance()->healthCheck();
echo json_encode($health, JSON_PRETTY_PRINT);

logInfo('Health check performed');
?>
