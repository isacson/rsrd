<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';

if ((isset($_GET['table']) && $_GET['table'] != "") && (isset($_GET['b1']) && $_GET['b1'] != "") && (isset($_GET['b2']) && $_GET['b2'] != "") && (isset($_GET['val']) && $_GET['val'] != "")) {

	if($_GET['val'] == 1) {

		$query = "UPDATE $_GET[table] SET $_GET[table] = '$_GET[b2]' WHERE $_GET[table] = '$_GET[b1]';";
		$stmt = $pdo->prepare($query);
		$result = $stmt->execute();

		if(!$result = $pdo->query($query))	{
			die('There was an error running the query [' . $pdo->error . ']');
		}
		else {
			echo "You have replaced the $_GET[table] &ldquo;$_GET[b1]&rdquo; with &ldquo;$_GET[b2].&rdquo;";
		}
	}

	if($_GET['val'] == 2) {

		$cat = $_GET['table'];
		$catkey = $cat . "_key";

		$in_query = "SELECT " . $catkey . " FROM " . $cat . " WHERE " . $cat . " = '" . $_GET['b2'] . "';";
		$in_stmt = $pdo->prepare($in_query);
		$in_result = $in_stmt->execute();

		if(!$in_result = $pdo->query($in_query))	{
			die('There was an error running the in-out query [' . $pdo->error . ']');
		}
		else {
			$in_row = $in_result->fetch(PDO::FETCH_ASSOC);
			$in_key = $in_row[$catkey];
		}

		$out_query = "SELECT " . $catkey . " FROM " . $cat . " WHERE " . $cat . " = '" . $_GET['b1'] . "';";
		$out_stmt = $pdo->prepare($out_query);
		$out_result = $out_stmt->execute();

		if(!$out_result = $pdo->query($out_query))	{
			die('There was an error running the out-in query [' . $pdo->error . ']');
		}
		else {
			$out_row = $out_result->fetch(PDO::FETCH_ASSOC);
			$out_key = $out_row[$catkey];
		}

		$filename_query = "UPDATE filename_" . $cat . " SET $catkey = '" . $in_key . "' WHERE $catkey = '" . $out_key . "';";
		$filename_stmt = $pdo->prepare($filename_query);
		$filename_result = $filename_stmt->execute();

		if(!$filename_result = $pdo->query($filename_query))	{
			die('There was an error running the filename query [' . $pdo->error . ']');
		}

		$question_query = "UPDATE question_" . $cat . " SET $catkey = '" . $in_key . "' WHERE $catkey = '" . $out_key . "';";
		$question_stmt = $pdo->prepare($question_query);
		$question_result = $question_stmt->execute();

		if(!$question_result = $pdo->query($question_query))	{
			die('There was an error running the question query [' . $pdo->error . ']');
		}

		$quote_query = "UPDATE quote_" . $cat . " SET $catkey = '" . $in_key . "' WHERE $catkey = '" . $out_key . "';";
		$quote_stmt = $pdo->prepare($quote_query);
		$quote_result = $quote_stmt->execute();

		if(!$quote_result = $pdo->query($quote_query))	{
			die('There was an error running the quote query [' . $pdo->error . ']');
		}

		$report_query = "UPDATE report_" . $cat . " SET $catkey = '" . $in_key . "' WHERE $catkey = '" . $out_key . "';";
		$report_stmt = $pdo->prepare($report_query);
		$report_result = $report_stmt->execute();

		if(!$report_result = $pdo->query($report_query))	{
			die('There was an error running the report query [' . $pdo->error . ']');
		}

		$del_query = "DELETE FROM " . $cat . " WHERE $catkey = '" . $out_key . "';";
		$del_stmt = $pdo->prepare($del_query);
		$del_result = $del_stmt->execute();

		if(!$del_result = $pdo->query($del_query))	{
			die('There was an error running the delete query [' . $pdo->error . ']');
		}
		echo "OK, $_GET[b1] has been replaced with $_GET[b2].";
	}
}

$pdo = null;

?>