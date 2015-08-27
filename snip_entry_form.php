<?php

	echo <<<_END

	<h3 class="area_title">Data Clips Entry Form</h3>

	<p class="intro_text">Upload a document, audio file, or image. Then use this form to add bits of information from it ("clips") that are of interest to our work. <a href='/'><br>Or just return to the Data Clips page</a>.</p>

	<form id="fileuploadform" class="dropzone" enctype="multipart/form-data">
 	<div id="primarydocsuploadresult">If your source document isn&rsquo;t in the &ldquo;primarydocs&rdquo; folder yet,<br>either drop it here or click here to upload it.</div>
	</form>


	<form id="snipentry" name="snipentry">

	<div><span style="color:navy; font-weight:bold;">Start Here &rArr; </span>Who are you? <input id="data_enterer" name="data_enterer" type="text" tabindex="1" size="20" placeholder="identify yourself" autocomplete="off"><span id="data_enterer_msg" class="msgtext"></span><button type="reset" value="Reset" id="snipentry_reset" onclick="snipEntryReset(event);">Clear this form</button></div>

	<div id="snipimgholder"></div>

	<div>Paste the text of the quote<br>
	<textarea id="quote" name="quote" rows="5" cols="90" tabindex="5" placeholder="try to clean up weird line breaks"></textarea></div>
	<div id="quote_msg" class="msgtext"></div>

	<div>Filename of the source document<br>
	<input id="filename" name="filename" type="text" tabindex="10" size="90" placeholder="as it&rsquo;s named in the &ldquo;primarydocs&rdquo; folder"></div>
	<div id="filename_msg" class="msgtext"></div>

	<div>Title of the source document<br>
	<input id="cite_title" name="cite_title" type="text" tabindex="15" size="90" placeholder="be exact, even if it&rsquo;s a long title"></div>
	<div id="cite_title_msg" class="msgtext"></div>

	<div>The web address (URL) of the source document<br>
	<input id="url" name="url" type="text" tabindex="20" size="90" placeholder="if it&rsquo;s available online somewhere else&mdash;otherwise leave this blank"></div>
	<div id="url_msg" class="msgtext"></div>

	<div>Name(s) of the author(s)<br>
	<input id="author" name="author" type="text" tabindex="25" size="90" placeholder="if there&rsquo;s more than one author, separate the names with commas"></div>
	<div id="author_msg" class="msgtext"></div>	

	<div>Publisher of the source document<br>
	<input id="publisher" name="publisher" type="text" tabindex="30" size="90" placeholder="a government agency? a periodical?"></div>
	<div id="publisher_msg" class="msgtext"></div>	

	<div>The page number <input id="page_num" name="page_num" type="text" tabindex="35" size="10" placeholder="(if a PDF)"> The city of publication <input id="city" name="city" type="text" tabindex="40" size="29" placeholder="just type &ldquo;w&rdquo; for Washington"></div>

	<div>The publication date <input id="date" name="date" type="text" tabindex="40" size="15" placeholder="yyyy-mm-dd">
	<div id="date_msg" class="msgtext"></div></div>

	<div id="report_box">

	<div>The report name<br>
	<input id="report_name" name="report_name" type="text" tabindex="41" size="90" placeholder="this will fill automatically if filename is recognized"></div>
	<div id="report_name_msg" class="msgtext"></div>	

	<div>Description of the report<br>
	<textarea id="report_desc" name="report_desc" rows="2" cols="90" tabindex="42" placeholder="this will fill automatically if filename is recognized"></textarea></div>

	<div>Name of the law requiring the report<br>
	<input id="law" name="law" type="text" tabindex="43" size="90" placeholder="this will fill automatically if filename is recognized"></div>
	<div id="law_msg" class="msgtext"></div>	

	<div>Section of that law <input id="section" name="section" type="text" tabindex="44" size="10" placeholder=""> Date the report is due <input id="report_due" name="report_due" type="text" tabindex="45" ize="15" placeholder="yyyy-mm-dd">
	<div id="report_due_msg" class="msgtext"></div>	</div>

	<div><input id="read_it" name="read_it" "tabindex="46" type="checkbox" value="1"> Have we read through the report yet?</div>

	</div>

	<fieldset id="prisec_radio"><legend>Source type</legend><input type="radio" id="prisec1" name="prisec" tabindex="47" value="1" checked>Primary<br><input type="radio" id="prisec0" name="prisec" tabindex="50" value="0">Secondary</fieldset>

	<fieldset id="lang_radio"><legend>Language</legend><input type="radio" id="language_en" name="language" tabindex="55" value="English" checked>English<br><input type="radio" id="language_sp" name="language" tabindex="60" value="Spanish">Spanish</fieldset>

	<fieldset id="audio_radio" style="margin-right:50px;"><legend>Data Type</legend><input type="radio" id="is_audio_y" name="is_audio" tabindex="65" value="1">Audio<br><input type="radio" id="is_audio_n" name="is_audio" tabindex="70" value="0" checked>Text</fieldset>

	<div><br><br><br><br>About what countries<br>
	<input id="country" name="country" type="text" tabindex="75" size="90" placeholder="relevant countries separated by commas"></div>
	<div id="country_msg" class="msgtext"></div>	

	<div>About what aid programs<br>
	<input id="program" name="program" type="text" tabindex="80" size="90" placeholder="relevant programs separated by commas"></div>
	<div id="program_msg" class="msgtext"></div>	

	<div>Tags that make sense<br>
	<input id="tag" name="tag" type="text" tabindex="85" size="90" placeholder="tags separated by commas"></div>
	<div id="tag_msg" class="msgtext"></div>	

	<div>About what government agencies<br>
	<input id="agency" name="agency" type="text" tabindex="90" size="90" placeholder="relevant government agencies separated by commas"></div>
	<div id="agency_msg" class="msgtext"></div>	

	<div>Should we share this with anyone?<br>
	<input id="share" name="share" type="text" tabindex="92" size="90" placeholder="first and last names separated by commas"></div>
	<div id="share_msg" class="msgtext"></div>	

	<div id="quescondiv0"><div>A question raised by this clip<br>
	<textarea id="quescon_question_text0" class="quesclass" onBlur="quesCheck(this);" name="quescon[question_text][]" rows="2" cols="90" tabindex="95" placeholder="whatever we want to know"></textarea></div>
	<div id="quescon0_msg" class="msgtext"></div>

	<div>Who should we ask?<br>
	<input id="quescon_nickname0" name="quescon[nickname][]" type="text" class="quesnick" tabindex="100" size="70" onBlur="nickCheck(this);" placeholder="each official&rsquo;s full name separated by commas"><button type="button" id="quescon_button" tabindex="102" onclick="addQuescon();">Add another question</button></div>
	<div id="quescon_nick0_msg" class="msgtext"></div>
	</div>

	<div><input id="important" name="important" tabindex="1000" type="checkbox" value="1"> This seems like an especially important clip</div>
	<div><span style="color:navy; font-weight:bold;">Finish Here &rArr; </span><input type="submit" id="snipEntrySubmit" name="snipEntrySubmit" value="Submit this" tabindex="1010"></div>

	<input id="formused" name="formused" type="hidden" value="1">

	</form>

	<script src='/js/snipp_entry.js'></script>

_END;

?>