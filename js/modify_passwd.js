var passwd = document.getElementsByClassName('passwd_modify');
var login = document.getElementsByClassName('login_modify');
var mail = document.getElementsByClassName('mail_modify');


function show_passwd_modify() {
  if (passwd[0].style.display == 'block') {
    passwd[0].style.display = 'none';
  }
  else
    passwd[0].style.display = 'block';
}

function show_login_modify() {
  if (login[0].style.display == 'block') {
    login[0].style.display = 'none';
  }
  else
    login[0].style.display = 'block';
}

function show_mail_modify() {
  if (mail[0].style.display == 'block') {
    mail[0].style.display = 'none';
  }
  else
    mail[0].style.display = 'block';
}
