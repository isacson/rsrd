<?php 

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";

if ($loggedin !== true) {
	echo "<h3>We're sorry</h3>";
	echo "<p>You must be logged in to use this page. If you've been given a username and password, click \"Login\" on the menu above. (<a href='/index.php'>Go back to the Data Clips page</a></p>";
	exit();
}
else {

	$oops_query = [];

	if (isset($_POST['oopsdelete']) && $_POST['oopsdelete'] != "") {
		$oopsdelete = explode("-----", $_POST['oopsdelete']);
		foreach ($oopsdelete as $key => $value) {
			if(!$value = $pdo->query($value))	{
					die('There was an error running the query [' . $pdo->error . ']');
			}
		}
		echo "<p>OK, $_POST[yrname]. You deleted the $_POST[nth] entry.</p>";
	}

	if (isset($_POST['formused']) && $_POST['formused'] != "") {

		$pdo->beginTransaction();

		// If there's something in the "quote" field, then we know data was entered.
		if (isset($_POST['quote']) && $_POST['quote'] != "") {

// First, let's do the stuff that goes into the "filename" sql table.
			// Is there a filename? (If it's a secondary source we're not keeping a copy of, there doesn't have to be.)
			if ((!isset($_POST['filename']) || $_POST['filename'] == "") && $_POST['prisec'] == 1) {
				// However, if it's a primary source, there'd better be a filename. We need a digital copy.
					echo "Sorry. A primary source document must have a filename because you've stored it in the primarydocs folder.<span id='nde' value='nfn'></span>";
					exit();
			}
			// If there's no filename, and it's a secondary source, at least make sure there's a URL for the place we found it.
			if ((!isset($_POST['filename']) || $_POST['filename'] == "") && $_POST['prisec'] == 0 && (!isset($_POST['url']) || $_POST['url'] == "")) {

				echo "Sorry. A secondary source at least needs a URL to show where you found it.<span id='nde' value='nur'></span>";
				exit();
			}
			if (!isset($_POST['cite_title']) || $_POST['cite_title'] == "") {
				echo "Sorry. To cite the document where you found this clip, we need its title.<span id='nde' value='nct'></span>";
				exit();
			}
			if (!isset($_POST['publisher']) || $_POST['publisher'] == "") {
				echo "Sorry. To cite the document where you found this clip, we need its publisher. Even if it&rsquo;s a letter or memo, include the institution the writer works for.<span id='nde' value='npu'></span>";
				exit();
			}
			if (!isset($_POST['date']) || $_POST['date'] == "") {
				echo "Sorry. To cite the document where you found this clip, we need to know the date it was published. At least the year.<span id='nde' value='ndt'></span>";
				exit();
			}

			// With those exceptions out of the way, let's query the database to see if the filename (and thus its data) already exists.

			if ((!isset($_POST['filename']) || $_POST['filename'] == "") && $_POST['prisec'] == 0 && (isset($_POST['url']) && $_POST['url'] != "")) {

				$furl = sanitizeString($_POST['url']);
				$find_query = "SELECT filename_key FROM filename WHERE url = '$furl'";
			}

			$find_query = findquery("filename", sanitizeString($_POST['filename']));

			if(!$findresult = $pdo->query($find_query))	{
				die('There was an error running the query [' . $pdo->error . ']');
			}

			// If it exists, we're done with the filename table. Add its key as a variable.
			$findrow = $findresult->fetchAll();
			if (count($findrow) > 0) {
				$filename_key = $findrow[0]['filename_key'];
			}
			// If it doesn't exist, make the query to enter the form data into the filename table.
			else {
				$filename_stmt = simplequery("filename, cite_title, url, date, city, prisec, language, is_audio, read_it", "filename", $_POST);

				// $filename_stmt is an array with 2 elements. [0] is the first line of the prepared statement. [1] is an array of the values for each parameter. All ready to go when it's time to do the INSERT query.

				$r = anotherSimple($filename_stmt, "filename", $pdo);
				$filename_key = $r[0];
				$oops_query[] = $r[1];
			}

//Now, let's do the data_enterer field, which is mandatory
			if (!isset($_POST['data_enterer']) || $_POST['data_enterer'] == "") {

				echo "We have to know who you are. Please put your name in the &ldquo;Who are you?&rdquo; field.<span id='nde' value='nde'></span>";
				exit();
			}
			else {

					$bbb = strpos($_POST['data_enterer'], " ");
					if ($bbb < 1) {
						echo "Sorry, we need your last name. There may be other &ldquo;$_POST[data_enterer]s&rdquo; in the future.<span id='nde' value='nde'></span>";
						exit();
					}

				// Let's query the database to see if this data_enterer already exists.
				$find_query = findquery("data_enterer", sanitizeString($_POST['data_enterer']));

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the data_enterer table. Add its key as a variable.
				$findrow = $findresult->fetchAll();

				if (count($findrow) > 0) {
					$data_enterer_key = $findrow[0]['data_enterer_key'];
				}
				else {
					$data_enterer_stmt = simplequery("data_enterer", "data_enterer", $_POST);
					$r = anotherSimple($data_enterer_stmt, "data_enterer", $pdo);
					$data_enterer_key = $r[0];
					$oops_query[] = $r[1];
				}
			}

// Now, let's insert data into the "quote" table. We already know that the "quote" field has text because we wouldn't have passed the first "if" statement and gotten here. So no need to test.
			$quote = sanitizeString($_POST['quote']);
			$quote = str_replace("'", "&rsquo;", $_POST['report_name']);

			$find_query = findquery("quote", $quote);

			if(!$findresult = $pdo->query($find_query))	{
				die('There was an error running the query [' . $pdo->error . ']');
			}
			// If it exists, then stop the process. We don't want the same quoe going in twice.
			$findrow = $findresult->fetchAll();

			if (count($findrow) > 0) {
				echo "You've already entered this exact quote. We can't let you do that twice.";
				exit();
			}
			else {

				// Build the "quote" query. This time, we're not just taking $_POST values, but using previously acquired keys, so we can't use the simplequery() function.
				$allfields = fieldtoarray("quote, page_num, important");
				$usedfields = [];
				$usedvalues = [];

				foreach($allfields as $key => $value) {

					if (isset($_POST["$value"]) && $_POST["$value"] != "") {
						$usedfields[] = $value;
						$usedvalues[] = sanitizeString($_POST["$value"]);
					}
				}

				// this is the new part
				$usedfields[] = "filename_key";
				$usedfields[] = "data_enterer_key";
				$usedvalues[] = $filename_key;
				$usedvalues[] = $data_enterer_key;
				//

				$usedfield2 = implode(", ", $usedfields);
				$placeholders = implode(", :", $usedfields);

				$qi = ("INSERT INTO quote ($usedfield2) VALUES (:$placeholders);");
				$qii = [];
				$qiii = [];

				foreach ($usedvalues as $key => $value) {
						
					$qii[] = $usedfields[$key];
					$qiii[] = $value;
				}

				$quote_stmt = array($qi, $qii, $qiii);
				$r = anotherSimple($quote_stmt, "quote", $pdo);
				$quote_key = $r[0];
				$oops_query[] = $r[1];
			}

// OK, let's insert data into the report_name table. This one's optional, just goes into an "if" statement.
			if (isset($_POST['report_name']) && $_POST['report_name'] != "") {

				$find_query = str_replace("'", "&rsquo;", $_POST['report_name']);
				// Let's query the database to see if this report name already exists.
				$find_query = findquery("report_name", sanitizeString($find_query));

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the report_name table. Add its key as a variable.
				$findrow = $findresult->fetchAll();

				if (count($findrow) > 0) {
					$report_name_key = $findrow[0]['report_name_key'];
				}
				else {
					$report_name_stmt = simplequery("report_name, report_desc", "report_name", $_POST);
					$r = anotherSimple($report_name_stmt, "report_name", $pdo);
					$report_name_key = $r[0];
					$oops_query[] = $r[1];
				}
			}
// The next insert is the "law" table. Again, not mandatory.
			if (isset($_POST['law']) && $_POST['law'] != "") {

				// Let's query the database to see if this law already exists.
				$find_query = findquery("law", sanitizeString($_POST['law']));

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the law table. Add its key as a variable.
				$findrow = $findresult->fetchAll();

				if (count($findrow) > 0) {
					$law_key = $findrow[0]['law_key'];
				}
				else {
					$law_stmt = simplequery("law", "law", $_POST);
					$r = anotherSimple($law_stmt, "law", $pdo);
					$law_key = $r[0];
					$oops_query[] = $r[1];
				}
			}
// Next insert: report
			if ((isset($_POST['report_name']) && $_POST['report_name'] != "") && (isset($_POST['filename']) && $_POST['filename'] != "")) {

				// Let's query the database to see if this report already exists.
				$find_query = "SELECT report_key FROM report WHERE report_name_key = $report_name_key";

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the report table. Add its key as a variable.
				$findrow = $findresult->fetchAll();

				if (count($findrow) > 0) {
					$report_key = $findrow[0]['report_key'];
				}
				else {

					// Build the "report" query. This time, we're not just taking $_POST values, but using previously acquired keys, so we can't use the simplequery() function.
					$allfields = fieldtoarray("section, report_due");
					$usedfields = [];
					$usedvalues = [];

					foreach ($allfields as $key => $value) {

						if (isset($_POST["$value"]) && $_POST["$value"] != "") {
							$usedfields[] = $value;
							$usedvalues[] = sanitizeString($_POST["$value"]);
						}
					}

					// this is the new part
					$usedfields[] = "report_name_key";
					$usedfields[] = "filename_key";
					if(isset($law_key)) {
						$usedfields[] = "law_key";
					}

					$usedvalues[] = $report_name_key;
					$usedvalues[] = $filename_key;
					if(isset($law_key)) {
						$usedvalues[] = $law_key;
					}

					$usedfield2 = implode(", ", $usedfields);
					$placeholders = implode(", :", $usedfields);

					$qi = ("INSERT INTO report ($usedfield2) VALUES (:$placeholders);");
					$qii = [];
					$qiii = [];

					foreach ($usedvalues as $key => $value) {
						
						$qii[] = $usedfields[$key];
						$qiii[] = $value;
					}

					$report_stmt = array($qi, $qii, $qiii);

					$r = anotherSimple($report_stmt, "report", $pdo);
					$report_key = $r[0];
					$oops_query[] = $r[1];
				}
			}
// Now, we insert into the "author" table. "Author" is the first value that may be an array, separated by commas.
			if (isset($_POST['author']) && $_POST['author'] != "") {

				$authors = fieldtoarray(sanitizeString($_POST['author']));

				foreach ($authors as $author) {

					// Let's query the database to see if this author already exists.
					$find_query = findquery("author", $author);

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the author table. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0) {
						$author_key[] = $findrow[0]['author_key'];
					}
					else {
						$stmt = $pdo->prepare("INSERT INTO author (author) VALUE (:author);");
						$stmt->bindValue(':author', $author);
						$stmt->execute();

						$author_key[] = $pdo->lastInsertId();
						$oops_query[] = "DELETE FROM author WHERE author_key = " . $pdo->lastInsertId();
					}
				}
			}
// Now, we insert into the "publisher" table. "Publisher" is an array, separated by commas.
			if (isset($_POST['publisher']) && $_POST['publisher'] != "") {

				$publishers = fieldtoarray(sanitizeString($_POST['publisher']));

				foreach ($publishers as $publisher) {

					// Let's query the database to see if this publisher already exists.
					$find_query = findquery("publisher", $publisher);

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the publisher table. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0) {
						$publisher_key[] = $findrow[0]['publisher_key'];
					}
					else {
						$stmt = $pdo->prepare("INSERT INTO publisher (publisher) VALUE (:publisher);");
						$stmt->bindValue(':publisher', $publisher);
						$stmt->execute();

						$publisher_key[] = $pdo->lastInsertId();
						$oops_query[] = "DELETE FROM publisher WHERE publisher_key = " . $pdo->lastInsertId();
					}
				}
			}
// Now, we insert into the "agency" table. "Agency" is required, but if it's not listed, we'll call it "No agency listed." Plus, the user may have entered an array.
			if (isset($_POST['agency']) && $_POST['agency'] != "") {

				$agencies = fieldtoarray(sanitizeString($_POST['agency']));

				foreach ($agencies as $agency) {

					// Let's query the database to see if this agency already exists.
					$find_query = findquery("agency", $agency);

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the agency table. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0) {
						$agency_key[] = $findrow[0]['agency_key'];
					}
					else {
						$r = acpt("agency", $agency, $pdo);

						$agency_key[] = $r[0];
						$oops_query[] = $r[1];
					}
				}
			}
			else {
				// What we'll do if the "agency" field is blank
				// See if "no agency listed" already exists. If so, store its key.
				$find_query = findquery("agency", "No agency listed");

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the agency table. Add its key as a variable.
				$findrow = $findresult->fetchAll();
				
				if (count($findrow) > 0) {
					$agency_key[] = $findrow[0]['agency_key'];
				}
				// If "no agency listed" does not exist, create it and get its key.
				else {
						$r = acpt("agency", "No agency listed", $pdo);

						$agency_key[] = $r[0];
						$oops_query[] = $r[1];
				}
			}
