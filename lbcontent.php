<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

$choosers = array("country", "program", "tag", "agency");

$filters = array();

// make where clause of all categories that aren't ""

$wheregets = "";

for ($i=0; $i < count($choosers); $i++) {
	if (isset ($_POST[$choosers[$i]]) && $_POST[$choosers[$i]] != "") {
		$filters[] = [$choosers[$i] => $_POST[$choosers[$i]]];
	}
}

if (count($filters) > 0) {

	$wheregets .= " WHERE ";
}

for ($i=0; $i < count($filters); $i++) { 
	$key = key($filters[$i]);
	$wheregets .= " $key" . "." . $key . " = '" . $filters[$i][$key] . "' ";
	if ($i < count($filters)-1) {
		$wheregets .= " AND ";
	}
}

$gets = [];

if (isset($_POST['prisec']) && ($_POST['prisec'] != "")) {

	$gets['prisec'] = $_POST['prisec'];
}

if (isset($_POST['language']) && ($_POST['language'] != "")) {

	$gets['language'] = $_POST['language'];
}

if (isset($_POST['important']) && ($_POST['important'] == "1")) {

	$gets['important'] = $_POST['important'];
}

if ($wheregets == "" && count($gets) > 0) {
	$wheregets .= " WHERE ";
}
elseif ($wheregets != "" && count($gets) > 0) {
		$wheregets .= " AND ";
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

if (isset($_POST['quotequery']) && $_POST['quotequery'] != "") {
	if ($wheregets == "") {
		$wheregets .= " WHERE ";
	}
	else {
		$wheregets .= " AND ";
	}
	$wheregets .= $_POST['quotequery'];
}

// pick out the categories that are ""

$blanks = array();

for ($i=0; $i < count($choosers); $i++) {
	if (isset ($_POST[$choosers[$i]]) && $_POST[$choosers[$i]] == "") {
		$blanks[] = [$choosers[$i] => $_POST[$choosers[$i]]];
	}
}

echo "{\n
		\"category\": [\n";

for ($i=0; $i < count($blanks) ; $i++) { 

	$open_item = key($blanks[$i]);

	$query = "SELECT " . $open_item . "." . $open_item . 
				", COUNT(quote_" . $open_item . "." . $open_item . "_key) FROM " . $open_item . 
				" INNER JOIN quote_" . $open_item . " ON " . $open_item . "." . $open_item . "_key = quote_" . $open_item . "." . $open_item . "_key ";

				for ($j=0; $j < count($filters) ; $j++) { 
				
					$search_item = key($filters[$j]);

					$query .= " INNER JOIN quote_" . $search_item . 
					" ON quote_" . $open_item . ".quote_key = 
					quote_" . $search_item . ".quote_key";

					$query .= " INNER JOIN " . $search_item . " ON quote_" . $search_item . "." . $search_item . "_key = " . $search_item . "." .
						$search_item . "_key ";
				}

				$query .= " INNER JOIN quote on quote_" . $open_item . ".quote_key = quote.quote_key 
							INNER JOIN filename ON filename.filename_key = quote.filename_key ";

				$query .= $wheregets . " GROUP BY quote_". $open_item . "." . $open_item . "_key ORDER BY COUNT(quote_" . $open_item . "." . $open_item . "_key) DESC, $open_item ";

//echo $query;

	echo "{\n
			\"name\": \"" . $open_item . "\",\n
			\"show\": \"";

		switch ($open_item) {
			case 'country':
				echo "Countries";
				break;
			case 'program':
				echo "Programs";
				break;
			case 'tag':
				echo "Tags";
				break;
			case 'agency':
				echo "Government Agencies";
				break;
		}

	echo "\",\n
			\"items\": [\n";

	$stmt = $pdo->prepare($query);
	$result = $stmt->execute();

	if(!$result = $pdo->query($query))	{
    	die('There was an error running the items query [' . $pdo->error . ']');
	}

	$value = $result->fetch(PDO::FETCH_ASSOC);
	$rows = $stmt->fetchAll();
	$cnt = count($rows);
	$j=1;

	if ($value != NULL) {

		do { 		
			$buh = key($value);
			$num = "COUNT(quote_" . $open_item . "." . $open_item . "_key)";

			echo "{\n
					\"id\": \"" . $value[$buh] . "\",\n
				\"occurs\": \"" . $value[$num] . "\"}";

			if ($j < $cnt) {
				echo ",\n";
			}
			$j++;
		}
		while($value = $result->fetch(PDO::FETCH_ASSOC));
	}

	echo "\n]}";

	if ($i < count($blanks)-1) {

		echo ",";
	}
}

echo "\n]}";

$pdo = null;

?>