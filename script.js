// Author  : Nitish Kumar Diwakar
// Email   : nitishkumardiwakar@gmail.com
// Github  : https://github.com/NitishDiwakar
// Project : Image Manager
// Licence : MIT

var loaded = 0;
var batch = 20;

function loadImages() {
    var gallery = document.getElementById('gallery');

    /* show folders only once */
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
        img.dataset.index = i;
        img.onclick = openViewer;
        gallery.appendChild(img);
    }

    loaded += batch;
}

/* Infinite scroll */
/*window.onscroll = function() {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
        loadImages();
    }
};*/

// Author  : Nitish Kumar Diwakar
// Email   : nitishkumardiwakar@gmail.com
// Github  : https://github.com/NitishDiwakar
// Project : Image Manager
// Licence : MIT

var loaded = 0;
var batch = 20;

function loadImages() {
    var gallery = document.getElementById('gallery');

    /* show folders only once */
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
        img.dataset.index = i;
        img.onclick = openViewer;
        gallery.appendChild(img);
    }

    loaded += batch;
}

/* Infinite scroll */
window.onscroll = function() {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
        loadImages();
    }
};

/* Viewer */
var current = 0;

function openViewer() {
    current = parseInt(this.dataset.index);
    showImage();
    document.getElementById('viewer').style.display = 'flex';
}

function showImage() {
    document.getElementById('viewer-img').src = images[current];

    // preload next & prev
    var next = new Image();
    next.src = images[(current + 1) % images.length];

    var prev = new Image();
    prev.src = images[(current - 1 + images.length) % images.length];
}

function closeViewer() {
    document.getElementById('viewer').style.display = 'none';
}

/* Keyboard */
document.addEventListener('keydown', function(e) {
    if (document.getElementById('viewer').style.display !== 'flex') return;

    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'Escape') closeViewer();
});

/* Swipe */
/*var startX = 0;

document.getElementById('viewer').addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
});

document.getElementById('viewer').addEventListener('touchend', function(e) {
    var endX = e.changedTouches[0].clientX;

    if (startX - endX > 50) nextImage();
    if (endX - startX > 50) prevImage();
});*/

var startX = 0;
var endX = 0;

var viewer = document.getElementById('viewer');

viewer.addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
});

viewer.addEventListener('touchmove', function(e) {
    endX = e.touches[0].clientX;
});

viewer.addEventListener('touchend', function() {
    var diff = startX - endX;

    if (Math.abs(diff) > 50) {
        if (diff > 0) {
            nextImage();   // swipe left → next
        } else {
            prevImage();   // swipe right → prev
        }
    }

    // reset
    startX = 0;
    endX = 0;
});


/* Arrows */
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

/* Close */
document.getElementById('close').onclick = closeViewer;

/* Init */
loadImages();

/* ensure enough images load on tablet */
setTimeout(function () {
    while (document.documentElement.scrollHeight <= window.innerHeight && loaded < images.length) {
        loadImages();
    }
}, 100);

/* Back button */
function goBack() {
    if (!currentDir) return;

    var parts = currentDir.split('/');
    parts.pop();
    var newDir = parts.join('/');

    window.location = newDir ? "?dir=" + newDir : "index.php";
}



/* Viewer */
var current = 0;

function openViewer() {
    current = parseInt(this.dataset.index);
    showImage();
    document.getElementById('viewer').style.display = 'flex';
}

function showImage() {
    document.getElementById('viewer-img').src = images[current];

    // preload next & prev
    var next = new Image();
    next.src = images[(current + 1) % images.length];

    var prev = new Image();
    prev.src = images[(current - 1 + images.length) % images.length];
}

function closeViewer() {
    document.getElementById('viewer').style.display = 'none';
}

/* Keyboard */
document.addEventListener('keydown', function(e) {
    if (document.getElementById('viewer').style.display !== 'flex') return;

    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'Escape') closeViewer();
});

/* Swipe */
var startX = 0;

document.getElementById('viewer').addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
});

document.getElementById('viewer').addEventListener('touchend', function(e) {
    var endX = e.changedTouches[0].clientX;

    if (startX - endX > 50) nextImage();
    if (endX - startX > 50) prevImage();
});

/* Arrows */
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

/* Close */
document.getElementById('close').onclick = closeViewer;

/* Init */
loadImages();

/* Back button */
function goBack() {
    if (!currentDir) return;

    var parts = currentDir.split('/');
    parts.pop();
    var newDir = parts.join('/');

    window.location = newDir ? "?dir=" + newDir : "index.php";
}
