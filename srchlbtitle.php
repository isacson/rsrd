<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";

//$country = $program = $tag = $agency = "";

$gets = [];

if (isset($_GET['country']) && ($_GET['country'] != "")) {

	$gets['country'] = $_GET['country'];
}

if (isset($_GET['program']) && ($_GET['program'] != "")) {

	$gets['program'] = $_GET['program'];
}

if (isset($_GET['tag']) && ($_GET['tag'] != "")) {

	$gets['tag'] = $_GET['tag'];
}

if (isset($_GET['agency']) && ($_GET['agency'] != "")) {

	$gets['agency'] = $_GET['agency'];
}

if (isset($_GET['data_enterer']) && ($_GET['data_enterer'] != "")) {

	$gets['data_enterer'] = $_GET['data_enterer'];
}

if (isset($_GET['prisec']) && ($_GET['prisec'] != "")) {

	$gets['prisec'] = $_GET['prisec'];
}

if (isset($_GET['language']) && ($_GET['language'] != "")) {

	$gets['language'] = $_GET['language'];
}

if (isset($_GET['important']) && ($_GET['important'] == "1")) {

	$gets['important'] = $_GET['important'];
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


if (isset($_POST['querytext']) && $_POST['querytext'] != "") {

	if ($wheregets == "") {
		$wheregets .= " WHERE ";
	}
	else {
		$wheregets .= " AND ";
	}

	$wheregets .= " $_POST[querytext] ";
	
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
				if(isset($_POST['ping'])) {
					$wheregets .= "";
				} 
				else {
					$wheregets .= " AND quote LIKE \"Data is an image%\"";
				}
			}
			else {
				if(isset($_POST['ping'])) {
					$wheregets .= "";
				}
				else {
					$wheregets .= "WHERE quote LIKE \"Data is an image%\"";
				}
			}
		}	
		else {

			if ($wheregets != "") {
				if(!isset($_POST['ping'])) {
					$wheregets .= " OR quote NOT LIKE \"Data is an image%\"";
				} 
				else {
					$wheregets .= " AND quote NOT LIKE \"Data is an image%\"";
				}
			}
			else {
				if(!isset($_POST['ping'])) {
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
}
else {

	if (isset($_GET['is_audio']) && $_GET['is_audio'] != "") {

		if ($wheregets == "") {
			$wheregets .= " WHERE ";
		}
		else {
			$wheregets .= " AND ";
		}

		$wheregets .= "( is_audio = $_GET[is_audio] ";
		
		if (isset($_GET['is_image']) && $_GET['is_image'] == 2 ) {
			$wheregets .= " OR quote LIKE \"Data is an image%\") ";
		}
		else {
			$wheregets .= " AND quote NOT LIKE \"Data is an image%\") ";	
		}
	}
	else {
		if (isset($_GET['is_image']) && $_GET['is_image'] == 2 ) {

			if ($wheregets != "") {
				if(isset($_GET['ping'])) {
					$wheregets .= "";
				} 
				else {
					$wheregets .= " AND quote LIKE \"Data is an image%\"";
				}
			}
			else {
				if(isset($_GET['ping'])) {
					$wheregets .= "";
				}
				else {
					$wheregets .= "WHERE quote LIKE \"Data is an image%\"";
				}
			}
		}	
		else {

			if ($wheregets != "") {
				if(!isset($_GET['ping'])) {
					$wheregets .= " OR quote NOT LIKE \"Data is an image%\"";
				} 
				else {
					$wheregets .= " AND quote NOT LIKE \"Data is an image%\"";
				}
			}
			else {
				if(!isset($_GET['ping'])) {
					$wheregets .= "";
				}
				else {
					$wheregets .= " WHERE quote NOT LIKE \"Data is an image%\" ";
				}					
			}			
		}	
	}

	//págination stuff 

	if (!isset($_GET['page'])) { 
		$page = 1; 
	}
	else {
		$page = $_GET['page'];
	}

	if (!isset($_GET['perpage'])) {
		$perpage = 20;
	}
	else {
		$perpage = $_GET['perpage'];
	}
}

$middle = "";

$pminus = ($page-1) * $perpage;

$pgbeg = $pminus + 1;

$pgend = $page * $perpage;

$max = " LIMIT " . $pminus . "," . $perpage;


$select_2 = " SELECT COUNT(DISTINCT quote.quote_key) AS allresults ";

$middle = " FROM quote ";

$middle .= " INNER JOIN filename ON filename.filename_key = quote.filename_key 
			
			LEFT JOIN filename_author ON filename.filename_key = filename_author.filename_key 
			LEFT JOIN author ON author.author_key = filename_author.author_key 

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

$middle .= " $wheregets ";

$max = " GROUP BY quote.quote_key ORDER BY quote.quote_key DESC " . $max;

$query2 = $select_2 . $middle;

if(!$result2 = $pdo->query($query2))	{
	die('There was an error running the query [' . $pdo->error . ']');
}

$row2 = $result2->fetch(PDO::FETCH_ASSOC);

$allresults = $row2['allresults'];

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

echo "<strong>$allresults clip";

if ($allresults > 1 || $allresults == 0) {

	echo "s";
}

echo " in the database about:</strong>";

$pdo = null;
?>