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

$allresults = $_POST['allresults'];
$page = $_POST['page'];
$totalpages = $_POST['totalpages'];
$pgbeg = $_POST['pgbeg'];
$pgend = $_POST['pgend'];
$perpage = $_POST['perpage'];
$pageminusone = $_POST['pageminusone'];
$pageplusone = $_POST['pageplusone'];

$resultone = $pageminusone * $perpage;

$max = " LIMIT " . $resultone . "," . $perpage;

$select_1 = " SELECT quote.quote, quote.quote_key, quote.important, GROUP_CONCAT(DISTINCT author.author SEPARATOR ', ') AS author, filename.filename, filename.cite_title, filename.city, filename.prisec AS prisec, GROUP_CONCAT(DISTINCT publisher.publisher SEPARATOR ', ') AS publisher, filename.date, quote.page_num, filename.url, report_name.report_name, report_name.report_name_key, GROUP_CONCAT(DISTINCT country.country SEPARATOR ', ') AS country, GROUP_CONCAT(DISTINCT program.program SEPARATOR ', ') AS program, GROUP_CONCAT(DISTINCT tag.tag SEPARATOR ', ') AS tag, GROUP_CONCAT(DISTINCT agency.agency SEPARATOR ', ') AS agency ";


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

// echo $query;

if(!$result = $pdo->query($query))	{
	die('There was an error running the query [' . $pdo->error . ']');
}

$row = $result->fetch(PDO::FETCH_ASSOC);


echo "<div id='rbcontentwords'>";

if ($allresults == 0) {
	echo "<p align='center'><strong>Sorry! No Clips match those criteria.</strong></p>";
}
else {
	do {

		$quote = stripslashes($row['quote']);
		$quote = str_replace("\\", "", $quote);
		$quote = preg_replace("/(\n)/", "<br>", $quote);

		echo "<div class='snipcontainer'>";

		if (isset($row['prisec']) && ($row['prisec'] == "0")) {

			echo "<div class='sniptextsec'";
		}
		else  {
			echo "<div class='sniptext'";
		}

		if(isset($row['important']) && $row['important'] == 1) {
			echo " style='border: solid 1px yellow;'";
		}

		echo " id='clip$row[quote_key]'>";

		preg_match("/\/primarydocs\/images\/.*?(\s|\n|$)/", $quote, $mgmatch);

		if (count($mgmatch)>0 && $mgmatch[0] != "") {

			echo "<div style='text-align:center;'><a href='$mgmatch[0]' target='_blank'><img src='$mgmatch[0]' style='height:200px;border-radius:10px;'></a></div>";
		} 

		echo "$quote</div>";

			echo "<div class='fnholder'>
			<div class='footnote' id='footnote$row[quote_key]'>";

			if ($row['author'] != ""){
				echo "$row[author], ";
			}

			$pubdate = new DateTime($row['date']);
			$pubdate = $pubdate->format("F j, Y");

			if (isset($row['filename']) && $row['filename'] != "") {
						echo "<a target='_blank' href='/primarydocs/$row[filename]'>";
			}
			else {
				echo "&ldquo;";
			}

			echo "$row[cite_title]";

			if (!isset($row['filename']) || $row['filename'] == "") {
				echo "&rdquo;";
			}

			if (isset($row['filename']) && $row['filename'] != "") {
						echo "</a>";
			}
			
			echo " ($row[city]: $row[publisher], $pubdate)";

			if (isset($row['page_num']) && $row['page_num'] != "") {
				echo ": $row[page_num]";
			}
			
			if (isset($row['url']) && ($row['url'] != "")) {

				echo " &lt;$row[url]&gt;";

			}

			echo ".</div>";

			echo "<div>";

			echo "<button class='fnbuttons' id='selectclip$row[quote_key]' onclick='selectClip(\"$row[quote_key]\")'>Select Clip</button><button class='fnbuttons' id='selectfoot$row[quote_key]' onclick='selectFoot(\"$row[quote_key]\")'>Select Footnote</button>";

			if ($loggedin == TRUE) {
				echo "<br><button class='fnbuttons' id='editfoot'>Edit This</button>";
			}

			echo "</div></div>";

			echo "<div class='catslist'>Countries: ";

			catsLister("country", $row);

			echo "</div>
			<div class='catslist'>Programs: ";

			catsLister("program", $row);

			echo "</div>
			<div class='catslist'>Tags: ";

			catsLister("tag", $row);

			echo "</div>
			<div class='catslist'>Agencies: ";

			catsLister("agency", $row);

			echo "</div>";

			if ((isset($row['report_name']) && $row['report_name'] != "")) {

				echo "<div class='catslist'>Instance of &ldquo;<a target='_blank' href='/reports/index.php?report_name_key=$row[report_name_key]'>$row[report_name]</a>&rdquo;</div>";
			}

			if ($loggedin == TRUE) {
				
				if ($row['question_text'] != "") {

					echo "<div class='catslist'>Questions:<ul class='queslist'>";
					
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

						echo "<li>$value";

						if ($quesrow['asked'] == 1) {

							$date_asked = new DateTime($quesrow['date_asked']);
							$date_asked = $date_asked->format("F j, Y");
							$i = 1;
							echo " (&#10003; asked ";
							do {
								echo "<a target='_blank' href='/contacts/index.php?contact_key=$quesrow[contact_key]'>$quesrow[nickname]</a> $date_asked";
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
									echo "<a target='_blank' href='/contacts/index.php?contact_key=$quesrow[contact_key]'>$quesrow[nickname]</a>";
									if ($quesrow2['num'] > $i) {
									echo ", ";
								}
								$i++;
							}
							while($quesrow = $quesresult->fetch(PDO::FETCH_ASSOC));			
							echo ")";
						}

						echo "</li>";

					}
					echo "</ul></div>";
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

					echo "<div class='catslist'>";

					if(isset($sharerow['shared']) && $sharerow['shared'] == 1) {

						echo " (&#10003; Shared ";
					}
					else {
						echo "(Share with ";
					}

					$i = 1;

					do {
						echo "<a target='_blank' href='/contacts/index.php?contact_key=$sharerow[contact_key]'>$sharerow[nickname]</a>";
						if ($sharerow2['num'] > $i) {
							echo ", ";
						}
						$i++;
					}
					while($sharerow = $shareresult->fetch(PDO::FETCH_ASSOC));

					echo ")</div>";
				}

				echo "<div class='catslist'>Clip entered by $row[data_enterer]</div>";
			}
		echo "</div>";
	}
	while($row = $result->fetch(PDO::FETCH_ASSOC));
}
echo "</div>";

