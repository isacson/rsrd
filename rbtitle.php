<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";

//$country = $program = $tag = $agency = "";

$allresults = $_POST['allresults'];
$page = $_POST['page'];
$totalpages = $_POST['totalpages'];
$pgbeg = $_POST['pgbeg'];
$pgend = $_POST['pgend'];
$perpage = $_POST['perpage'];
$pageminusone = $_POST['pageminusone'];
$pageplusone = $_POST['pageplusone'];

echo "<strong>Page $page of $totalpages</strong> ($pgbeg to $pgend of <span id='allresults'>$allresults</span> clips)<div id='rbtitnav'>";

if ($page != 1) {
	echo  "<a onClick='paginateLink(1, $perpage, $allresults);'>&lt;&lt; First Page</a> &mdash; <a onClick='paginateLink($pageminusone, $perpage, $allresults);'>&lt; Previous Page</a> &mdash; ";
}

	echo  "<form id='gotopagetop' class='paginationform' name='gotopagetop'>Go To Page <input type='text' id='getpagea' name='getpagea' size='4'> <button type='submit' onClick='event.preventDefault(); paginateButta(";

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


echo  "<form id='clipsperpagetop' class='paginationform' name='clipsperpagetop'>Clips Per Page <input type='text' id='getclipspagea' name='getclipspagea' size='4'> <button type='submit' onClick='event.preventDefault(); paginateButta(";

	if ($loggedin === TRUE) {
		echo  "\"zaxxon\"";
	}
	else {
		echo "0";
	}

echo  ", $allresults);'>Go</button>";


if ($loggedin == FALSE) {
	echo  " (maximum 50)";
}

echo " &nbsp;<span id='rbview'><span id='rbviewas' onclick='paginateButta(";

if ($loggedin === TRUE) {
		echo  "\"zaxxon\"";
	}
else {
	echo "0";
}

echo  ", $allresults, 1)'>View</span>, or ";

echo "<span id='rbdlas' onclick='paginateButta(";

if ($loggedin === TRUE) {
		echo  "\"zaxxon\"";
	}
else {
	echo "0";
}

echo  ", $allresults, 2, querytext)'>download</span>, this page as plain text</span></form>";

echo  "</div>";

$pdo = null;
?>