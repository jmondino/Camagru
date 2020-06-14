
function send_data(filter) {
  if (filter == 'aucun')
  {
    document.querySelector('#filtre_photo').removeAttribute('src');
    document.querySelector('#filtre_photo').removeAttribute('width');
    document.querySelector('#filtre_photo').removeAttribute('height');
    // document.querySelector('#filtre_photo').style.display = "none";
  }
  else {
    document.querySelector('#filtre_photo').src = "ressources/filtres/" + filter + ".png";
    document.querySelector('#filtre_photo').style.display = "block";
  }
  set_width();
}

function set_width() {
  var img = document.querySelector('#img_photo');
  var width = img.clientWidth;
  var height = img.clientHeight;
  var filtre_src = document.querySelector('#filtre_photo').src;

  if (filtre_src)
  {
    var filtre = document.querySelector('#filtre_photo');
    filtre.width = width;
    filtre.height = height;
  }
}

(function() {

  var streaming = false,
      video        = document.querySelector('#video'),
      cover        = document.querySelector('#cover'),
      canvas       = document.querySelector('#canvas'),
      photo        = document.querySelector('#img_photo'),
      startbutton  = document.querySelector('#startbutton'),
      width = 320,
      height = 0;

  navigator.getMedia = ( navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia ||
                         navigator.msGetUserMedia);

  navigator.getMedia(
    {
      video: true,
      audio: false
    },
    function(stream) {
      if (navigator.mozGetUserMedia) {
        video.srcObject = stream;
      } else {
        var vendorURL = window.URL || window.webkitURL;
        video.srcObject = stream;
      }
      video.play();
    },
    function(err) {
      console.log("An error occured! " + err);
    }
  );

  video.addEventListener('canplay', function(ev){
    if (!streaming) {
      height = video.videoHeight / (video.videoWidth/width);
      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      streaming = true;
    }
  }, false);

  function takepicture() {
    canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(video, 0, 0, width, height);
    var webcam_data = canvas.toDataURL('image/png');
    photo.setAttribute('src', webcam_data);
    set_width();
    $.ajax({
      method: 'POST',
      url: '/camagru/php/save_webcam.php',
      data: 'webcam_data=' + webcam_data,
      success: function(webcam_data) {
        console.log(webcam_data);
      }
    });
  }
  startbutton.addEventListener('click', function(ev){
      takepicture();
    ev.preventDefault();
  }, false);
})();
