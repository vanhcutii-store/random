
var player = document.getElementById('player');
  var videoSources = [
      "./1.mp4",
      "./3.mp4",
      "./4.mp4",
      "./7.mp4",
      "./26.mp4",
      "./27.mp4",
      "./37.mp4",
      "./38.mp4",
      "./55.mp4",
      "./56.mp4",
      "./57.mp4",
      "./59.mp4",
      "./60.mp4",
      "./61.mp4",
      "./62.mp4",
      "./63.mp4",
      "./64.mp4",
      "./65.mp4",
      "./66.mp4",
      "./VID_20240414_135851.mp4",
    
    ];
  var currentVideoIndex = Math.floor(Math.random() * videoSources.length);
  var isPlaying = true;

  function toggleVideo() {
    if (isPlaying) {
      player.pause();
      isPlaying = false;
    }
    else {
      player.play();
      isPlaying = true;
    }
  }
function toggleActive() {
  var videoPlayer = document.getElementById("video-player");
  videoPlayer.classList.toggle("active");
}

  function playRandomVideo() {
    var randomIndex = Math.floor(Math.random() * videoSources.length);
    while (randomIndex === currentVideoIndex) {
      randomIndex = Math.floor(Math.random() * videoSources.length);
    }
    currentVideoIndex = randomIndex;
    var randomSource = videoSources[randomIndex];
    player.src = randomSource;
    player.load();
    player.play();
  }

  player.addEventListener('play', function () {
    isPlaying = true;
  });

  player.addEventListener('pause', function () {
    isPlaying = false;
  });

  window.addEventListener('DOMContentLoaded', function () {
    player.play();
  });

  function goHome() {
    player.src = videoSources[currentVideoIndex];
    player.load();
    player.play();
  }
  window.addEventListener('DOMContentLoaded', function () {
    player.controls = false;
    player.play();
  });

        document.addEventListener("DOMContentLoaded", function() {
    var imageElement = document.createElement("img");
    var imageVietAnh = document.createElement("div");
    imageVietAnh.className = "image";
    imageVietAnh.style.position = "fixed";
    imageVietAnh.style.right = "0px";
    imageVietAnh.style.bottom = "0px";
    imageVietAnh.style.zIndex = "9999";
    var css = `
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px rgb(106, 96, 255);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgb(0, 255, 98);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #00ffbc;
        }
    `;
    var style = document.createElement("style");
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
    var mediaQuery = window.matchMedia("(max-width: 768px)");
    if (mediaQuery.matches) {
    imageVietAnh.style.display = "none";
    }
    imageElement.src = "//api.thanhdieu.com/anime-cb.php";
    imageElement.alt = "vanh";
    imageVietAnh.appendChild(imageElement);
    document.body.appendChild(imageVietAnh);
});
