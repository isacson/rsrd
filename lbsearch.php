<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/functions.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/session_stuff.php";

echo <<<_END

<form id="snipsearch" name="snipsearch">

<p><button type="reset" value="Reset" id="snipsearch_reset" onclick="snipSearchReset(event);">Clear Form</button>Word(s) in the <strong>clip text</strong><br>
<input id="quotesearch" name="quotesearch" type="text" tabindex="5" maxlength="100" placeholder="phrases in quotes; OK to use &ldquo;and,&rdquo; &ldquo;or,&rdquo; &ldquo;not&rdquo;"></p>

<hr>

<p>About 

<select id="country_all_any" tabindex="10">
  <option value="all">all</option>
  <option value="any">any</option>
</select> 

of the following <strong>countries</strong>: 

<input id="country_include" type="text" tabindex="15" size="30" maxlength="100" placeholder="separate countries with commas">, but not <input id="country_not" type="text" tabindex="20" size="30" maxlength="100" placeholder="separate countries with commas">.
<div id="country_msg"></div>

</p>

<p>About 

<select id="program_all_any" tabindex="25">
  <option value="all">all</option>
  <option value="any">any</option>
</select> 

of the following <strong>aid programs</strong>: 

<input id="program_include" type="text" tabindex="30" size="30" maxlength="100" placeholder="separate programs with commas">, but not <input id="program_not" type="text" tabindex="35" size="30" maxlength="100" placeholder="separate programs with commas">.
<div id="program_msg"></div>

</p>

<p>About 

<select id="tag_all_any" tabindex="40">
  <option value="all">all</option>
  <option value="any">any</option>
</select> 

of the following <strong>tags</strong>: 

<input id="tag_include" type="text" tabindex="45" size="30" maxlength="100" placeholder="separate tags with commas">, but not <input id="tag_not" type="text" tabindex="50" size="30" maxlength="100" placeholder="separate tags with commas">.
<div id="tag_msg"></div>

</p>

<p>Involving 

<select id="agency_all_any" tabindex="55">
  <option value="all">all</option>
  <option value="any">any</option>
</select> 

of the following <strong>government agencies</strong>: 

<input id="agency_include" type="text" tabindex="60" size="30" maxlength="100" placeholder="separate agencies with commas">, but not <input id="agency_not" type="text" tabindex="65" size="30" maxlength="100" placeholder="separate agencies with commas">.
<div id="agency_msg"></div>

</p>

<hr>

<p><strong>Published</strong> between <input id="clipdate1" name="clipdate1" type="text" tabindex="70" size="15" placeholder="yyyy-mm-dd"> and <input id="clipdate2" name="clipdate2" type="text" tabindex="70" size="15" placeholder="yyyy-mm-dd">.
<div id="clipdate_msg"></div>
</p>

<hr>

<p>By the following <strong>author(s)</strong>: 

<input id="author_include" type="text" tabindex="75" maxlength="100" placeholder="separate authors with commas">
<div id="author_msg"></div>

</p>

<p>Any words in the <strong>title</strong>: 

<input id="cite_title_words" type="text" tabindex="80" maxlength="100" placeholder="start typing and choose one of the titles that appear">
<div id="cite_title_words_msg"></div>

</p>

<p>From the <strong>government report</strong> entitled: 

<input id="report_title_words" type="text" tabindex="85" maxlength="100" placeholder="start typing and choose one of the titles that appear">
<div id="report_title_words_msg"></div>

</p>

<p>Fragment of the <strong>law</strong> requiring the report:

<input id="law_words" type="text" tabindex="90" maxlength="100" placeholder="start typing and choose one of the laws that appear">
<div id="law_words_msg"></div>

</p>

<hr>

_END;

if ($loggedin == TRUE) {

	echo "<p>Data entered by  

	<select id='data_enterer_all_any' tabindex='95'>
	  <option value='all'>all</option>
	  <option value='any'>any</option>
	</select> 

	of the following <strong>colleagues</strong>: 

	<input id='data_enterer_include' type='text' tabindex='100' size='30' maxlength='100' placeholder='separate names with commas'>, but not <input id='data_enterer_not' type='text' tabindex='105' size='30' maxlength='100' placeholder='separate names with commas'>.
	<div id='data_enterer_msg'></div>

	</p>";

}

echo "</form>";

echo "<script src='/js/snipsearch.js'></script>";

?>