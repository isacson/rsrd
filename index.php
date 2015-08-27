<?php 

require_once $_SERVER["DOCUMENT_ROOT"] . '/header.php';

echo "<script src='/js/dropzone.min.js'></script>";

echo "<main>";

if ($loggedin === TRUE) {
	echo "<div class='heydata'><strong>Hello WOLA staff member!</strong><br><span id='useasearchform' onclick='snipEntry()'>Perhaps you&rsquo;d like to enter data?</span></div>";
}

echo "<h3 class='area_title'>Data Clips</h3>";

echo "<p class='intro_text'>We use this site for note-taking about U.S. defense and security relations with Latin America and the Caribbean.</p>";

echo <<<_END

	<div class="browse_search">

	<div id="browsebutton" class="browse_search_butt_sel">
		Browse
	</div>

	<div id="searchbutton" class="browse_search_butt_unsel">
		Search
	</div>

	</div>

	<form id="mainsearchform" name="mainsearchform"><input id="mainsearch" name="mainsearch" type="text" tabindex="1" maxlength="100" 

_END;

if (isset($loggedin) && $loggedin === TRUE) {

	echo " style='width:350px;' ";
}

echo <<<_END

	placeholder="Quick Clips Search"><button type="submit" id="mainsearchbutt" tabindex="2" value="go">Go</button></form>
	
	<div id="leftbrowse">
		<div id="lbtitle">
			<div id="lbtitletext">
			</div>
		</div>
		<div id="lbcontent">
		</div>
	</div>
	<div id="rightbrowse">
		<div id="rbtitle">
		</div>
		<div id="rbcheckbox">
			<fieldset id="prisecholder">
				<legend>Source Type</legend>
				<input type="checkbox" id="primarysource" onclick="checkUrl()" checked>
					<label for="primarysource">Primary</label>
					<br>
				<input type="checkbox" id="secondarysource" onclick="checkUrl()" checked>
					<label for="secondarysource">Secondary</label>
			</fieldset>
			<fieldset id="languageholder">
				<legend>Language</legend>
				<input type="checkbox" id="english" onclick="checkUrl()" checked>
					<label for="english">English</label>
					<br>
				<input type="checkbox" id="spanish" onclick="checkUrl()" checked>
					<label for="spanish">Spanish</label>
			</fieldset>
			<fieldset id="audioholder">
				<legend>Data Type</legend>
				<table border="0" cellpadding="0" cellspacing="0"><tr><td>
				<input type="checkbox" id="isaudio" onclick="checkUrl()" checked>
					<label for="isaudio">Audio</label></td><td>&nbsp;</td><td>
				<input type="checkbox" id="isimage" onclick="checkUrl()" checked>
					<label for="isimage">Image</label></td></tr><tr><td>
				<input type="checkbox" id="istext" onclick="checkUrl()" checked>
					<label for="istext">Text</label></td><td>&nbsp;</td><td>&nbsp;</td></tr></table>
			</fieldset>
			<fieldset id="importantholder">
				<legend>Importance</legend>
				<input type="checkbox" id="important" onclick="checkUrl()">
					<label for="important">Clips we view as important</label>
			</fieldset>
		</div>
		<div id="rbcontent">
		</div>
	</div>

_END;

echo "</main>";

echo "<script src='/js/snipp.js'></script>";

require_once $_SERVER["DOCUMENT_ROOT"] . '/footer.php';

?>