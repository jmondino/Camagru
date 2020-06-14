function display_big(img) {
  document.getElementById("big_img").src = img;
  document.getElementById("all2").style.display = "block";
}

function add_like(i) {
  $.ajax({
    method: 'POST',
    url: '/camagru/php/like_ajax.php',
    data: 'like=' + i,
    success: function(yes) {
      console.log(yes);
    }
  });
  name = '#like' + i;
  name2 = '#thumb' + i;
  name3 = '#nb_like' + i;
  var elm = document.querySelector(name);
  var thumb = document.querySelector(name2);
  var tab = document.querySelector(name3).innerHTML.split(" ");
  var like = tab[0];
  if (elm.className == 'like blue')
  {
    elm.classList.remove("blue");
    thumb.classList.remove("blue");
    like--;
  }
  else
  {
    elm.classList.add("blue");
    thumb.classList.add("blue");
    like++;
  }
  document.querySelector(name3).innerHTML = like + ' j\'aime';
}

function add_comment(i, login) {
  name = '#comment' + i;
  name2 = '#nb_com' + i;
  name3 = '#comment_place' + i;
  pic_id = i;
  comment = document.querySelector(name).value;
  comment = comment;
  $.ajax({
    method: 'POST',
    url: '/camagru/php/comment_ajax.php',
    data: {pic_id, comment},
    success: function(yes) {
      console.log(yes);
    }
  });
  document.querySelector(name).value = "";
  var tab = document.querySelector(name2).innerHTML.split(" ");
  var nb_com = tab[0];
  nb_com++;
  if (nb_com < 2)
    document.querySelector(name2).innerHTML = nb_com + ' commentaire';
  else
    document.querySelector(name2).innerHTML = nb_com + ' commentaires';
  location.reload(false);
}

function show_error(i, pic_id) {
  name = '#comment' + pic_id;
  if (i == 1)
  {
    document.getElementById("must_connected_like").style.display = "flex";
    $("#must_connected_like").fadeOut(5000);
  }
  else
  {
    document.getElementById("must_connected_comment").style.display = "flex";
    $("#must_connected_comment").fadeOut(5000);
    document.querySelector(name).value = "";
  }
}