// Now, we insert into the "country" table. "Country" is required, but if it's not listed, we'll call it "Western Hemisphere Regional." Plus, the user may have entered an array.
			if (isset($_POST['country']) && $_POST['country'] != "") {

				$countries = fieldtoarray(sanitizeString($_POST['country']));

				foreach ($countries as $country) {

					// Let's query the database to see if this country already exists.
					$find_query = findquery("country", $country);

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the country table. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0) {
						$country_key[] = $findrow[0]['country_key'];
					}
					else {
						$r = acpt("country", $country, $pdo);

						$country_key[] = $r[0];
						$oops_query[] = $r[1];
					}
				}
			}
			else {
				// What we'll do if the "country" field is blank
				// See if "Western Hemisphere Regional" already exists. If so, store its key.
				$find_query = findquery("country", "Western Hemisphere Regional");

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the country table. Add its key as a variable.
				$findrow = $findresult->fetchAll();
				
				if (count($findrow) > 0) {
					$country_key[] = $findrow[0]['country_key'];
				}
				// If "Western Hemisphere Regional" does not exist, create it and get its key.
				else {
					$r = acpt("country", "Western Hemisphere Regional", $pdo);

					$country_key[] = $r[0];
					$oops_query[] = $r[1];
				}
			}
// Now, we insert into the "program" table. "Program" is required, but if it's not listed, we'll call it "No program listed." Plus, the user may have entered an array.
			if (isset($_POST['program']) && $_POST['program'] != "") {

				$programs = fieldtoarray(sanitizeString($_POST['program']));

				foreach ($programs as $program) {

					// Let's query the database to see if this program already exists.
					$find_query = findquery("program", $program);

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the program table. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0 && $findrow[0] != "") {
						$program_key[] = $findrow[0]['program_key'];
					}
					else {
						$r = acpt("program", $program, $pdo);

						$program_key[] = $r[0];
						$oops_query[] = $r[1];
					}
				}
			}
			else {
				// What we'll do if the "program" field is blank
				// See if "No program listed" already exists. If so, store its key.
				$find_query = findquery("program", "No program listed");

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the program table. Add its key as a variable.
				$findrow = $findresult->fetchAll();
				
				if (count($findrow) > 0) {
					$program_key[] = $findrow[0]['program_key'];
				}
				// If "No program listed" does not exist, create it and get its key.
				else {
					$r = acpt("program", "No program listed", $pdo);

					$program_key[] = $r[0];
					$oops_query[] = $r[1];
				}
			}
