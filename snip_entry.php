<?php 

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";



if ($loggedin !== true) {
	echo "<h3>We're sorry</h3>";
	echo "<p>You must be logged in to use this page. If you've been given a username and password, click \"Login\" on the menu above. (<a href='/index.php'>Go back to the Data Clips page</a></p>";
	exit();
}
else {

	include "snip_entry_form.php";

}



?>