<?php
# Author  : Nitish Kumar Diwakar
# Email   : nitishkumardiwakar@gmail.com
# Github  : https://github.com/NitishDiwakar
# Project : Image Manager
# Licence : MIT

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

#preview {
    position: fixed;
    display: none;
    pointer-events: none;
    border: 1px solid #444;
    background: #000;
    padding: 5px;
    z-index: 9999;
}

#preview img {
    max-width: 200px;
    max-height: 200px;
    display: block;
}
</style>
</head>

<body>

<h2>📂 /uploads/<?php echo $currentDir ?: 'root'; ?></h2>

<?php if ($currentDir): ?>
<!-- <a href="?dir=<?php // echo urlencode(dirname($currentDir)); ?>">⬅ Back</a> -->
<?php
$parent = dirname($currentDir);
if ($parent === '.') $parent = '';
?>

<a href="<?php echo $parent ? '?dir=' . urlencode($parent) : 'index.php'; ?>">⬅ Back</a>
<?php endif; ?>

<hr>


<!-- CREATE FOLDER -->
<form method="post" action="action.php">
    <input type="hidden" name="action" value="mkdir">
    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
    <input type="text" name="name" placeholder="New folder" required>
    <button>Create</button>
</form>

<!-- UPLOAD -->
<form method="post" action="upload.php" enctype="multipart/form-data">
    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
    <input type="file" name="files[]" multiple accept="image/*" required>
    <button>Upload</button>
</form>

<p>📤 You can select multiple images (Ctrl / Shift)</p>

<hr>

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
<!-- 🖼 <a href="../uploads/<?php echo ($currentDir ? $currentDir . '/' : '') . $file['name']; ?>" target="_blank">
    <?php echo $file['name']; ?>
</a> -->
<!-- Added pop up view on hover of image link -->
<?php $imgPath = "../uploads/" . ($currentDir ? $currentDir . '/' : '') . $file['name']; ?>

🖼 <a href="<?php echo $imgPath; ?>" target="_blank"
    onmouseover="showPreview(event, '<?php echo $imgPath; ?>')"
    onmousemove="movePreview(event)"
    onmouseout="hidePreview()">
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


<div id="preview"></div>

<script>
var preview = document.getElementById('preview');

function showPreview(e, src) {
    preview.innerHTML = '<img src="' + src + '">';
    preview.style.display = 'block';
    movePreview(e);
}

function movePreview(e) {
    preview.style.left = (e.clientX + 15) + 'px';
    preview.style.top = (e.clientY + 15) + 'px';
}

function hidePreview() {
    preview.style.display = 'none';
}
</script>
</body>
</html>

