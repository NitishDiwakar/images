<?php
$baseDir = realpath(__DIR__ . '/../uploads');

$action = $_POST['action'] ?? '';

function safePath($base, $path) {
    $full = realpath($base . '/' . $path);
    if ($full === false || strpos($full, $base) !== 0) return false;
    return $full;
}

/* CREATE FOLDER */
if ($action === 'mkdir') {
    $dir = $_POST['dir'];
    $name = basename($_POST['name']);

    $path = $baseDir . '/' . ($dir ? $dir . '/' : '') . $name;
    mkdir($path);
}

/* DELETE */
if ($action === 'delete') {
    $path = $_POST['path'];
    $full = safePath($baseDir, $path);

    if ($full) {
        if (is_dir($full)) {
            rmdir($full); // only empty
        } else {
            unlink($full);
        }
    }
}

/* RENAME */
if ($action === 'rename') {
    $path = $_POST['path'];
    $newname = basename($_POST['newname']);

    $full = safePath($baseDir, $path);

    if ($full && $newname) {
        $newPath = dirname($full) . '/' . $newname;
        rename($full, $newPath);
    }
}

/* redirect back */
$redirectDir = $_POST['dir'] ?? dirname($_POST['path'] ?? '');
header("Location: index.php?dir=" . urlencode($redirectDir));
