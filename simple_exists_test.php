<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

if ((isset($_GET['f']) && $_GET['f'] != "") && (isset($_GET['v']) && $_GET['v'] != "") && (!isset($_GET['t']) || $_GET['t'] == "")) {

	$query = "SELECT COUNT(*) AS field FROM $_GET[f] WHERE $_GET[f] = '$_GET[v]';";

	$stmt = $pdo->prepare($query);
	$result = $stmt->execute();

	if(!$result = $pdo->query($query))	{
		die('There was an error running the query [' . $pdo->error . ']');
	}

	$row = $result->fetch(PDO::FETCH_ASSOC);

	if ($row['field'] < 1) {
		echo "0";
	}
	else {
		echo "$row[field]";
	}

	$pdo = null;

}

if ((isset($_GET['f']) && $_GET['f'] != "") && (isset($_GET['t']) && $_GET['t'] != "") && (isset($_GET['v']) && $_GET['v'] != "")) {

	$query = "SELECT COUNT(*) AS field FROM $_GET[t] WHERE $_GET[f] LIKE '%" . $_GET['v']. "%';";

	$stmt = $pdo->prepare($query);
	$result = $stmt->execute();

	if(!$result = $pdo->query($query))	{
		die('There was an error running the query [' . $pdo->error . ']');
	}

	$row = $result->fetch(PDO::FETCH_ASSOC);

	if ($row['field'] < 1) {
		echo "0";
	}
	else {
		echo "$row[field]";
	}

	$pdo = null;

}

?>