// Now, we insert into the "tag" table. "Tag" is required, but if it's not listed, we'll call it "Untagged." Plus, the user may have entered an array.
			if (isset($_POST['tag']) && $_POST['tag'] != "") {

				$tags = fieldtoarray(sanitizeString($_POST['tag']));

				foreach ($tags as $tag) {

					// Let's query the database to see if this tag already exists.
					$find_query = findquery("tag", $tag);

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the tag table. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0) {
						$tag_key[] = $findrow[0]['tag_key'];
					}
					else {
						$r = acpt("tag", $tag, $pdo);

						$tag_key[] = $r[0];
						$oops_query[] = $r[1];
					}
				}
			}
			else {
				// What we'll do if the "tag" field is blank
				// See if "Untagged" already exists. If so, store its key.
				$find_query = findquery("tag", "Untagged");

				if(!$findresult = $pdo->query($find_query))	{
					die('There was an error running the query [' . $pdo->error . ']');
				}
				// If it exists, we're done with the tag table. Add its key as a variable.
				$findrow = $findresult->fetchAll();
				
				if (count($findrow) > 0) {
					$tag_key[] = $findrow[0]['tag_key'];
				}
				// If "Untagged" does not exist, create it and get its key.
				else {
						$r = acpt("tag", "Untagged", $pdo);

						$tag_key[] = $r[0];
						$oops_query[] = $r[1];
				}
			}
