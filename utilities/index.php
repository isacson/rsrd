<?php 

require_once $_SERVER["DOCUMENT_ROOT"] . '/header.php';

echo "<main>";

if ($loggedin != TRUE) {
	echo "<p class='intro_text'>Sorry, this page is for WOLA staff given a username and password. If you have those, please click &ldquo;Login&rdquo; above.</p>";
}
else {
echo "<h3 class='area_title'>Utilities</h3>";

echo "<p class='intro_text'>Here are some tools to make quick changes to the database. <span style='color:red; font-weight:bold;''>Do not use this page unless you have just backed up a copy of the database. If you don&rsquo;t know how to do that, stop now and ask for help.</span></p>";

echo <<<_END

<form id="cat_replace">

<h3>Rename or Replace a Category</h3>

<div>Wherever the 

<select id="replace_cat">
  <option value="country">country</option>
  <option value="program">program</option>
  <option value="tag" selected>tag</option>
  <option value="agency">government agency</option>
</select> 

named 

<input id="replace_blank1" type="text"> 

appears in the database, 

<select id="replace_cat_txt">
  <option value="rename">rename it as</option>
  <option value="replace" selected>replace it with</option>
</select>

<input id="replace_blank2" type="text">.

<button id="cat_replace_butt" value="Go">Go</button>

</div>

</form>

<div id="replace_cat_msg"></div>

_END;
}

echo "</main>";

echo "<script src='/js/utilities.js'></script>";

require_once $_SERVER["DOCUMENT_ROOT"] . '/footer.php';

?>