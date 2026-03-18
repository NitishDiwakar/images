<?php
$baseDir = realpath(__DIR__ . '/../uploads');

$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '';
$dirPath = realpath($baseDir . '/' . $currentDir);

/* security */
if ($dirPath === false || strpos($dirPath, $baseDir) !== 0) {
    $dirPath = $baseDir;
    $currentDir = '';
}

$items = scandir($dirPath);

/* separate folders + files */
$folders = [];
$files = [];

// foreach ($items as $item) {
//     if ($item === '.' || $item === '..') continue;

foreach ($items as $item) {
    if ($item === '.' || $item === '..' || $item[0] === '.') continue;


    $full = $dirPath . '/' . $item;

    if (is_dir($full)) {
        $folders[] = $item;
    } else {
        $files[] = [
            'name' => $item,
            'time' => filemtime($full)
        ];
    }
}

/* sort files latest first */
usort($files, function($a, $b) {
    return $b['time'] - $a['time'];
});
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Files</title>

<style>
body { font-family:sans-serif; background:#111; color:#fff; }
a { color:#0af; text-decoration:none; }
.item { padding:8px; border-bottom:1px solid #333; }
form { display:inline; margin-left:10px; }
input { background:#222; color:#fff; border:1px solid #444; }
button { cursor:pointer; }
</style>
</head>

<body>

<h2>📂 /uploads/<?php echo $currentDir ?: 'root'; ?></h2>

<?php if ($currentDir): ?>
<a href="?dir=<?php echo urlencode(dirname($currentDir)); ?>">⬅ Back</a>
<?php endif; ?>

<hr>

<!-- create folder -->
<form method="post" action="action.php">
    <input type="hidden" name="action" value="mkdir">
    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
    <input type="text" name="name" placeholder="New folder" required>
    <button>Create</button>
</form>

<!-- upload -->
<form method="post" action="upload.php" enctype="multipart/form-data">
    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
    <input type="file" name="files[]" multiple accept="image/*" required>
    <button>Upload</button>
</form>

<p>📤 You can select multiple images (Ctrl / Shift)</p>

<hr>

<!-- FOLDERS FIRST -->
<?php foreach ($folders as $folder): 
    $rel = ($currentDir ? $currentDir . '/' : '') . $folder;
?>
<div class="item">
    📁 <a href="?dir=<?php echo urlencode($rel); ?>"><?php echo $folder; ?></a>

    <!-- rename -->
    <form method="post" action="action.php">
        <input type="hidden" name="action" value="rename">
        <input type="hidden" name="path" value="<?php echo htmlspecialchars($rel); ?>">
        <input type="text" name="newname" placeholder="Rename">
        <button>✏️</button>
    </form>

    <!-- delete -->
    <form method="post" action="action.php">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="path" value="<?php echo htmlspecialchars($rel); ?>">
        <button onclick="return confirm('Delete folder?')">❌</button>
    </form>
</div>
<?php endforeach; ?>


<!-- FILES AFTER -->
<?php foreach ($files as $file): 
    $rel = ($currentDir ? $currentDir . '/' : '') . $file['name'];
?>
<div class="item">
   <!-- 🖼  --> <?php // echo $file['name']; ?>
🖼 <a href="../uploads/<?php echo ($currentDir ? $currentDir . '/' : '') . $file['name']; ?>" target="_blank">
    <?php echo $file['name']; ?>
</a>
    <!-- rename -->
    <form method="post" action="action.php">
        <input type="hidden" name="action" value="rename">
        <input type="hidden" name="path" value="<?php echo htmlspecialchars($rel); ?>">
        <input type="text" name="newname" placeholder="Rename">
        <button>✏️</button>
    </form>

    <!-- delete -->
    <form method="post" action="action.php">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="path" value="<?php echo htmlspecialchars($rel); ?>">
        <button onclick="return confirm('Delete file?')">❌</button>
    </form>
</div>
<?php endforeach; ?>

</body>
</html>
