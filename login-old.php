<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/header.php';

$formtext = "<div id='thelogintext'>Sorry, that was wrong. You can try the form again, or just <a href='/'>use the public version of the site</a>.</div><form id='theloginform' action='login.php' method='post'><label>Login: </label><input type='text' name='login' id='login'><br><label>Password: </label><input type='password' name='password' id='password'><br><input type='submit' value='submit'></form>";

$un_temp = mysql_entities_fix_string($conn, $_POST['login']);
$pw_temp = mysql_entities_fix_string($conn, $_POST['password']);

$query = "SELECT * from members WHERE username = '$un_temp'";
$result = $conn->query($query);

if (!$result) die($conn->error);

elseif ($result->num_rows) {
	$row = $result->fetch_array(MYSQLI_NUM);

	$result->close();

	$salt1 = "ble%ah*";
	$salt2 = "^a&a^";
	$token = hash('ripemd128', "$salt1$pw_temp$salt2");

	if ($token == $row[2]) {
		session_start();
		$_SESSION['username'] = $un_temp;
		$_SESSION['password'] = $pw_temp;
		echo "<script>window.open('/');close();</script>";
//		header('Location: /');
	}
	else {
		echo "$formtext";
	}
}
else {
	echo "$formtext";
}

require_once $_SERVER["DOCUMENT_ROOT"] . '/footer.php';

$conn->close();

function mysql_entities_fix_string($conn, $string)
{
	return htmlentities(mysql_fix_string($conn, $string));
}

function mysql_fix_string($conn, $string)

{
	if (get_magic_quotes_gpc()) $string = stripslashes($string);
	return $conn->real_escape_string($string);
}

?>