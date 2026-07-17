<?php
header('Content-Type: text/plain');
if (!chdir(dirname(__DIR__))) die("chdir failed");
putenv('GIT_DIR'); putenv('GIT_WORK_TREE');
$o = []; $r = null;

// Stage everything including deletions
exec('git add -A 2>&1', $o, $r);
echo "Add -A: $r\n" . implode("\n", $o) . "\n\n";

// Status check
$o = [];
exec('git status 2>&1', $o, $r);
echo "Status: \n" . implode("\n", $o) . "\n\n";

// Commit
$o = [];
exec('git commit -m "Add Railway deployment config and cleanup temp scripts" 2>&1', $o, $r);
echo "Commit: $r\n" . implode("\n", $o) . "\n\n";

// Push
$o = [];
exec('git push origin main 2>&1', $o, $r);
echo "Push: $r\n" . implode("\n", $o) . "\n";