// Time to insert the contacts of the people we'd like to share the snippet with.
			if (isset($_POST['share']) && $_POST['share'] != "") {	

				$shares = fieldtoarray(sanitizeString($_POST['share']));

				foreach ($shares as $share) {
					
					// Let's query the database to see if this contact already exists.
					$find_query = "SELECT contact_key FROM contact WHERE nickname = '$share'";

					if(!$findresult = $pdo->query($find_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}
					// If it exists, we're done with the contact table for shares. Add its key as a variable.
					$findrow = $findresult->fetchAll();
					
					if (count($findrow) > 0) {
						$share_contact_key[] = $findrow[0]['contact_key'];
					}
					else {
						// add the new nickname to contacts table
						$stmt = $pdo->prepare("INSERT INTO contact (nickname) VALUE (:nickname);");
						$stmt->bindValue(':nickname', $share);
						$stmt->execute();

						$share_contact_key[] = $pdo->lastInsertId();
						$oops_query[] = "DELETE FROM contact WHERE contact_key = " . $pdo->lastInsertId();
					}
				}
				// now we put it into the share table
				foreach ($share_contact_key as $value) {
					$stmt = $pdo->prepare("INSERT INTO share (quote_key, contact_key) VALUES (:quote_key, :contact_key);");
					$stmt->bindValue(':quote_key', $quote_key);
					$stmt->bindValue(':contact_key', $value);
					$stmt->execute();
				}
				$oops_query[] = "DELETE FROM share WHERE quote_key = '$quote_key'";
			}
// The array of questions now
			if (isset($_POST['quescon'])) {
				if ($_POST['quescon']['question_text'][0] != "") {

					$question_texts = $_POST['quescon']['question_text'];
					$nicknames = $_POST['quescon']['nickname'];

					foreach ($question_texts as $key => $question_text) {
						if ($question_text != "") {
							$question_text = sanitizeString($question_text);
							$question_text = str_replace("&quot;", "\\\"", $question_text);
							$question_text = str_replace("\\\'", "&rsquo;", $question_text);
							$find_query = "SELECT question_key FROM question WHERE question_text = '$question_text'";

							if(!$findresult = $pdo->query($find_query))	{
								die('There was an error running the query [' . $pdo->error . ']');
							}
							$findrow = $findresult->fetchAll();

							if (count($findrow) > 0) {
								$question_key[$key] = $findrow[0]['question_key'];
							}
							else {
							// add the new question_text to question table
								$r = questionsKeys($question_text, $pdo);

								$question_key[$key] = $r[0];
								$oops_query[] = $r[1];
							}

							if (isset($nicknames[$key]) && $nicknames[$key] != "") {
								$nickname = fieldtoarray(sanitizeString($nicknames[$key]));
								foreach ($nickname as $nick) {
									$find_query = "SELECT contact_key FROM contact WHERE nickname = '$nick'";
									
									if(!$findresult = $pdo->query($find_query))	{
										die('There was an error running the query [' . $pdo->error . ']');
									}

									$findrow = $findresult->fetchAll();
						
									if (count($findrow) > 0) {
										$ques_contact_key[$key][] = $findrow[0]['contact_key'];
									}
									else {
									// add the new nickname to contacts table
									$r = contactsKeys($nick, $pdo);

									$ques_contact_key[$key][] = $r[0];
									$oops_query[] = $r[1];
									}
								}
							}
						}
					}
				}
			}

// Now, let's do the contact-question table inserts

			if ((isset($ques_contact_key) && count($ques_contact_key) > 0) && (isset($question_key) && $question_key[0] != "")) {

				foreach ($ques_contact_key as $key => $thevalue) {
					foreach ($thevalue as $value) {
						$oops_query[] = junctioninsert("contact", "question", $value, $question_key[$key], $pdo);
					}
				}
			}

// Now, insert into the junction tables involving "agency"
			foreach ($agency_key as $agency) {

				if (isset($filename_key) && $filename_key != "") {
					$oops_query[] = junctioninsert("filename", "agency", $filename_key, $agency, $pdo);
				}

				if(isset($question_key) && $question_key[0] != "") {
					foreach ($question_key as $key => $value) {
						$oops_query[] = junctioninsert("question", "agency", $value, $agency, $pdo);
					}
				}

				$oops_query[] = junctioninsert("quote", "agency", $quote_key, $agency, $pdo);

				if (isset($report_key) && $report_key != "") {
					$oops_query[] = junctioninsert("report", "agency", $report_key, $agency, $pdo);
				}

			}
			if (isset($author_key) && count($author_key) > 0) {

				foreach ($author_key as $author) {

					$oops_query[] = junctioninsert("filename", "author", $filename_key, $author, $pdo);
				}
			}
			foreach ($publisher_key as $publisher) {

				$oops_query[] = junctioninsert("filename", "publisher", $filename_key, $publisher, $pdo);
			}
			foreach ($country_key as $country) {

				if (isset($filename_key) && $filename_key != "") {
					$oops_query[] = junctioninsert("filename", "country", $filename_key, $country, $pdo);
				}

				if(isset($question_key) && $question_key[0] != "") {
					foreach ($question_key as $key => $value) {
						$oops_query[] = junctioninsert("question", "country", $value, $country, $pdo);
					}
				}

				$oops_query[] = junctioninsert("quote", "country", $quote_key, $country, $pdo);

				if (isset($report_key) && $report_key != "") {
					$oops_query[] = junctioninsert("report", "country", $report_key, $country, $pdo);
				}
			}
			foreach ($program_key as $program) {

				if (isset($filename_key) && $filename_key != "") {
					$oops_query[] = junctioninsert("filename", "program", $filename_key, $program, $pdo);
				}

				if(isset($question_key) && $question_key[0] != "") {
					foreach ($question_key as $key => $value) {
						$oops_query[] = junctioninsert("question", "program", $value, $program, $pdo);
					}
				}

				$oops_query[] = junctioninsert("quote", "program", $quote_key, $program, $pdo);

				if (isset($report_key) && $report_key != "") {
					$oops_query[] = junctioninsert("report", "program", $report_key, $program, $pdo);
				}
			}
			foreach ($tag_key as $tag) {

				if (isset($filename_key) && $filename_key != "") {
					$oops_query[] = junctioninsert("filename", "tag", $filename_key, $tag, $pdo);
				}

				if(isset($question_key) && $question_key[0] != "") {
					foreach ($question_key as $key => $value) {
						$oops_query[] = junctioninsert("question", "tag", $value, $tag, $pdo);
					}
				}

				$oops_query[] = junctioninsert("quote", "tag", $quote_key, $tag, $pdo);

				if (isset($report_key) && $report_key != "") {
					$oops_query[] = junctioninsert("report", "tag", $report_key, $tag, $pdo);
				}
			}
			if (isset($filename_key) && $filename_key != "") {

				if(isset($question_key) && $question_key[0] != "") {
					foreach ($question_key as $key => $value) {
						$oops_query[] = junctioninsert("filename", "question", $filename_key, $value, $pdo);
					}
				}
			}

			if(isset($question_key) && $question_key[0] != "") {
				foreach ($question_key as $key => $value) {
					$oops_query[] = junctioninsert("quote", "question", $quote_key, $value, $pdo);
				}
			}

			if (isset($report_key) && $report_key != "") {

				if(isset($question_key) && $question_key[0] != "") {
					foreach ($question_key as $key => $value) {
						$oops_query[] = junctioninsert("report", "question", $report_key, $value, $pdo);
					}
				}
			}

// Let's associate contacts with countries, programs, tags via the questions to be asked of them (don't bother with shares)
			if((isset($question_key) && $question_key[0] != "") && isset($_POST['quescon']['nickname']) && $_POST['quescon']['nickname'][0] != "") {
				foreach ($question_key as $key => $value) {
					$row = getquestioncontact("country", $value, $pdo);

					do {
						$oops_query[] = junctioninsert("contact", "country", $row[0]['contact_key'], $row[0]['country_key'], $pdo);
					}
					while($row[0] = $row[1]->fetch(PDO::FETCH_ASSOC));

					$row = getquestioncontact("program", $value, $pdo);

					do {
						$oops_query[] = junctioninsert("contact", "program", $row[0]['contact_key'], $row[0]['program_key'], $pdo);
					}
					while($row[0] = $row[1]->fetch(PDO::FETCH_ASSOC));

					$row = getquestioncontact("tag", $value, $pdo);

					do {
						$oops_query[] = junctioninsert("contact", "tag", $row[0]['contact_key'], $row[0]['tag_key'], $pdo);
					}
					while($row[0] = $row[1]->fetch(PDO::FETCH_ASSOC));
				}
			}

			try {

				$pdo->commit();
				$oo = implode("-----", $oops_query);
				$ct_query = "SELECT COUNT(*) FROM quote";
								
					if(!$ct_result = $pdo->query($ct_query))	{
						die('There was an error running the query [' . $pdo->error . ']');
					}

				$ct_row = $ct_result->fetch(PDO::FETCH_ASSOC);
				$snn = $ct_row['COUNT(*)'];

				$sss = strlen($snn);
				$sst = substr($snn, $sss-1, 1);
				$ssd = substr($snn, $sss-2, 1);

				if ($sst == "4" || $sst == "5" || $sst == "6" || $sst == "7" || $sst == "8" || $sst == "9" || $sst == "0") {
					$nth = "th";
				}
				if ($sst == "1") {
					if ($ssd == "1") {
						$nth = "th";
					}
					else {
						$nth = "st";
					}
				}
				if ($sst == "2") {
					if ($ssd == "1") {
						$nth = "th";
					}
					else {
						$nth = "nd";
					}
				}

				if ($sst == "3") {
					if ($ssd == "1") {
						$nth = "th";
					}
					else {
						$nth = "rd";
					}
				}
				$bbb = substr($_POST['data_enterer'], 0, $bbb);
				$shortquote = substr($_POST['quote'], 0, 140);
				$shortquote = $shortquote . "&hellip;";

				echo <<<_END

				<p>Thank you, $bbb. You've just entered the $snn$nth clip in the database.</p>

				<form id="oopsentry" name="oopsentry">
				<input id="oopsdelete" name="oopsdelete" type="hidden" value="$oo">
				<input id="nth" name="nth" type="hidden" value="$snn$nth">
				<input id="yrname" name="yrname" type="hidden" value="$bbb">

				<p><strong>Wait, no, delete this:</strong> 
				<input type="submit") value="Delete"></p></form>
				<div id="whatyouentered">
				&ldquo;$shortquote&rdquo;<br><br>
_END;
				if (isset($_POST['author']) && $_POST['author'] != "") {
					echo "$_POST[author], ";
				}

				echo "&ldquo;$_POST[cite_title]&rdquo; (";

				if (isset($_POST['city']) && $_POST['city'] != "") {
					echo "$_POST[city]: ";
				}

				if (isset($_POST['publisher']) && $_POST['publisher'] != "") {
					echo "$_POST[publisher], ";
				}

				$pubdate = new DateTime($_POST['date']);
				$pubdate = $pubdate->format("F j, Y");
				echo "$pubdate)";

				if (isset($_POST['page_num']) && $_POST['page_num'] != "") {
					echo ": $_POST[page_num]";
				}

				if (isset($_POST['url']) && $_POST['url'] != "") {
					echo " &lt;$_POST[url]&gt;";
				}

				echo ".<br><br>";

				if (isset($_POST['prisec']) && $_POST['prisec'] != "") {
					if ($_POST['prisec'] == 1) {
						echo "Primary source / ";
					}
					else {
						echo "Secondary source / ";
					}
				}

				if (isset($_POST['language']) && $_POST['language'] != "") {
					if ($_POST['language'] == "English") {
						echo "English / ";
					}
					if ($_POST['language'] == "Spanish") {
						echo "Spanish / ";
					}
				}

				if (isset($_POST['is_audio']) && $_POST['is_audio'] != "") {
					switch ($_POST['is_audio']) {
						case '1':
							echo "Audio file";
							break;
						case '2':
							echo "Image file";
							break;
						case '0':
							echo "Text file";
							break;
					}
				}

				echo "<br><br>Countries: $_POST[country]<br>
					Aid Programs: $_POST[program]<br>
					Tags: $_POST[tag]<br>
					U.S. Agencies: $_POST[agency]";

				if (isset($_POST['report_name']) && $_POST['report_name'] != "") {
					echo "<br><br>Instance of &ldquo;$_POST[report_name]&rdquo;</div>";
				}
			}
			catch (PDOException $e) {

				$pdo->rollBack();
				echo "<Error performing the transaction.";
				exit();
			}
		}
		else {
			echo "Come on, you didn't even enter a clip text.";

		}
	}
}

