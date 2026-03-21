// Author  : Nitish Kumar Diwakar
// Email   : nitishkumardiwakar@gmail.com
// Github  : https://github.com/NitishDiwakar
// Project : Image Manager
// Licence : MIT

var loaded = 0;
var batch = 20;

/* LOAD IMAGES */
function loadImages() {
    var gallery = document.getElementById('gallery');

    if (loaded === 0) {
        folders.forEach(function(folder) {
            var div = document.createElement('div');
            div.className = 'folder';
            div.innerText = "📁 " + folder;

            div.onclick = function() {
                window.location = "?dir=" + (currentDir ? currentDir + "/" : "") + folder;
            };

            gallery.appendChild(div);
        });
    }

    for (var i = loaded; i < loaded + batch && i < images.length; i++) {
        var img = document.createElement('img');
        img.src = images[i];
        img.loading = "lazy";

        // ✅ ALWAYS use real index
        img.dataset.index = i;

        img.onclick = openViewer;
        gallery.appendChild(img);
    }

    loaded += batch;
}

/* SCROLL LOAD */
window.addEventListener('scroll', function() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var windowHeight = window.innerHeight;
    var docHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= docHeight - 150) {
        loadImages();
    }
});

/* VIEWER */
var current = 0;

function openViewer() {
    current = parseInt(this.dataset.index);
    showImage();
    document.getElementById('viewer').style.display = 'flex';
}

function showImage() {
    document.getElementById('viewer-img').src = images[current];

    // ✅ preload next & prev
    new Image().src = images[(current + 1) % images.length];
    new Image().src = images[(current - 1 + images.length) % images.length];
}

function closeViewer() {
    document.getElementById('viewer').style.display = 'none';
}

/* KEYBOARD */
document.addEventListener('keydown', function(e) {
    if (document.getElementById('viewer').style.display !== 'flex') return;

    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'Escape') closeViewer();
});

/* TAP NAVIGATION (clean, single handler) */
var viewer = document.getElementById('viewer');

viewer.addEventListener('click', function(e) {

    if (e.target.id === 'close') return;

    var rect = viewer.getBoundingClientRect();
    var x = e.clientX - rect.left;
    var width = rect.width;

    var leftZone = width * 0.3;
    var rightZone = width * 0.7;

    if (x < leftZone) {
        prevImage();
    }
    else if (x > rightZone) {
        nextImage();
    }
});

/* ARROWS */
document.getElementById('nav-left').onclick = prevImage;
document.getElementById('nav-right').onclick = nextImage;

function nextImage() {
    current = (current + 1) % images.length;
    showImage();
}

function prevImage() {
    current = (current - 1 + images.length) % images.length;
    showImage();
}

/* CLOSE */
document.getElementById('close').onclick = closeViewer;

/* INIT */
loadImages();

/* FILL SCREEN (tablet fix) */
setTimeout(function () {
    while (document.documentElement.scrollHeight <= window.innerHeight && loaded < images.length) {
        loadImages();
    }
}, 100);

/* BACK */
function goBack() {
    if (!currentDir) return;

    var parts = currentDir.split('/');
    parts.pop();
    var newDir = parts.join('/');

    window.location = newDir ? "?dir=" + newDir : "index.php";
}