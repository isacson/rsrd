<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

if ((isset($_GET['val']) && $_GET['val'] != "") && (isset($_GET['t']) && $_GET['t'] != "") && (isset($_GET['v']) && $_GET['v'] != "")) {
	$query = "SELECT COUNT(*) num FROM $_GET[t] WHERE $_GET[val] = '$_GET[v]';";
}

$stmt = $pdo->prepare($query);
$result = $stmt->execute();

if(!$result = $pdo->query($query))	{
	die('There was an error running the items query [' . $pdo->error . ']');
}

$row = $result->fetch(PDO::FETCH_ASSOC);

echo "$row[num]";

$pdo = null;

?>