function simplequery($allfields, $datatable, $post) {

	$allfields = explode(", ", $allfields);
	$usedfields = [];
	$usedvalues = [];

	foreach ($allfields as $key => $value) {

		if (isset($post[$value]) && $post[$value] != "") {
			$usedfields[] = $value;
			$usedvalues[] = sanitizeString($post[$value]);
		}
	}

	$usedfield2 = implode(", ", $usedfields);
	$placeholders = implode(", :", $usedfields);

	$qi = ("INSERT INTO $datatable ($usedfield2) VALUES (:$placeholders);");
	$qii = [];
	$qiii = [];

	foreach ($usedvalues as $key => $value) {
			
		$qii[] = $usedfields[$key];
		$qiii[] = $value;
	}

	$returns = array($qi, $qii, $qiii);

	return($returns);
}

function anotherSimple($p, $a, $pdo) {
	$stmt = $pdo->prepare($p[0]);

	foreach($p[2] as $key=>$value) {
		$b = $p[1][$key];
		$stmt->bindValue(":$b", $value);
	}
	
	$stmt->execute();

	$q = $pdo->lastInsertId();
	$r = "DELETE FROM ". $a . " WHERE " . $a . "_key = " . $q;
	return(array($q, $r));
}
function findquery($f, $p) {
	return("SELECT " . $f . "_key FROM $f WHERE $f = '" . $p . "'");
}

