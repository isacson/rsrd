<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";

//$country = $program = $tag = $agency = "";

$gets = [];

if (isset($_POST['country']) && ($_POST['country'] != "")) {

	$gets['country'] = $_POST['country'];
}

if (isset($_POST['program']) && ($_POST['program'] != "")) {

	$gets['program'] = $_POST['program'];
}

if (isset($_POST['tag']) && ($_POST['tag'] != "")) {

	$gets['tag'] = $_POST['tag'];
}

if (isset($_POST['agency']) && ($_POST['agency'] != "")) {

	$gets['agency'] = $_POST['agency'];
}

if (isset($_POST['data_enterer']) && ($_POST['data_enterer'] != "")) {

	$gets['data_enterer'] = $_POST['data_enterer'];
}

if (isset($_POST['prisec']) && ($_POST['prisec'] != "")) {

	$gets['prisec'] = $_POST['prisec'];
}

if (isset($_POST['language']) && ($_POST['language'] != "")) {

	$gets['language'] = $_POST['language'];
}

if (isset($_POST['important']) && ($_POST['important'] == "1")) {

	$gets['important'] = $_POST['important'];
}

if (count($gets) > 0) {

	$wheregets = " WHERE ";
}
else {
	$wheregets = "";
}

$i = 1;

foreach ($gets as $key => $value) {
	
	$wheregets .= " $key = \"$value\" ";
	if (count($gets) > $i) {
		$wheregets .= " AND ";
	}
	$i++;
}

if (isset($_POST['is_audio']) && $_POST['is_audio'] != "") {

	if ($wheregets == "") {
		$wheregets .= " WHERE ";
	}
	else {
		$wheregets .= " AND ";
	}

	$wheregets .= "( is_audio = $_POST[is_audio] ";
	
	if (isset($_POST['is_image']) && $_POST['is_image'] == 2 ) {
		$wheregets .= " OR quote LIKE \"Data is an image%\") ";
	}
	else {
		$wheregets .= " AND quote NOT LIKE \"Data is an image%\") ";	
	}
}
else {
	if (isset($_POST['is_image']) && $_POST['is_image'] == 2 ) {

		if ($wheregets != "") {
			if(isset($_POST['ping']) && $_POST['ping'] == 1) {
				$wheregets .= "";
			} 
			else {
				$wheregets .= " AND quote LIKE \"Data is an image%\"";
			}
		}
		else {
			if(isset($_POST['ping']) && $_POST['ping'] == 1) {
				$wheregets .= "";
			}
			else {
				$wheregets .= "WHERE quote LIKE \"Data is an image%\"";
			}
		}
	}	
	else {

		if ($wheregets != "") {
			if(!isset($_POST['ping']) || $_POST['ping'] != 1) {
				$wheregets .= " OR quote NOT LIKE \"Data is an image%\"";
			} 
			else {
				$wheregets .= " AND quote NOT LIKE \"Data is an image%\"";
			}
		}
		else {
			if(!isset($_POST['ping']) || $_POST['ping'] != 1) {
				$wheregets .= "";
			}
			else {
				$wheregets .= " WHERE quote NOT LIKE \"Data is an image%\" ";
			}					
		}			
	}	
}

//págination stuff 

if (!isset($_POST['page'])) { 
	$page = 1; 
}
else {
	$page = $_POST['page'];
}

if (!isset($_POST['perpage'])) {
	$perpage = 20;
}
else {
	$perpage = $_POST['perpage'];
}

if (isset($_POST['quotequery']) && $_POST['quotequery'] != "") {
	if ($wheregets == "") {
		$wheregets .= " WHERE ";
	}
	else {
		$wheregets .= " AND ";
	}
	$wheregets .= $_POST['quotequery'];
}

$havingets = "";

if (isset($_POST['havingquery']) && $_POST['havingquery'] != "") {
	if ($havingets == "") {
		$havingets .= " HAVING ";
	}
	else {
		$havingets .= " AND ";
	}
	$havingets .= $_POST['havingquery'];
}

$middle = "";

$pminus = ($page-1) * $perpage;

$pgbeg = $pminus + 1;

$pgend = $page * $perpage;



$select_2 = " SELECT DISTINCT quote.quote_key, quote.quote, quote.important, GROUP_CONCAT(DISTINCT author.author SEPARATOR ', ') AS author, filename.filename, filename.cite_title, filename.prisec AS prisec, GROUP_CONCAT(DISTINCT publisher.publisher SEPARATOR ', ') AS publisher, filename.date, report_name.report_name, report_name.report_name_key, GROUP_CONCAT(DISTINCT country.country SEPARATOR ', ') AS country, GROUP_CONCAT(DISTINCT program.program SEPARATOR ', ') AS program, GROUP_CONCAT(DISTINCT tag.tag SEPARATOR ', ') AS tag, GROUP_CONCAT(DISTINCT agency.agency SEPARATOR ', ') AS agency ";

$middle = " FROM quote ";

$middle .= " INNER JOIN filename ON filename.filename_key = quote.filename_key 
			
			LEFT JOIN filename_author ON filename.filename_key = filename_author.filename_key 
			LEFT JOIN author ON author.author_key = filename_author.author_key 

			LEFT JOIN filename_publisher ON filename.filename_key = filename_publisher.filename_key 
			LEFT JOIN publisher ON publisher.publisher_key = filename_publisher.publisher_key 

			LEFT JOIN report ON report.filename_key = filename.filename_key
			LEFT JOIN report_name ON report_name.report_name_key = report.report_name_key

			INNER JOIN quote_country ON quote.quote_key = quote_country.quote_key
			INNER JOIN country ON quote_country.country_key = country.country_key

			INNER JOIN quote_program ON quote.quote_key = quote_program.quote_key
			INNER JOIN program ON quote_program.program_key = program.program_key

			INNER JOIN quote_tag ON quote.quote_key = quote_tag.quote_key
			INNER JOIN tag ON quote_tag.tag_key = tag.tag_key

			INNER JOIN quote_agency ON quote.quote_key = quote_agency.quote_key
			INNER JOIN agency ON quote_agency.agency_key = agency.agency_key";

if ($loggedin == TRUE) {

	$middle .= " LEFT JOIN quote_question ON quote.quote_key = quote_question.quote_key
				LEFT JOIN question on quote_question.question_key = question.question_key

				INNER JOIN data_enterer ON data_enterer.data_enterer_key = quote.data_enterer_key";
}

$middle .= " $wheregets GROUP BY quote.quote_key $havingets ";

$query2 = $select_2 . $middle;

if(!$result2 = $pdo->query($query2))	{
	die('There was an error running the query [' . $pdo->error . ']');
}

$row2 = $result2->fetchAll();

$allresults = count($row2);

$totalpages = ceil($allresults / $perpage);

$pageminusone = $page-1;
$pageplusone = $page+1;

if ($pgend > $allresults) {
	$pgend = $allresults;
}
if ($pgbeg > $allresults) {
	$pgbeg = $allresults;
	$page = $allresults;
}

echo <<<_END

{"results":"$allresults","page":"$page","totalpages":"$totalpages","pgbeg":"$pgbeg","pgend":"$pgend","perpage":"$perpage","pageminusone":"$pageminusone","pageplusone":"$pageplusone"}

_END;

$pdo = null;
?>