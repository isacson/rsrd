<?php 

require_once 'functions.php';

if (isset($_SERVER['PHP_AUTH_USER']) &&
	isset ($_SERVER['PHP_AUTH_PW']))
{
	$un_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
	$pw_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);

	$query = "SELECT * from members WHERE username = '$un_temp'";
	$result = $conn->query($query);

	if (!$result) die($conn->error);
	elseif ($result->num_rows)
	{
		$row = $result->fetch_array(MYSQLI_NUM);

		$result->close();

		$salt1 = "ble%ah*";

		$salt2 = "^a&a^";

		$token = hash('ripemd128', "$salt1$pw_temp$salt2");

		if ($token == $row[2])
		{
			session_start();
			$_SESSION['username'] = $un_temp;
			$_SESSION['password'] = $pw_temp;
			$safe = 1;
		}
		else
		{
			$safe = 0;
		}
	}
	else
	{
		$safe = 0;
	}
}
else
{
	header('WWW-Authenticate: Basic realm="If you have access to the private areas of this site, enter your password now."');
	$safe = 0;
}

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