echo  "<div id='rbnavfoot'><strong>Page $page of $totalpages</strong> ($pgbeg to $pgend of $allresults clips)<br>";

if ($page != 1) {
	echo "<a onClick='paginateLink(1, $perpage, $allresults);'>&lt;&lt; First Page</a> &mdash; <a onClick='paginateLink($pageminusone, $perpage, $allresults);'>&lt; Previous Page</a> &mdash; ";
}

	echo  "<form id='gotopagebot' class='paginationform' name='gotopagebot'>Go To Page <input type='text' id='getpage' name='getpage' size='4'> <button type='submit' onClick='event.preventDefault(); paginateButt(";

	if ($loggedin === TRUE) {
		echo  "\"zaxxon\"";
	}
	else {
		echo "0";
	}

	echo  ", $allresults);'>Go</button>";

if ($page != $totalpages) {
	echo  " &mdash; <a onClick='paginateLink($pageplusone, $perpage, $allresults);'>Next Page &gt;</a> &mdash; <a onClick='paginateLink($totalpages, $perpage, $allresults);'>Last Page &gt;&gt;</a></form>";
}


echo  "<form id='clipsperpagebot' class='paginationform' name='clipsperpagebot'>Clips Per Page <input type='text' id='getclipspage' name='getclipspage' size='4'> <button type='submit' onClick='event.preventDefault(); paginateButt(";

	if ($loggedin === TRUE) {
		echo  "\"zaxxon\"";
	}
	else {
		echo "0";
	}

echo  ", $allresults);'>Go</button>";


if ($loggedin == FALSE) {
	echo  " (maximum 50)</form>";
}

echo  "</div>";

function commalinks($cat, $array) {

	foreach ($array as $key => $value) {
		echo "<span class='itemslist' onclick='fromCatslist(\"$cat\", \"$value\")'>$value</span>";
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