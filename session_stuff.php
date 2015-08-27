<?php

session_start();

if (isset($_SESSION['username'])) {
	$user		= $_SESSION['username'];
	$loggedin	= TRUE;
	$userstr	= " ($user)";
}
else {
	$loggedin = FALSE;
	$userstr  = " (guest user)";
}

?>