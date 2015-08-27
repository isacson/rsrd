<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';


$formtext = "<div id='thelogintext'>Sorry, that was wrong. You can try the form again, or just <a href='/'>use the public version of the site</a>.</div><form id='theloginform' action='login.php' method='post'><label>Login: </label><input type='text' name='login' id='login'><br><label>Password: </label><input type='password' name='password' id='password'><br><input type='submit' value='submit'></form>";

$un_temp = mysql_entities_fix_string($conn, $_POST['login']);
$pw_temp = mysql_entities_fix_string($conn, $_POST['password']);

$query = "SELECT * from members WHERE username = '$un_temp'";
$result = $conn->query($query);

if (!$result) die($conn->error);

elseif ($result->num_rows) {
	$row = $result->fetch_array(MYSQLI_NUM);

	$result->close();

	$salt1 = "YOURSALT";
	$salt2 = "ALSOYOURSALT";
	$token = hash('ripemd128', "$salt1$pw_temp$salt2");

	if ($token == $row[2]) {
		session_start();
		$_SESSION['username'] = $un_temp;
		$_SESSION['password'] = $pw_temp;
		header('Location: /');
	}
	else {

		echo <<<_END

			<title>$appname$userstr</title>
			<link rel='stylesheet' href='/styles.css' type='text/css' media='screen'>
			<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.min.css' type='text/css' media='screen'>
			<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.structure.min.css' type='text/css' media='screen'>
			<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css' type='text/css' media='screen'>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
			<script src='/js/jquery-ui-1.11.4.custom/jquery-ui.min.js'></script>
			</head><body>
			<?php include_once("analyticstracking.php") ?>
			<header><div style='font-size:0.75em; color:red; margin-top:0.25em'>You have found a site that is under construction. For now, you will find many bugs and little information.</div>

_END;

		if ($loggedin == TRUE) {
			echo "<div id='logged_in'>You are logged in and have full access to the site.</div>";
			$button_query = "SELECT * FROM buttons WHERE button_name != 'Login'";
		}
		else {
			$button_query = "SELECT * FROM buttons WHERE is_private IS NULL";
		}

		$msq = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if($msq->connect_errno > 0){
		    die('Unable to connect to database [' . $msq->connect_error . ']');
		}
		if(!$result = $msq->query($button_query)){
		    die('There was an error running the buttons query [' . $msq->error . ']');
		}

		echo "<h2>$appname</h2><div id='header_menu'>";

		// PUT HEADER MENUS HERE

		$header_menu_width = (900-$result->num_rows)/$result->num_rows;

		while($row = $result->fetch_assoc()) {
			echo "<div class='header_button' style='width:$header_menu_width";
			echo "px;' ";

			if( $row['button_name'] == "Login") {
				echo "id='logg' ";
			}

			if( $row['button_name'] != "Login") {
				if($row['button_name'] == "SAM LatAm" && $loggedin == FALSE) {
					$row['button_name'] = "SAM Latin America";
				}
				echo "title='" . $row['title_text'] . "' ";
				echo "onclick='" . $row['button_code'] . "'>" . $row['button_name'] . "</div>";
			}
			else {
				echo ">" . $row['button_name'] . "</div>";
			}
		}

		echo "</div>";

		echo "<div id='loginform'><br></div>";

		echo "</header>";

		echo "$formtext";
	}
}
else {

	echo <<<_END

		<title>$appname$userstr</title>
		<link rel='stylesheet' href='/styles.css' type='text/css' media='screen'>
		<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.min.css' type='text/css' media='screen'>
		<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.structure.min.css' type='text/css' media='screen'>
		<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css' type='text/css' media='screen'>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src='/js/jquery-ui-1.11.4.custom/jquery-ui.min.js'></script>
		</head><body>
		<?php include_once("analyticstracking.php") ?>
		<header><div style='font-size:0.75em; color:red; margin-top:0.25em'>You have found a site that is under construction. For now, you will find many bugs and little information.</div>

_END;

	if ($loggedin == TRUE) {
		echo "<div id='logged_in'>You are logged in and have full access to the site.</div>";
		$button_query = "SELECT * FROM buttons WHERE button_name != 'Login'";
	}
	else {
		$button_query = "SELECT * FROM buttons WHERE is_private IS NULL";
	}

	$msq = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if($msq->connect_errno > 0){
	    die('Unable to connect to database [' . $msq->connect_error . ']');
	}
	if(!$result = $msq->query($button_query)){
	    die('There was an error running the buttons query [' . $msq->error . ']');
	}

	echo "<h2>$appname</h2><div id='header_menu'>";

	// PUT HEADER MENUS HERE

	$header_menu_width = (900-$result->num_rows)/$result->num_rows;

	while($row = $result->fetch_assoc()) {
		echo "<div class='header_button' style='width:$header_menu_width";
		echo "px;' ";

		if( $row['button_name'] == "Login") {
			echo "id='logg' ";
		}

		if( $row['button_name'] != "Login") {
			if($row['button_name'] == "SAM LatAm" && $loggedin == FALSE) {
				$row['button_name'] = "SAM Latin America";
			}
			echo "title='" . $row['title_text'] . "' ";
			echo "onclick='" . $row['button_code'] . "'>" . $row['button_name'] . "</div>";
		}
		else {
			echo ">" . $row['button_name'] . "</div>";
		}
	}

	echo "</div>";

	echo "<div id='loginform'><br></div>";

	echo "</header>";
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