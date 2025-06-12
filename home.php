<?php
ini_set('session.cookie_httponly', 1);       
ini_set('session.cookie_secure', 0);         
ini_set('session.cookie_samesite', 'Strict'); 

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

writeLog('INFO', 'Home page accessed', [
    'user_id' => $_SESSION['user_id'] ?? 'guest'
]);

session_start();
require 'db.php';

if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['account_name']); ?></h2>
    <p><a href="profile.php?id=<?php echo (int)$_SESSION['account_id']; ?>">Lihat Profil</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>


