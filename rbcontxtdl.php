<?php
date_default_timezone_set('EST');
$fname = date("Ymd_His");

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename='.$fname.'_clips.txt');

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

if (isset($_GET['quotequery']) && $_GET['quotequery'] != "") {
	if ($wheregets == "") {
		$wheregets .= " WHERE ";
	}
	else {
		$wheregets .= " AND ";
	}
	$wheregets .= $_GET['quotequery'];
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

$max = " LIMIT " . $pminus . "," . $perpage;

$select_1 = " SELECT quote.quote, quote.quote_key, quote.important, GROUP_CONCAT(DISTINCT author.author SEPARATOR ', ') AS author, filename.filename, filename.cite_title, filename.city, filename.prisec AS prisec, GROUP_CONCAT(DISTINCT publisher.publisher SEPARATOR ', ') AS publisher, filename.date, quote.page_num, filename.url, report_name.report_name, report_name.report_name_key, GROUP_CONCAT(DISTINCT country.country SEPARATOR ', ') AS country, GROUP_CONCAT(DISTINCT program.program SEPARATOR ', ') AS program, GROUP_CONCAT(DISTINCT tag.tag SEPARATOR ', ') AS tag, GROUP_CONCAT(DISTINCT agency.agency SEPARATOR ', ') AS agency ";

$select_2 = " SELECT COUNT(DISTINCT quote.quote_key) AS allresults, quote.quote, quote.quote_key, quote.important, GROUP_CONCAT(DISTINCT author.author SEPARATOR ', ') AS author, filename.filename, filename.cite_title, filename.city, filename.prisec AS prisec, GROUP_CONCAT(DISTINCT publisher.publisher SEPARATOR ', ') AS publisher, filename.date, quote.page_num, filename.url, report_name.report_name, report_name.report_name_key, GROUP_CONCAT(DISTINCT country.country SEPARATOR ', ') AS country, GROUP_CONCAT(DISTINCT program.program SEPARATOR ', ') AS program, GROUP_CONCAT(DISTINCT tag.tag SEPARATOR ', ') AS tag, GROUP_CONCAT(DISTINCT agency.agency SEPARATOR ', ') AS agency ";

if ($loggedin == TRUE) {

	$select_1 .= ", GROUP_CONCAT(DISTINCT question.question_text SEPARATOR '<br>') AS question_text, GROUP_CONCAT(DISTINCT question.question_key SEPARATOR ', ') AS question_key, data_enterer.data_enterer ";
}

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

$max = "ORDER BY filename.date DESC " . $max;	

$query = $select_1 . $middle . $max;
$query2 = $select_2 . $middle;

// echo $query;

if(!$result = $pdo->query($query))	{
	die('There was an error running the query [' . $pdo->error . ']');
}

$row = $result->fetch(PDO::FETCH_ASSOC);

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

if ($allresults == 0) {
	echo "Sorry! No Clips match those criteria.";
}
else {
	do {

		$quote = stripslashes($row['quote']);
		$quote = str_replace("\\", "", $quote);
		$quote = html_entity_decode($quote);

		echo "$quote\n\n";

		echo "Footnote: ";

			if ($row['author'] != ""){
				echo "$row[author], ";
			}

			$pubdate = new DateTime($row['date']);
			$pubdate = $pubdate->format("F j, Y");

			if (!isset($row['filename']) || $row['filename'] == "") {
				echo "\"";
			}

			$cite_title = html_entity_decode($row['cite_title']);

			echo "$cite_title";

			if (!isset($row['filename']) || $row['filename'] == "") {
				echo "\"";
			}
			
			echo " ($row[city]: $row[publisher], $pubdate)";

			if (isset($row['page_num']) && $row['page_num'] != "") {
				echo ": $row[page_num]";
			}
			
			if (isset($row['url']) && ($row['url'] != "")) {

				echo " <$row[url]>";

			}

			echo ".\n\n";

			if ((isset($row['report_name']) && $row['report_name'] != "")) {

				echo "Instance of \"$row[report_name]\"\n\n";
			}

			if (isset($row['filename']) && $row['filename'] != "") {
				echo "Hosted at <http://$thisdomain/primarydocs/$row[filename]>\n\n";
			}

			if ($loggedin == TRUE) {
				
				if ($row['question_text'] != "") {

					echo "Questions:\n\n";
					
					$question_text = explode("<br>", $row['question_text']);
					$question_key = explode(", ", $row['question_key']);

					foreach ($question_text as $key => $value) {

						$quesquery = "SELECT DISTINCT question.asked
										, question.date_asked
										, contact.nickname
										, contact.contact_key 
										FROM question
										LEFT JOIN contact_question ON contact_question.question_key = question.question_key
										LEFT JOIN contact ON contact.contact_key = contact_question.contact_key
										WHERE question.question_key = $question_key[$key]";

						if(!$quesresult = $pdo->query($quesquery))	{
							die('There was an error running the questions query [' . $pdo->error . ']');
						}
						$quesrow = $quesresult->fetch(PDO::FETCH_ASSOC);

						$quesquery2 = "SELECT COUNT(DISTINCT contact.nickname) AS num 
										FROM question
										LEFT JOIN contact_question ON contact_question.question_key = question.question_key
										LEFT JOIN contact ON contact.contact_key = contact_question.contact_key
										WHERE question.question_key = $question_key[$key]";

						if(!$quesresult2 = $pdo->query($quesquery2))	{
							die('There was an error running the questions query [' . $pdo->error . ']');
						}
						$quesrow2 = $quesresult2->fetch(PDO::FETCH_ASSOC);
						$value = stripslashes($value);
						$value = str_replace("\\", "", $value);

						echo "* $value";

						if ($quesrow['asked'] == 1) {

							$date_asked = new DateTime($quesrow['date_asked']);
							$date_asked = $date_asked->format("F j, Y");
							$i = 1;
							echo " (X asked ";
							do {
								echo "$quesrow[nickname] $date_asked";
								if ($quesrow2['num'] > $i) {
									echo ", ";
								}
								$i++;
							}
							while($quesrow = $quesresult->fetch(PDO::FETCH_ASSOC));
							echo ")";
						}
						elseif (isset($quesrow['nickname']) || $quesrow['nickname'] != "") {
							$i = 1;
							echo " (ask ";
								do {
									echo "$quesrow[nickname]";
									if ($quesrow2['num'] > $i) {
									echo ", ";
								}
								$i++;
							}
							while($quesrow = $quesresult->fetch(PDO::FETCH_ASSOC));			
							echo ")";
						}

						echo "\n";

					}
					echo "\n";
				}

				$sharequery = "SELECT share.shared AS shared
								, share.quote_key
								, share.contact_key
								, contact.nickname AS nickname
								FROM share
								LEFT JOIN contact ON contact.contact_key = share.contact_key
								WHERE share.quote_key = $row[quote_key]";

				$sharequery2 = "SELECT COUNT(share.contact_key) AS num 
								FROM share
								LEFT JOIN contact ON contact.contact_key = share.contact_key
								WHERE share.quote_key = $row[quote_key]";


				if(!$shareresult = $pdo->query($sharequery))	{
					die('There was an error running the questions query [' . $pdo->error . ']');
				}
				if(!$shareresult2 = $pdo->query($sharequery2))	{
					die('There was an error running the questions query [' . $pdo->error . ']');
				}

				$sharerow = $shareresult->fetch(PDO::FETCH_ASSOC);
				$sharerow2 = $shareresult2->fetch(PDO::FETCH_ASSOC);

				if (isset($sharerow['nickname']) && $sharerow['nickname'] != "") {

					if(isset($sharerow['shared']) && $sharerow['shared'] == 1) {

						echo "(X shared ";
					}
					else {
						echo "(share with ";
					}

					$i = 1;

					do {
						echo "$sharerow[nickname]";
						if ($sharerow2['num'] > $i) {
							echo ", ";
						}
						$i++;
					}
					while($sharerow = $shareresult->fetch(PDO::FETCH_ASSOC));

					echo ")\n\n";
				}

				echo "Clip entered by $row[data_enterer]\n\n";
			}
		echo "----------------------------------------\n\n";
	}
	while($row = $result->fetch(PDO::FETCH_ASSOC));
}

function commalinks($cat, $array) {

	foreach ($array as $key => $value) {
		echo "$value";
		if ($key < count($array)-1) {
			echo ", ";
		}
	}
}

function catsLister($cat, $row) {

	$category = explode(", ", $row["$cat"]);
	sort($category);
	commalinks($cat, $category);
}

$pdo = null;
?>