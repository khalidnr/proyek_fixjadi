<?php

if (!is_dir('logs')) {
    mkdir('logs', 0755, true);
}

// Fungsi logging sederhana
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
    ];
    
    $logLine = json_encode($logEntry) . PHP_EOL;
    
    // Log ke file sesuai level
    $filename = 'logs/' . strtolower($level) . '.log';
    file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    
    // Log semua ke combined.log
    file_put_contents('logs/combined.log', $logLine, FILE_APPEND | LOCK_EX);
}

// Log setiap request yang masuk
writeLog('INFO', 'Request received', [
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI']
]);

session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>Login</h2>
<form method="POST" action="authenticate.php">
  <label>Username:</label><input type="text" name="username"><br>
  <label>Password:</label><input type="password" name="password"><br>
  <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
  <button type="submit">Login</button>
</form>
<p>Belum punya akun? <a href="register.php">Register di sini</a></p>
</body>
</html>
