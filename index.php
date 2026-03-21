<?php
# Author  : Nitish Kumar Diwakar
# Email   : nitishkumardiwakar@gmail.com
# Github  : https://github.com/NitishDiwakar
# Project : Image Manager
# Licence : MIT

$baseDir = __DIR__ . '/uploads';
$urlBase = 'uploads';

$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '';
$dirPath = realpath($baseDir . '/' . $currentDir);

/* Security */
if ($dirPath === false || strpos($dirPath, realpath($baseDir)) !== 0) {
    $dirPath = realpath($baseDir);
    $currentDir = '';
}

$items = scandir($dirPath);

$folders = [];
$images = [];

$allowed = ['jpg','jpeg','png','gif','webp'];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;

    $fullPath = $dirPath . '/' . $item;

    if (is_dir($fullPath)) {
        $folders[] = $item;
    } else {
        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $images[] = [
                'path' => $urlBase . '/' . ($currentDir ? $currentDir . '/' : '') . $item,
                'time' => filemtime($fullPath)
            ];
        }
    }
}

/*  strict latest first */
usort($images, function($a, $b) {
    return $b['time'] <=> $a['time'];
});

/* clean array */
$images = array_values(array_map(function($x){
    return $x['path'];
}, $images));

sort($folders);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Image Viewer</title>

<link rel="stylesheet" href="styles.css">
</head>

<body>

<?php if ($currentDir): ?>
<div id="topbar">
    <button onclick="goBack()">⬅ Back</button>
</div>
<?php endif; ?>

<div id="gallery"></div>

<div id="viewer">
    <div id="close">&times;</div>
    <div id="nav-left">&#10094;</div>
    <img id="viewer-img">
    <div id="nav-right">&#10095;</div>
</div>

<script>
var images = <?php echo json_encode($images); ?>;
var folders = <?php echo json_encode($folders); ?>;
var currentDir = "<?php echo $currentDir; ?>";
</script>

<script src="script.js?v=final"></script>

</body>
</html>