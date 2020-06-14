var element = document.getElementsByClassName('menu');

function open_menu() {
  if (element[0].style.display == 'block') {
    element[0].style.display = 'none';
  }
  else
    element[0].style.display = 'block';
}
