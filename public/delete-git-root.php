<?php
header('Content-Type: text/plain');

$target = dirname(__DIR__) . '/.git';
$output = [];
$retval = null;

// Forcefully remove directory using Windows cmd rmdir
exec('cmd /c "rmdir /s /q ' . escapeshellarg($target) . '" 2>&1', $output, $retval);

echo "Result Code: $retval\nOutput:\n" . implode("\n", $output);
