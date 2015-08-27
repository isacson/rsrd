<?php

	$dbhost = "localhost";
	$dbname = "YOUR DATABASE NAME";
	$dbuser = "YOUR USERNAME";
	$dbpass = "YOUR PASSWORD";
	$appname = "WOLA Regional Security Research Database";
	$thisdomain = "www.defenseassistance.org";

try {

	$pdo = new PDO('mysql:host=localhost;dbname=YOUR DATABASE NAME', $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec('SET NAMES "utf8"');
}

catch (PDOException $e) {

	echo "Unable to connect to the database server.";
	exit();
}

	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($conn->connect_error) die($conn->connect_error);

function sanitizeString($var) {
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
//	$var = str_replace("'","\\\'", $var);
	$var = str_replace("\"","\\\"", $var);
	return $var;
}

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

?>