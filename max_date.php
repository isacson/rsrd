<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

$query = "SELECT date FROM filename ORDER BY date DESC LIMIT 1;";

$stmt = $pdo->prepare($query);
$result = $stmt->execute();

if(!$result = $pdo->query($query))	{
	die('There was an error running the query [' . $pdo->error . ']');
}

$row = $result->fetch(PDO::FETCH_ASSOC);

if ($row['date'] == "") {
	echo "0000-00-00";
}
else {
	echo "$row[date]";
}

$pdo = null;

?>