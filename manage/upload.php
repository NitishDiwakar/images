<?php
# Author  : Nitish Kumar Diwakar
# Email   : nitishkumardiwakar@gmail.com
# Github  : https://github.com/NitishDiwakar
# Project : Image Manager
# Licence : MIT

$baseDir = realpath(__DIR__ . '/../uploads');

$dir = $_POST['dir'] ?? '';
$targetDir = realpath($baseDir . '/' . $dir);

if ($targetDir === false || strpos($targetDir, $baseDir) !== 0) {
    die("Invalid directory");
}

$allowed = ['jpg','jpeg','png','gif','webp'];

foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {

    if ($_FILES['files']['error'][$i] !== 0) continue;
    if (!is_uploaded_file($tmp)) continue;

    $name = basename($_FILES['files']['name'][$i]);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) continue;

    move_uploaded_file($tmp, $targetDir . '/' . $name);
}

/* redirect back */
header("Location: index.php?dir=" . urlencode($dir));
