var my_input = document.getElementById("")

function validateForm () {


}






make_query("INSERT INTO users (firstname, lastname, email, password, login, birth, age) VALUES (\"$firstname\", \"$lastname\", \"$mail\", \"$passwd\", \"$login\", \"$birthDate\", \"$age\")");
header("location: ../index.php");
