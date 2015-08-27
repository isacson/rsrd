<?php 

	require_once 'functions.php';

	$salt1 = "ble%ah*";
	$salt2 = "^a&a^";

	$username = 'me';
	$password = 'go';
	$token = hash('ripemd128', "$salt1$password$salt2");

	add_user($conn, $username, $token);

	function add_user($conn, $un, $pw)
	{
		$query = "INSERT INTO members VALUES(NULL, '$un', '$pw')";
		$result = $conn->query($query);
		if (!$result) die($conn->error);
	}

?>