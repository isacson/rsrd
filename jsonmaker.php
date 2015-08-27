<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

if (isset($_GET['val']) && $_GET['val'] != "") {
	$formblank = $_GET['val'];
	$query = doquery($_GET['val']);
}

if ((isset($_GET['val']) && $_GET['val'] != "") && (isset($_GET['t']) && $_GET['t'] != "")) {
	$formblank = $_GET['val'];
	$query = doubleQuery($_GET['val'], $_GET['t']);
}

function doquery($d) {

	return("SELECT DISTINCT $d FROM $d ORDER BY $d");
}

function doubleQuery($d,$t) {

	return("SELECT DISTINCT $d FROM $t ORDER BY $d");
}

$stmt = $pdo->prepare($query);
$result = $stmt->execute();

if(!$result = $pdo->query($query))	{
	die('There was an error running the items query [' . $pdo->error . ']');
}

$row = $result->fetch(PDO::FETCH_ASSOC);
$rows = $stmt->fetchAll();
$cnt = count($rows);
$j=1;

echo "[\n";

do { 	
	$aformblank = stripslashes($row[$formblank]);
	$aformblank = str_replace("\"", "\\\"", $aformblank);

	echo "\"$aformblank\"";

	if ($j < $cnt) {
		echo ",\n";
	}
	$j++;
}
while($row = $result->fetch(PDO::FETCH_ASSOC));

echo "\n]";

$pdo = null;

?>