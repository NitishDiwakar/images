<?php
# Author  : Nitish Kumar Diwakar
# Email   : nitishkumardiwakar@gmail.com
# Github  : https://github.com/NitishDiwakar
# Project : Image Manager
# Licence : MIT

$baseDir = realpath(__DIR__ . '/../uploads');

// $dir = $_POST['dir'] ?? '';
$dir = urldecode($_POST['dir'] ?? '');

/* normalize directory (important for Windows + Linux) */
$dir = str_replace(['\\', '..'], ['/', ''], $dir);

/* build full path safely */
$targetDir = $baseDir . ($dir ? DIRECTORY_SEPARATOR . $dir : '');
// 
/*echo "DIR: [" . $dir . "]<br>";
echo "TARGET: [" . $targetDir . "]<br>";
echo "REAL: [" . realpath($targetDir) . "]<br>";
exit;*/
// 
/* resolve real path */
$realTarget = realpath($targetDir);

/* Security: prevent traversal */
if ($realTarget === false || strpos($realTarget, $baseDir) !== 0) {
    die("Invalid directory");
}

if (!is_dir($realTarget)) {
    die("Directory does not exist");
}

$allowed = ['jpg','jpeg','png','gif','webp'];

foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {

    if ($_FILES['files']['error'][$i] !== 0) continue;
    if (!is_uploaded_file($tmp)) continue;

    $name = basename($_FILES['files']['name'][$i]);

    /* normalize filename */
    $name = str_replace(['\\','/'], '', $name);

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) continue;

    $dest = $realTarget . DIRECTORY_SEPARATOR . $name;

    move_uploaded_file($tmp, $dest);
}

/* redirect back */
header("Location: index.php?dir=" . urlencode($dir));
exit;