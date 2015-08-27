<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

if ((isset($_GET['f']) && $_GET['f'] != "") && (isset($_GET['t']) && $_GET['t'] != "") && (isset($_GET['s']) && $_GET['s'] != "") && (isset($_GET['v']) && $_GET['v'] != "")) {

	$query = "SELECT $_GET[f] field FROM $_GET[t] WHERE $_GET[s] LIKE '$_GET[v]%';";
	$stmt = $pdo->prepare($query);
	$result = $stmt->execute();

	if(!$result = $pdo->query($query))	{
		die('There was an error running the query [' . $pdo->error . ']');
	}

	$row = $result->fetch(PDO::FETCH_ASSOC);
	$n = $stmt->fetchAll();
	$cnt = count($n);

	if ($cnt < 1) {
		echo "$_GET[v]";
	}
	else {
		echo "sorry";
	}

	$pdo = null;

}

?>