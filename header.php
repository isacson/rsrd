<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";

echo <<<_END

	<title>$appname$userstr</title>
	<link rel='stylesheet' href='/styles.css' type='text/css' media='screen'>
	<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.min.css' type='text/css' media='screen'>
	<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.structure.min.css' type='text/css' media='screen'>
	<link rel='stylesheet' href='/js/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css' type='text/css' media='screen'>
	<script src='/js/jquery-2.1.4.min.js'></script>
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

?>