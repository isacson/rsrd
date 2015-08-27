function replaceAll(find, replace, str) {
	// a handy function because javascript doesn't have a function that lets you replace everytime something appears in a string (it only does it once)
	return str.replace(find, replace);
}

function snipEntry() {
	$.ajax({
		type: 'GET',
		url: "snip_entry.php",
		dataType: 'html',
		success: function(data) {
			$( "main" ).html(data);
		}
	})
}
$( "#snipentry" ).ready(function() {
  $( "#snipentry input:not([type='submit'])" ).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

$( " main " ).on("submit", "#snipentry", function(event) {
	event.preventDefault();
	event.stopImmediatePropagation();
	$( this ).next( "div" ).remove();
	$( "#snipentry" ).css( "border", "solid 1pt");
	$( "#snipentry" ).css( "border-color", "navy");
	$( "#quote" ).css("height", "initial");						
	$( "#snipimgholder" ).html("");
	$( "#fileuploadform" ).html("<div id='primarydocsuploadresult'>If your source document isn&rsquo;t in the &ldquo;primarydocs&rdquo; folder yet,<br>either drop it here or click here to upload it.</div>");
	$('html, body').animate({ scrollTop: 0 }, 'fast');

	$.ajax({
		type: 'POST',
		url: "snip_process.php",
		data: $("#snipentry").serialize(),
		dataType: "html",
		success: function(data) {
			$("<div/>", {id: "snipentryright"}).insertAfter( "#snipentry" );
			$( "#snipentryright" ).html(data);
			$( "#important").prop('checked', false);
			$( "#share, .quesnick, .quesclass" ).val("");
			nde = $( "#nde" ).attr("value");
			switch (nde) {
				case "nde":
					$( "#data_enterer" ).focus();
					$( "#data_enterer" ).css("border-color","red");
					break;
				case "nur":
					$( "#url" ).focus();
					$( "#url" ).css("border-color","red");
					break;
				case "nfn":
					$( "#filename" ).focus();
					$( "#filename" ).css("border-color","red");
					break;
				case "nct":
					$( "#cite_title" ).focus();
					$( "#cite_title" ).css("border-color","red");
					break;
				case "npu":
					$( "#publisher" ).focus();
					$( "#publisher" ).css("border-color","red");
					break;
				case "ndt":
					$( "#date" ).focus();
					$( "#date" ).css("border-color","red");
					break;
				default:
					$( "#quote" ).focus().val("");
			}
		}
	})
});


$( " main " ).on("submit", "#oopsentry", function(event) {
	event.preventDefault();
	event.stopImmediatePropagation();
	$( "#snipentry" ).css( "border", "none");
	$('html, body').animate({ scrollTop: 0 }, 'fast');

	$.ajax({
		type: 'POST',
		url: "snip_process.php",
		data: $("#oopsentry").serialize(),
		dataType: "html",
		success: function(data) {
			$("<div/>", {id: "snipentryright"}).insertAfter( "#snipentry" );
			$( "#snipentryright" ).html(data);
			$( "#snipentryright" ).nextAll().remove();
			$( "#quote" ).focus().val("");
		}
	})
});

$( "#snipentry" ).ready( function() {
	$( "#report_box" ).hide();
});

$(function() {

	function aTags(item) {
		var a = $.ajax({
			type: 'GET',
			url: "/jsonmaker.php?val=" + item,
			dataType: "json",
			success: function(json) {
				return json;
			}
		});
		return a;
	}

	function aTagsTwo(item, table) {
		var a = $.ajax({
			type: 'GET',
			url: "/jsonmaker.php?val=" + item + "&t=" + table,
			dataType: "json",
			success: function(json) {
				return json;
			}
		});
		return a;
	}

	var focid;
	var availableTags;

	$( "#data_enterer, #filename, #author, #publisher, #report_name, #law, #country, #program, #tag, #agency" ).focus(function() {
		focid = this.id;
		availableTags = aTags(focid);
	});

	$( "#cite_title, #url" ).focus(function() {
		focid = this.id;
		availableTags = aTagsTwo(focid, "filename");
	});

	$( "#share, #quescon_nickname0, #quescon_nickname1, #quescon_nickname2, #quescon_nickname3, #quescon_nickname4, #quescon_nickname5" ).on("focus", function() {
		availableTags = aTagsTwo("nickname", "contact");
	});

	$( "#quescon_question_text0, #quescon_question_text1, #quescon_question_text2, #quescon_question_text3, #quescon_question_text4, #quescon_question_text5" ).on("focus", function() {
		availableTags = aTagsTwo("question_text", "question");
	});

	function split( val ) {
	  return val.split( /,\s*/ );
	}

	function extractLast( term ) {
	  return split( term ).pop();
	}

	$( "#data_enterer, #filename, #author, #report_name, #law, #country, #program, #tag, #agency, #cite_title, #url, #publisher, #share, #quescon_nickname0, #quescon_nickname1, #quescon_nickname2, #quescon_nickname3, #quescon_nickname4, #quescon_nickname5, #quescon_question_text0, #quescon_question_text1, #quescon_question_text2, #quescon_question_text3, #quescon_question_text4, #quescon_question_text5" )
	  // don't navigate away from the field on tab when selecting an item
	.bind( "keydown", function( event ) {
	    if ( event.keyCode === $.ui.keyCode.TAB &&
	        $( this ).autocomplete( "instance" ).menu.active ) {
	      event.preventDefault();
	    }
	})
	.on("blur", function() {
		$( this ).val(function(i,v) {
			return v.replace(/,\s$/,"");
		})
	})
	.autocomplete({
	    minLength: 1,
	    source: function( request, response ) {
	    // delegate back to autocomplete, but extract the last term
		    response( $.ui.autocomplete.filter(availableTags.responseJSON, extractLast(request.term)) );
	    },
	    focus: function() {
	      // prevent value inserted on focus
	      return false;
	    },
	    select: function( event, ui ) {
	      var terms = split( this.value );
	      // remove the current input
	      terms.pop();
	      // add the selected item
	      terms.push( ui.item.value );
	      // add placeholder to get the comma-and-space at the end
	      terms.push( "" );
	      this.value = terms.join( ", " );
	      return false;
	    }
	});
});

$( "#data_enterer" ).blur( function() {
	var dataenval = this.value;
	if (dataenval == "") {
		$( "#data_enterer_msg" ).html(" You must identify yourself.").css("color", "red");
		$( "#data_enterer" ).css("border-color","red");
		e = undefined;
	}
	else {
		var d = dataenval.indexOf(" ");
		if (d > 0) {
			e = dataenval.substr(0, d);
			$( "#data_enterer_msg" ).html(" Hello, " + e + ".").css("color", "green");
			$( "#data_enterer" ).css("border-color","initial");
			$.ajax({
				type: 'GET',
				url: "/formtest.php?val=data_enterer&t=data_enterer&v=" + dataenval,
				dataType: "html",
				success: function(h) {
					if (h > 0) {
						$( "#data_enterer_msg" ).append(" <span style='color:green'>Welcome back.</span>");
						$( "#data_enterer" ).css("border-color","initial");
					}
					else {
						$( "#data_enterer_msg" ).append(" <span style='color:goldenrod'>You're new here.</span>");
						$( "#data_enterer" ).css("border-color","goldenrod");
					}
				}
			});
		}
		else {
			$( "#data_enterer_msg" ).html(" Please add your last name. There may be other &ldquo;" + dataenval + "s&rdquo; in the future.").css("color", "red");
			$( "#data_enterer" ).css("border-color","red");
		}
	}
});

$( "#quote" ).blur(function() {
	if (this.value == "") {
		$( "#quote_msg" ).html("This isn&rsquo;t going to work if the quote is empty.").css("color", "red");
		$( "#quote" ).css("border-color","red");
	}
	else {
		$( "#quote_msg" ).html("");
		$( "#quote" ).css("border-color","initial");
		var e = this.value.substr(0, 140);
		e = htmlEntities(e);
		$.ajax({
			type: 'GET',
			url: "/existstest.php?f=quote_key&t=quote&s=quote&v=" + e,
			dataType: "html",
			success: function(h) {
				if (h != "sorry" && h > 0) {
					$( "#quote_msg" ).html("Wait. A nearly identical quote already exists. <a onClick='snipEdit(" + h + ")'>View it here</a>.").css("color", "red");
					$( "#quote" ).css("border-color","red");
				}
			}
		});
	}
});

$( "#filename" ).blur( function() {
	filenameval = this.value;
	var aud = /\.mp3$/;
	var pd = /\.pdf$|\.txt$|\.htm$|\.html$|\.doc$/i;
	var mg = /\.jpg$|\.jpeg$|\.gif$|\.png$|\.svg$/i;
	if (filenameval != "") {
		if (aud.test(filenameval)) {
			clearFnForms();
			$( "#quote" ).css("height", "initial");			
			$( "#snipimgholder" ).html("");
			$( "#is_audio_y" ).prop("checked", true);
			$( "#is_audio_n" ).prop("checked", false);
			$( "#report_box" ).hide("blind");
			$( "#filename_msg" ).html("Assuming this is an audio document.").css("color", "green");
			$( "#filename" ).css("border-color","initial");
		}
		else {
			if (pd.test(filenameval)) {
				$( "#filename_msg" ).html("").css("color", "initial");
				$( "#filename" ).css("border-color","initial");
				clearFnForms();
				$( "#quote" ).css("height", "initial");
				$( "#prisec1" ).prop("checked", true);
				$( "#prisec0" ).prop("checked", false);				
				$( "#report_box" ).show("blind");
			}
			else {
				if (mg.test(filenameval)) {
					$( "#filename_msg" ).html("Assuming this is an image. If it&rsquo;s cut out of another document, like a PDF file, <strong>put that document&rsquo;s filename here instead</strong>. ").css("color", "green");
					$( "#filename" ).css("border-color","initial");
					clearFnForms();
					$( "#report_box" ).show("blind");
					var hbl = /has been uploaded/;
					var hbltest = $( "#primarydocsuploadresult" ).html();
					if (hbl.test(hbltest) === false) {
						var fntest = /^images/;
						var fntest2 = /^\/images/;
						if (fntest.test(filenameval) === true || fntest2.test(filenameval) === true) {
							$( "#snipimgholder" ).html("<a href='/primarydocs/" + filenameval + "' target='_blank'><img src='/primarydocs/" + filenameval + "' style='height:200px;'></a>");
							$( "#quote" ).val("Data is an image at /primarydocs/" + filenameval);
						}
						else {
							$( "#snipimgholder" ).html("<a href='/primarydocs/images/" + filenameval + "' target='_blank'><img src='/primarydocs/images/" + filenameval + "' style='height:200px;'></a>");
							$( "#quote" ).val("Data is an image at /primarydocs/images/" + filenameval);
						}
						$( "#quote" ).css("height", "2em");
						$( "#quote_msg" ).html("If you&rsquo;re posting an image, leave this text as is and it will display correctly.");
						$( "#quote_msg" ).css("color", "green");
					}
				}
				else {
					$( "#filename_msg" ).html("It&rsquo;s rather odd to have a filename that doesn&rsquo;t end in &ldquo;.pdf,&rdquo; &ldquo;.html,&rdquo; &ldquo;.htm,&rdquo; &ldquo;.doc,&rdquo; &ldquo;.txt,&rdquo; &ldquo;jp(e)g,&rdquo; &ldquo;gif,&rdquo; &ldquo;png,&rdquo; or &ldquo;svg.&rdquo; ").css("color", "goldenrod");
					$( "#filename" ).css("border-color","goldenrod");
					clearFnForms();
					$( "#quote" ).css("height", "initial");
					$( "#snipimgholder" ).html("");
					$( "#prisec1" ).prop("checked", true);
					$( "#prisec0" ).prop("checked", false);				
					$( "#report_box" ).show("blind");
				}
			}
		}
		$.ajax({
			type: 'GET',
			url: "/filenamejson.php?f=" + filenameval,
			dataType: "json",
			success: function(json) {
				if (typeof json.cite_title !== "undefined" && json.cite_title != "") {
					$( "#filename_msg" ).html("That document is in the database. Its corresponding blanks are now filled in.").css("color", "green");
					$( "#filename" ).css("border-color","initial");
					$( "#cite_title" ).val(json.cite_title);
					$( "#cite_title_msg" ).html("");
					$( "#cite_title" ).css("border-color","initial");
					$( "#url" ).val(json.url);
					$( "#url_msg" ).html("");
					$( "#url" ).css("border-color","initial");
					$( "#publisher" ).val(json.publisher);
					$( "#date" ).val(json.date);
					$( "#cite_title" ).val(json.cite_title);
					$( "#city" ).val(json.city);
					$( "#report_name" ).val(json.report_name);
					$( "#report_desc" ).val(json.report_desc);
					$( "#law" ).val(json.law);
					$( "#section" ).val(json.section);
					$( "#report_due" ).val(json.report_due);
					$( "#author" ).val(json.author);
					if (json.prisec == 1) {
						$( "#prisec1" ).prop("checked", true);
						$( "#prisec0" ).prop("checked", false);
					}
					else {
						$( "#prisec1" ).prop("checked", false);
						$( "#prisec0" ).prop("checked", true);
					}
					if (json.language == "English") {
						$( "#language_en" ).prop("checked", true);
						$( "#language_sp" ).prop("checked", false);
					}
					else {
						$( "#language_en" ).prop("checked", false);
						$( "#language_sp" ).prop("checked", true);
					}
					if (json.is_audio == 1) {
						$( "#is_audio_y" ).prop("checked", true);
						$( "#is_audio_n" ).prop("checked", false);
					}
					else {
						$( "#is_audio_y" ).prop("checked", false);
						$( "#is_audio_n" ).prop("checked", true);
					}
					if (json.is_image == 2) {
						$( "#is_audio_y" ).prop("checked", false);
						$( "#is_audio_n" ).prop("checked", false);
					}
					if (json.read_it == 1) {
						$( "#read_it" ).prop("checked", true);
					}
					else {
						$( "#read_it" ).prop("checked", false);
					}
					if (($( "#url_msg").html().indexOf("URL exists") >= 0) || ($( "#url_msg").html().indexOf("secondary source") >= 0)) {
						$( "#url_msg" ).html("");
						$( "#url" ).css("border-color","initial");
					}
				}
				else {
					if ($( "#filename_msg" ).html() == "Assuming this is an audio document.") {
						$( "#filename_msg" ).append("<span style='color:goldenrod'> This will be the first time that this document appears in the database.</span>");
						$( "#filename" ).css("border-color","goldenrod");
						$( "#quote" ).css("height", "initial");
						$( "#snipimgholder" ).html("");
					}
					else {
						$( "#filename_msg" ).append("This will be the first time that this document appears in the database.").css("color", "goldenrod");
						$( "#filename" ).css("border-color","goldenrod");
					}
					clearFnForms();
					$( "#prisec1" ).prop("checked", true);
					$( "#prisec0" ).prop("checked", false);
					$( "#read_it" ).prop("checked", false);
				}
			}
		});
	}
	else {
		if ($( "#prisec1" ).is(":checked")) {
			clearFnForms();
			$( "#quote" ).css("height", "initial");
/*			var diai = /^Data is an image/;
			var qv = $( "#quote" ).val();
			if (diai.test(qv) === true) {
				$( "#quote" ).val("");
			}
*/			$( "#snipimgholder" ).html("");
			$( "#filename_msg" ).html("As there&rsquo;s no filename, let&rsquo;s assume this is a secondary source document.").css("color", "goldenrod");
			$( "#filename" ).css("border-color","goldenrod");
			$( "#prisec1" ).prop("checked", false);
			$( "#prisec0" ).prop("checked", true);		
			$( "#report_box" ).hide("blind");
			$( "#is_audio_y" ).prop("checked", false);
			$( "#is_audio_n" ).prop("checked", true);
			$( "#read_it" ).prop("checked", false);
			if ($( "#url_msg").html().indexOf("URL exists") >= 0) {
				$( "#url_msg" ).html("");
				$( "#url" ).css("border-color","initial");
			}
		}
	}
});

$( "#cite_title" ).blur( function() {
	if (this.value == "") {
		if (typeof e != "undefined") {
			$( "#cite_title_msg" ).html("You can&rsquo;t cite a document without a title, " + e + ".").css("color", "red");
			$( "#cite_title" ).css("border-color","red");
		}
		else
			$( "#cite_title_msg" ).html("You can&rsquo;t cite a document without a title.").css("color", "red");
			$( "#cite_title" ).css("border-color","red");
	}
	else {
		$( "#cite_title_msg" ).html("");
		$( "#cite_title" ).css("border-color","initial");
	}
});

$( "#url" ).blur(function() {
	if (this.value == "") {
		if ($( "#prisec0" ).is(":checked")) {
			$( "#url_msg" ).html("A secondary source at least needs the URL where you found it.").css("color", "red");
			$( "#url" ).css("border-color","red");
		}
		else {
			$( "#url_msg" ).html("");
			$( "#url" ).css("border-color","initial");
		}
	}
	else {
		var rl = this.value;
		var d = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
		var r = new RegExp(d);
		if(r.test(rl)) {

			$.ajax({
				type: 'GET',
				url: "/existstest.php?f=filename_key&t=filename&s=url&v=" + rl,
				dataType: "html",
				success: function(h) {
					if (h != "sorry" && h > 0) {
						k = $( "#filename_msg" ).html();
						if (k.indexOf("first time") >= 0){
							$( "#url_msg" ).html("This URL exists in the database. <a onClick='snipEdit(" + h + ")'>View it here</a>. Are you sure this is a new document?").css("color", "goldenrod");
							$( "#url" ).css("border-color","goldenrod");
						}
					}
					else {
						$( "#url_msg" ).html("");
						$( "#url" ).css("border-color","initial");
					}
				}
			});
		}
		else {
			$( "#url_msg" ).html("This doesn&rsquo;t look like a URL.").css("color", "goldenrod");
			$( "#url" ).css("border-color","goldenrod");
			if (rl.indexOf("http://") != 0 || rl.indexOf("https://") == 0) {
				$( "#url_msg" ).append(" Try starting with &ldquo;http://&rdquo;");	
			}
		}
	}
});

$( "#author" ).blur( function() {
	formWarn("author", this.value);
});

$( "#report_name" ).blur( function() {
	if (this.value != ""){
		var a = this.value.replace(/(['"])/g,"\\$1");

		$.ajax({
			type: 'GET',
			url: "/existstest.php?f=report_desc&t=report_name&s=report_name&v=" + a,
			dataType: "html",
			success: function(h) {
				if (h != "sorry" && h != "") {
					$( "#report_name_msg" ).html("That report exists in the database. Its description has been filled in below.").css("color", "green");
						$( "#report_desc" ).val(h);
				}
				else {
					$( "#report_name_msg" ).html(" &ldquo;" + a + "&rdquo; is new to the database.").css("color", "goldenrod");
					$( "#report_name" ).css("border-color", "goldenrod");
					$( "#report_desc" ).val("");
				}
			}
		});
	}
	else {
		$( "#report_name_msg" ).html("").css("color", "initial");
		$( "#report_name" ).css("border-color", "initial");				
		$( "#report_desc" ).val("");
	}
});

$( "#law" ).blur( function() {
	formWarn("law", this.value);
});

$( "#country" ).blur( function() {
	formWarn("country", this.value);
});

$( "#program" ).blur( function() {
	formWarn("program", this.value);
});

$( "#tag" ).blur( function() {
	formWarn("tag", this.value);
});

$( "#agency" ).blur( function() {
	formWarn("agency", this.value);
});

$( "#date, #report_due" ).datepicker({
	dateFormat: "yy-mm-dd",
	changeMonth: true,
	changeYear: true,
	constrainInput: false,
	onSelect: function() {
		$( this ).focus();
	},
	onClose: function() {
		$( this ).focus();
	}
}).blur( function() {
	var g = new RegExp(/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/);
	var t = this.value;
	var h = /^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z.]*(,\s|,|\s)(\d{2,4})$/;
	var ds = /^\d\d?(\s|\-|\/|\.)\d{2,4}$/;
	var yo = /^\d{2,4}$/;
	if (ds.test(t) === true || h.test(t) === true || yo.test(t) === true) {
		if (ds.test(t) === true) {
			var i = t.slice(-4);
			var j = /[0-9]{4}/;
			if (j.test(i) === true) {
				var year = i;
			}
			else {
				var k = i.slice(-2);
				var year = "20" + k;
			}
			var month = t.slice(0,2);
			var dr = /^\d(\s|\-|\/|\.)/;
			if (dr.test(month) === true) {
				var l = month.slice(0,1);
				switch(l) {
					case "1":
						var month = "01";
						break;
					case "2":
						var month = "02";
						break;
					case "3":
						var month = "03";
						break;
					case "4":
						var month = "04";
						break;
					case "5":
						var month = "05";
						break;
					case "6":
						var month = "06";
						break;
					case "7":
						var month = "07";
						break;
					case "8":
						var month = "08";
						break;
					case "9":
						var month = "09";
						break;
				}
			}
			$( this ).val(year + "-" + month + "-00");
			$( this ).next( "div" ).html("No day was specified. Substituting &ldquo;00.&rdquo;").css("color", "goldenrod");
			$( this ).css("border-color","goldenrod");		
		}
		if (h.test(t) === true) {
			var i = t.slice(-4);
			var j = /[0-9]{4}/;
			if (j.test(i) === true) {
				var year = i;
			}
			else {
				var k = i.slice(-2);
				var year = "20" + k;
			}
			var l = t.slice(0,3)
			switch (l) {
				case "Jan":
					var month = "01";
					break;
				case "Feb":
					var month = "02";
					break;
				case "Mar":
					var month = "03";
					break;
				case "Apr":
					var month = "04";
					break;
				case "May":
					var month = "05";
					break;
				case "Jun":
					var month = "06";
					break;
				case "Jul":
					var month = "07";
					break;
				case "Aug":
					var month = "08";
					break;
				case "Sep":
					var month = "09";
					break;
				case "Oct":
					var month = "10";
					break;
				case "Nov":
					var month = "11";
					break;
				case "Dec":
					var month = "12";
					break;
			}
			month = "0" + month;
			month = month.slice(-2);
			$( this ).val(year + "-" + month + "-00");
			$( this ).next( "div" ).html("No day was specified. Substituting &ldquo;00.&rdquo;").css("color", "goldenrod");
			$( this ).css("border-color","goldenrod");
		}
		if (yo.test(t) === true) {
			var j = /[0-9]{4}/;
			if (j.test(t) === false) {
				var k = t.slice(-2);
				var t = "20" + k;
			}
			$( this ).val(t + "-00-00");
			$( this ).next( "div" ).html("Only a year was specified. Substituting &ldquo;00-00&rdquo; for month and date.").css("color", "goldenrod");
			$( this ).css("border-color","goldenrod");		
		}
	}
	else {
		if (g.test(t) === false) {
			var a = Date.parse(this.value);
			a = new Date(a);
			var year = a.getFullYear();
			var month = "0" + (a.getMonth()+1);
			month = month.slice(-2);
			var date = "0" + a.getDate();
			date = date.slice(-2);
			$( this ).val(year + "-" + month + "-" + date);
		}
		if ($( this ).attr("id") == "date") {
			if (t == "") {
				$( "#date_msg" ).html("There has to be a publication date. ").css("color", "red");
				$( "#date" ).css("border-color","red");
			}
			else{
				$( "#date_msg" ).html("").css("color", "initial");
				$( "#date" ).css("border-color","initial");
			}
		}
		if ( this.value == "NaN-aN-aN" ) {
			$( this ).val("");
			if ($( this ).attr("id") == "date") {
				$( this ).next( "div" ).append("That was not a recognizable date.").css("color", "red");
				$( this ).css("border-color","red");
			}
			else {
				if ($( "#law" ).val() != "") {
					$( this ).next( "div" ).html("That was not a recognizable date.").css("color", "goldenrod");
					$( this ).css("border-color","goldenrod");
				}
			}
		}
		else {
			$( this ).next( "div" ).html("").css("color", "initial");
			$( this ).css("border-color","initial");		
		}
		$( this ).next( "input" ).focus();
	}
});

$( "#share" ).blur( function() {
	var a = this.value;
	var a = a.replace(/,\s/g,",");
	var authors = a.split(",");
	var newauths = [];

	for (var i = 0, len = authors.length; i < len; i++) {
		$.ajax({
			type: 'GET',
			url: "/doesntexisttest.php?f=contact_key&t=contact&s=nickname&v=" + authors[i],
			dataType: "html",
			success: function(h) {
				if (h != "sorry") {
					newauths.push(h);
				}
				if (i >= len) {
					if (newauths.length >= 1) {
						var r = newauths.join(", ");
						if (newauths.length > 1) {
							$( "#share_msg" ).html("&ldquo;" + r + "&rdquo; are new to the database.").css("color", "goldenrod");
							$( "#share" ).css("border-color", "goldenrod");								
						}
						else {
							$( "#share_msg" ).html(" &ldquo;" + r + "&rdquo; is new to the database.").css("color", "goldenrod");
							$( "#share" ).css("border-color", "goldenrod");
						}
					}
					if (newauths.length < 1 || h == "") {
						$( "#share_msg" ).html("");
						$( "#share" ).css("border-color", "initial");
					}
				}
			}
		});
	}
});

function nickCheck(th) {
	var a = th.value;
	var a = a.replace(/,\s$/,"");
	var a = a.replace(/,\s/g,",");
	var authors = a.split(",");
	var newauths = [];
	var b = quescondivnum - 1;

	for (var i = 0, len = authors.length; i < len; i++) {
		$.ajax({
			type: 'GET',
			url: "/doesntexisttest.php?f=contact_key&t=contact&s=nickname&v=" + authors[i],
			dataType: "html",
			success: function(h) {
				if (h != "sorry") {
					newauths.push(h);
				}
				if (i >= len) {
					if (newauths.length >= 1) {
						var r = newauths.join(", ");
						if (newauths.length > 1) {
							$( "#quescon_nick" + b + "_msg" ).html("&ldquo;" + r + "&rdquo; are new to the database.").css("color", "goldenrod");
							$( "#quescon_nickname" + b ).css("border-color", "goldenrod");								
						}
						else {
							$( "#quescon_nick" + b + "_msg" ).html(" &ldquo;" + r + "&rdquo; is new to the database.").css("color", "goldenrod");
							$( "#quescon_nickname" + b ).css("border-color", "goldenrod");
						}
					}
					if (newauths.length < 1 || h == "") {
						$( "#quescon_nick" + b + "_msg" ).html("");
						$( "#quescon_nickname" + b ).css("border-color", "initial");
					}
				}
			}
		});
	}
};

function quesCheck(th) {
	var a = th.value;
	var a = a.replace("'","\\\'")
	var a = a.replace(/,\s$/,"");
	var b = quescondivnum - 1;

	$.ajax({
		type: 'GET',
		url: "/doesntexisttest.php?f=question_text&t=question&s=question_text&v=" + a,
		dataType: "html",
		success: function(h) {
			if (h != "sorry" && a != "") {
				$( "#quescon" + b + "_msg" ).html(" &ldquo;" + h + "&rdquo; is new to the database.").css("color", "goldenrod");
				$( "#quescon_question_text" + b ).css("border-color", "goldenrod");
			}
			else {
				$( "#quescon" + b + "_msg" ).html("");
				$( "#quescon_question_text" + b ).css("border-color", "initial");
			}
		}
	});
};

$( "#publisher" ).blur( function() {
	if (this.value == "") {
		$( "#publisher_msg" ).html("You can&rsquo;t cite this document without a publisher. (If it&rsquo;s a letter, enter the writer&rsquo;s place of work.)").css("color", "red");
		$( "#publisher" ).css("border-color","red");
	}
	else {
		formWarnPub("publisher", this.value, "publisher");
	}
});

$( "#city" ).blur( function() {
	if (this.value == "w") {
		$( this ).val("Washington");
	}
});

function formWarnPub(th, c, p) {
	var a = c.replace(/,\s/g,",");
	var authors = a.split(",");
	var newauths = [];

	for (var i = 0, len = authors.length; i < len; i++) {
		$.ajax({
			type: 'GET',
			url: "/doesntexisttest.php?f=" + th + "_key&t=" + th + "&s=" + p + "&v=" + authors[i],
			dataType: "html",
			success: function(h) {
				if (h != "sorry") {
					newauths.push(h);
				}
				if (i >= len) {
					if (newauths.length >= 1) {
						var r = newauths.join(", ");
						if (newauths.length > 1) {
							$( "#" + p + "_msg" ).html("&ldquo;" + r + "&rdquo; are new to the database.").css("color", "goldenrod");
							$( "#" + p ).css("border-color", "goldenrod");								
						}
						else {
							$( "#" + p + "_msg" ).html(" &ldquo;" + r + "&rdquo; is new to the database.").css("color", "goldenrod");
							$( "#" + p ).css("border-color", "goldenrod");
						}
					}
					if (newauths.length < 1 || h == "") {
						$( "#" + p + "_msg" ).html("");
						$( "#" + p ).css("border-color", "initial");
					}
				}
			}
		});
	}
};

function formWarn(th, c) {
	var a = c.replace(/,\s/g,",");
	var authors = a.split(",");
	var newauths = [];

	for (var i = 0, len = authors.length; i < len; i++) {
		$.ajax({
			type: 'GET',
			url: "/doesntexisttest.php?f=" + th + "_key&t=" + th + "&s=" + th + "&v=" + authors[i],
			dataType: "html",
			success: function(h) {
				if (h != "sorry") {
					newauths.push(h);
				}
				if (i >= len) {
					if (newauths.length >= 1) {
						var r = newauths.join(", ");
						if (newauths.length > 1) {
							$( "#" + th + "_msg" ).html("&ldquo;" + r + "&rdquo; are new to the database.").css("color", "goldenrod");
							$( "#" + th ).css("border-color", "goldenrod");								
						}
						else {
							$( "#" + th + "_msg" ).html(" &ldquo;" + r + "&rdquo; is new to the database.").css("color", "goldenrod");
							$( "#" + th ).css("border-color", "goldenrod");
						}
					}
					if (newauths.length < 1 || h == "") {
						$( "#" + th + "_msg" ).html("");
						$( "#" + th ).css("border-color", "initial");
					}
				}
			}
		});
	}
};

function htmlEntities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/’/g, '&rsquo;').replace(/“/g, '&ldquo;').replace(/”/g, '&rdquo;').replace(/‘/g, '&lsquo;').replace(/—/g, '&mdash;').replace(/–/g, '&ndash;');
}

function clearFnForms() {
	$( "#cite_title" ).val("");
	$( "#url" ).val("");
	$( "#publisher" ).val("");
	$( "#date" ).val("");
	$( "#cite_title" ).val("");
	$( "#city" ).val("");
	$( "#report_name" ).val("");
	$( "#report_desc" ).val("");
	$( "#law" ).val("");
	$( "#section" ).val("");
	$( "#report_due" ).val("");
	$( "#author" ).val("");
	$( ".quesclass" ).val("");
	$( ".quesnick" ).val("");
}

function snipEntryReset(event) {
	$("div[id$='msg'], span[id$='msg']").html("");
	$("input, textarea").css("border-color", "initial");
	$( "#quote" ).css("height", "initial");
	$( "#primarydocsuploadresult" ).html("If your source document isn't in the &ldquo;primarydocs&rdquo; folder yet,<br>either drop it here or click here to upload it.");
	$( "#snipimgholder" ).html("");
}

quescondivnum = 1;
qqttabindex = 105;
qntabindex = 110;
qbtabindex = 115;

function addQuescon() {
	var a = quescondivnum-1;
	if ($( "#quescon_question_text" + a ).val() != "") {
		$( "#quescon" + a + "_msg" ).html("");
		$( "#quescon_question_text" + a).css("border-color", "initial");
		$("<div/>", {
			id: "quescondiv" + quescondivnum,
			html: "<div>Another question<br><textarea id='quescon_question_text" + quescondivnum + "' name='quescon[question_text][]' class='quesclass' onBlur='quesCheck(this);' rows='2' cols='90' tabindex='" + qqttabindex + "' placeholder='whatever we want to know'></textarea></div><div id='quescon" + quescondivnum + "_msg' class='msgtext'></div><div>Who should we ask?<br><input id='quescon_nickname" + quescondivnum + "' name='quescon[nickname][]' type='text' class='quesnick' tabindex='" + qntabindex + "' size='70' onBlur='nickCheck(this);' placeholder='each official&rsquo;s full name separated by commas'><button type='button' id='quescon_button' tabindex='" + qbtabindex + "' onclick='addQuescon();'>Add another question</button></div><div id='quescon_nick" + quescondivnum + "_msg' class='msgtext'></div>"
		})
		.appendTo( "#quescondiv0" );
		quescondivnum++;
		qqttabindex = qqttabindex + 15;
		qntabindex = qntabindex + 15;
		qbtabindex = qbtabindex + 15;
	}
	else {
		$( "#quescon" + a + "_msg" ).html("Please ask a question before trying to add another.").css("color", "goldenrod");
		$( "#quescon_question_text" + a ).css("border-color", "goldenrod");
	}
}

function addToFilename(a) {
	$( "#filename" ).val(a);
}

$(function() {

	$("#fileuploadform" ).dropzone({
		paramName: "fileToUpload",
		url: "/primarydocsupload.php",
		maxFilesize: 250,
		uploadMultiple: false,
		dictDefaultMessage: "",
		clickable: "#fileuploadform, #primarydocsuploadresult",
		addRemoveLinks: true,
		success: function(file, response) {
			$( "#primarydocsuploadresult" ).html(response);
			var r = /^([a-zA-Z0-9\/_\-\.\(\)])+\sis/;
			var s = /Sorry/;
			droppedfile = "";
			imsorry = "";
			if (r.test(response) === true) {
				var k = response.indexOf(" is");
				droppedfile = response.slice(12,k);
				$( "#filename" ).val(droppedfile);
				$( "#filename_msg" ).html("").css("color", "initial");
				$( "#filename" ).css("border-color","initial");
				clearFnForms();
				$( "#report_box" ).show("blind");
				var mg = /\.jpg$|\.jpeg$|\.gif$|\.png$|\.svg$/i;
				if (mg.test(droppedfile)) {

					$( "#quote" ).val("Data is an image at /primarydocs/" + droppedfile);
					$( "#quote" ).css("height", "2em");
					$( "#quote_msg" ).html("If you&rsquo;re posting an image, leave this text as is and it will display correctly.");
					$( "#quote_msg" ).css("color", "green");
				}
				$( "#data_enterer" ).focus();
			}
			if (s.test(response) === true) {
				imsorry = "sorry";
			}
		},
		removedfile: function(file) {
			var dropfile = "primarydocs/" + droppedfile;
			if (imsorry != "sorry") {
				$.ajax({
				    url: "/delete_dropzone_file.php?dropfile=" + dropfile,
	    			type: "GET",
	    			success: function() {
						$( "#filename" ).val("");
						$( "#quote" ).val("");
						$( "#quote" ).css("height", "initial");						
						$( "#snipimgholder" ).html("");
						$( "#fileuploadform" ).find(file.previewElement).remove();
						$( "#primarydocsuploadresult" ).html("If your source document isn't in the &ldquo;primarydocs&rdquo; folder yet,<br>either drop it here or click here to upload it.");
						$('html, body').animate({ scrollTop: 0 }, 'fast');
						$( "#data_enterer" ).focus();
					}
				});
			}
			else {
					$( "#filename" ).val("");
					$( "#quote" ).css("height", "initial");
					var diai = /^Data is an image/;
					var qv = $( "#quote" ).val();
					if (diai.test(qv) === true) {
						$( "#quote" ).val("");
					}					
					$( "#snipimgholder" ).html("");
					$( "#fileuploadform" ).find(file.previewElement).remove();
					$( "#primarydocsuploadresult" ).html("If your source document isn't in the &ldquo;primarydocs&rdquo; folder yet,<br>either drop it here or click here to upload it.");
					$('html, body').animate({ scrollTop: 0 }, 'fast');
					$( "#data_enterer" ).focus();
			}
		}
	});
});