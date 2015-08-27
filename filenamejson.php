<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

if (isset($_GET['f']) && $_GET['f'] != "") {
	$query = doQuery($_GET['f']);
}
else {
	exit();
}

function doQuery($f) {

	return("SELECT cite_title, publisher, url, date, city, prisec, language, is_audio, read_it, report_name, report_desc, law, section, report_due FROM filename
		LEFT JOIN report ON filename.filename_key = report.filename_key 
		LEFT JOIN report_name ON report_name.report_name_key = report.report_name_key 
		LEFT JOIN law ON law.law_key = report.law_key 
		LEFT JOIN filename_publisher ON filename.filename_key = filename_publisher.filename_key 
		LEFT JOIN publisher ON publisher.publisher_key = filename_publisher.publisher_key 

		WHERE filename = '$f';");
}

$stmt = $pdo->prepare($query);
$result = $stmt->execute();

if(!$result = $pdo->query($query))	{
	die('There was an error running the items query [' . $pdo->error . ']');
}

$row = $result->fetch(PDO::FETCH_ASSOC);

$author_query = "SELECT DISTINCT author FROM filename
				LEFT JOIN filename_author ON filename.filename_key = filename_author.filename_key 
				LEFT JOIN author ON author.author_key = filename_author.author_key
				where filename = '$_GET[f]'";

$author_stmt = $pdo->prepare($author_query);
$author_result = $author_stmt->execute();

if(!$author_result = $pdo->query($author_query))	{
	die('There was an error running the author query [' . $pdo->error . ']');
}

$author_row = $author_result->fetch(PDO::FETCH_ASSOC);

echo "{ ";

$authors = [];

if ($author_row != "") {

	foreach ($author_row as $key => $value) {
		$authors[] = $value;
	}

	$author = implode(", ",$authors);
}
else {
	$author = "";
}

if ($row != "") {

	foreach ($row as $key => $value) {
		$value = stripslashes($value);

		echo " \"$key\" : \"$value\", ";
	}
}

echo " \"author\" : \"$author\"";

echo " }";

$pdo = null;

?>