<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

$cqcount_query = 

	"SELECT 	c.country
					, cq.country_key
					, COUNT(cq.country_key)
				FROM country AS c
				INNER JOIN quote_country AS cq
					ON c.country_key = cq.country_key
				GROUP BY cq.country_key";

$msq = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if($msq->connect_errno > 0){
    die('Unable to connect to database [' . $msq->connect_error . ']');
}
if(!$result = $msq->query($cqcount_query)){
    die('There was an error running the country key query [' . $msq->error . ']');
}

echo "<ul>";

while($row = $result->fetch_assoc()) {

	echo "<li>" . $row['country'] . " (" . $row['COUNT(cq.country_key)'] . ")</li>";
}

echo "</ul>";

$msq->close();

?>