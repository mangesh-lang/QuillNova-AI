<?php
header('Content-Type: text/plain');

// Change working directory to Laravel root safely
if (!chdir(dirname(__DIR__))) {
    die("Error: Could not change directory to Laravel project root.");
}

// Unset overriding git environment variables
putenv('GIT_DIR');
putenv('GIT_WORK_TREE');

$output = [];
$retval = null;

// 1. Git Init
exec('git init 2>&1', $output, $retval);
echo "1. Git Init (Code: $retval):\n" . implode("\n", $output) . "\n\n";

// 2. Git Config User Identity
$output = [];
exec('git config user.name "mangesh-lang" 2>&1', $output, $retval);
exec('git config user.email "ymangesh103@gmail.com" 2>&1', $output, $retval);
echo "2. Git Identity configured.\n\n";

// 3. Add Remote Origin
$output = [];
exec('git remote add origin https://github.com/mangesh-lang/QuillNova-AI.git 2>&1', $output, $retval);
echo "3. Add Remote Origin (Code: $retval):\n" . implode("\n", $output) . "\n\n";

// 4. Git Add All
$output = [];
exec('git add . 2>&1', $output, $retval);
echo "4. Git Add (Code: $retval):\n" . implode("\n", $output) . "\n\n";

// 5. Git Commit
$output = [];
$msg = "Initialize QuillNova project with dynamic glassmorphic UI, free Groq model support, and SMTP dynamic mailer bindings";
exec('git commit -m "' . addslashes($msg) . '" 2>&1', $output, $retval);
echo "5. Git Commit (Code: $retval):\n" . implode("\n", $output) . "\n\n";

// 6. Rename branch to main
$output = [];
exec('git branch -M main 2>&1', $output, $retval);

// 7. Git Push
$output = [];
exec('git push -u origin main 2>&1', $output, $retval);
echo "7. Git Push (Code: $retval):\n" . implode("\n", $output) . "\n\n";