function fieldtoarray($f) {
	$a = preg_replace("/, +?/", ",", $f);
	$a = explode(",",$a);
	return $a;
}

function junctioninsert($main, $sat, $mainkey, $satkey, $pdo) {
	$stmt = $pdo->prepare("INSERT INTO " . $main . "_" . $sat . " (" . $sat . "_key, " . $main . "_key) VALUES (:" . $sat . "_key, :" . $main . "_key);");
	$s = $sat . "_key";
	$m = $main . "_key";
	$stmt->bindValue(":$s", $satkey);
	$stmt->bindValue(":$m", $mainkey);			
	$stmt->execute();
	$lid = $pdo->lastInsertId();
	return "DELETE FROM " . $main . "_" . $sat . " WHERE " . $main . "_" . $sat . "_key = " . $lid;
}

function getquestioncontact($q, $qk, $pdo) {

	$query = "SELECT contact_question.contact_key
					, question_" . $q . "." . $q . "_key 
					FROM contact_question 
					INNER JOIN question_" . $q .  
					" ON contact_question.question_key = question_" . $q . ".question_key
 					WHERE contact_question.question_key = '$qk'";

	if(!$result = $pdo->query($query))	{
		die('There was an error running the query [' . $pdo->error . ']');
	}

	$j[] = $result->fetch(PDO::FETCH_ASSOC);
	$j[] = $result;
	return $j;
}

function acpt($a, $b, $pdo) {
	$stmt = $pdo->prepare("INSERT INTO $a ($a) VALUE (:$a);");
	$stmt->bindValue(":$a", $b);
	$stmt->execute();

	$p = $pdo->lastInsertId();
	$q = "DELETE FROM " . $a . " WHERE " . $a . "_key = " . $p;

	return(array($p, $q));
}
function questionsKeys($a, $pdo) {
	$stmt = $pdo->prepare("INSERT INTO question (question_text) VALUE (:question_text);");
	$stmt->bindValue(':question_text', $a);
	$stmt->execute();

	$p = $pdo->lastInsertId();
	$q = "DELETE FROM question WHERE question_key = " . $p;
	return(array($p, $q));
}
function contactsKeys($a, $pdo) {

	$stmt = $pdo->prepare("INSERT INTO contact (nickname) VALUE (:nickname);");
	$stmt->bindValue(':nickname', $a);
	$stmt->execute();

	$p = $pdo->lastInsertId();
	$q = "DELETE FROM contact WHERE contact_key = " . $p;
	return(array($p, $q));
}


?>