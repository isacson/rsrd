$( "#snipsearch input, #snipsearch select" ).on("blur change", function() {
	snipSearchMe();
});


function snipSearchMe() {

	searchused = 1;
	searchchosen = 1;
	querytext = {
		quotesql: "",
		havingsql: ""
	};

// Search blank

	var a = $( "#quotesearch" ).val();
	quoteblank = a;

	if (a != "") {

		a = a.replace(/^\s+/g,"");
		a = a.replace(/\s+$/g,"");
		a = a.replace(/\s+/g," ");
		var regex = /^(AND\s|OR\s)/i;
		while (regex.test(a) === true) {
			a = a.replace(/^(AND\s|OR\s)/ig,"");
		}
		var regex = /\".*?\"/g;
		var b = a.match(regex);
		if (b != null) {
			for (var i = b.length - 1; i >= 0; i--) {
				var c = b[i].replace(/\"/g,"");
				c = c.replace(/\s/,"~~qsp~~");
				a = a.replace(b[i],c);
			};
		}

		a = a.replace(/\sAND\s/gi, " ");

		var regex = /\sOR\s.+?(\s|$)/gi;
		var b = a.match(regex);
		if (b != null) {
			for (var i = b.length - 1; i >= 0; i--) {
				c = b[i].replace(/\sOR\s+?/i," ~~or~~");
				a = a.replace(b[i],c);
			};
		}

		var regex = /\sNOT\s.+?(\s|$)/gi;
		var b = a.match(regex);
		if (b != null) {
			for (var i = b.length - 1; i >= 0; i--) {
				c = b[i].replace(/\sNOT\s+?/i," ~~not~~");
				a = a.replace(b[i],c);
			};
		}

		a = a.split(" ");


		for (var i = a.length - 1; i >= 0; i--) {
			var regexor = /~~or~~/;
			var regexnot = /~~not~~/;
			if (regexor.test(a[i]) === false && regexnot.test(a[i]) === false) {
				if (i > 0) {
					a[i] = " AND quote REGEXP '[[:<:]]" + a[i] + "[[:>:]]' ";
				}
				else {
					a[i] = " quote REGEXP '[[:<:]]" + a[i] + "[[:>:]]' ";				
				}
			}
			if (regexor.test(a[i]) === true) {
				a[i] = a[i].replace(regexor, "");
				a[i] = " OR quote REGEXP '[[:<:]]" + a[i] + "[[:>:]]' ";
				a.push(a[i]);
				a[i] = " ";
			}
			if (regexnot.test(a[i]) === true) {
				a[i] = a[i].replace(regexnot, "");
				a[i] = " quote NOT REGEXP '[[:<:]]" + a[i] + "[[:>:]]' ";
			}
		}

		if (a.length > 1) {
			a = a.join(" AND ");
		}
		else {
			a = a[0];
		}

		if (querytext.quotesql == "") {

			querytext.quotesql = " (" + a + ") ";
		}
		else {

			querytext.quotesql = " AND (" + a + ") ";
		}

	}
	else {
		quoteblank = "";
		querytext.quotesql = " quote LIKE '%' "; 
	}

	var regex = /AND\s+?AND/i;
	while(regex.test(querytext.quotesql) === true) {
		var a = querytext.quotesql;
		querytext.quotesql = a.replace(regex, " AND ");
	}

	var regex = /(AND\s+?OR|OR\s+?OR)/i;
	while(regex.test(querytext.quotesql) === true) {
		var a = querytext.quotesql;
		querytext.quotesql = a.replace(regex, " OR ");
	}

	var regex = /~~qsp~~/g;
	if (regex.test(querytext.quotesql) === true) {
		var a = querytext.quotesql;
		querytext.quotesql = a.replace(regex, " ");
	}

	var regex = /\s+/;
	if (regex.test(querytext.quotesql) === true) {
		var a = querytext.quotesql;
		querytext.quotesql = a.replace(regex, " ");
	}

	justthequote = querytext.quotesql;


// country, program, tag, agency, author, data_enterer blanks

	var a = "";

	a = simpleBlanks("country_include", "country_msg", "country", "country_all_any", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleBlanks("program_include", "program_msg", "program", "program_all_any", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleBlanks("tag_include", "tag_msg", "tag", "tag_all_any", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleBlanks("agency_include", "agency_msg", "agency", "agency_all_any", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleBlanks("author_include", "author_msg", "author", "author_all_any", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	if ($( "#data_enterer_include" ).length > 0) {

		a = simpleBlanks("data_enterer_include", "data_enterer_msg", "data_enterer", "data_enterer_all_any", querytext.havingsql);
		querytext.havingsql = querytext.havingsql + a;
	}

// "NOT" country, program, tag, agency, data_enterer blanks

	var a = "";

	a = simpleNot("country_not", "country_msg", "country", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleNot("program_not", "program_msg", "program", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleNot("tag_not", "tag_msg", "tag", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	a = simpleNot("agency_not", "agency_msg", "agency", querytext.havingsql);
	querytext.havingsql = querytext.havingsql + a;

	if ($( "#data_enterer_include" ).length > 0) {

		a = simpleNot("data_enterer_not", "data_enterer_msg", "data_enterer", querytext.havingsql);
		querytext.havingsql = querytext.havingsql + a;
	}

// cite_title, report_name, law

	var a = "";

	a = justBlanks("cite_title_words", "cite_title_words_msg", "cite_title", "filename", querytext.quotesql);
	querytext.quotesql = querytext.quotesql + a;

	a = justBlanks("report_title_words", "report_title_words_msg", "report_name", "report_name", querytext.quotesql);
	querytext.quotesql = querytext.quotesql + a;

	a = justBlanks("law_words", "law_words_msg", "law", "law", querytext.quotesql);
	querytext.quotesql = querytext.quotesql + a;



	var a = "";

	$( "#clipdate_msg" ).html("");

	var a = $( "#clipdate1" ).val();
	var aa = $( "#clipdate2" ).val();

	var regex = /^[\d-]+$/;

	if (regex.test(a) === false) {

		a = "";
	}

	if (regex.test(aa) === false) {

		aa = "";
	}

	var c = "";

// if first date is filled but not second

	if (a != "" && aa == "") {

		if (querytext.quotesql != "") {
			c = " AND ( date >= '" + a + "') ";
		}
		else {
			c = " ( date >= '" + a + "') ";
		}
	}

// if second date is filled but not first

	if (a == "" && aa != "") {

		if (querytext.quotesql != "") {
			c = " AND ( date <= '" + aa + "') ";
		}
		else {
			c = " ( date <= '" + aa + "') ";
		}
	}

// if both dates are filled

	if (a != "" && aa != "") {

		if (aa < a ) {
			var aaa = a;
			a = aa;
			aa = aaa;
		}

		if (querytext.quotesql != "") {
			c = " AND ( date >= '" + a + "' AND date <= '" + aa + "') ";
		}
		else {
			c = " ( date >= '" + a + "' AND date <= '" + aa + "') ";
		}
	}

	querytext.quotesql = querytext.quotesql + c;


	var regex = /AND\s+?AND/i;
	while(regex.test(querytext.quotesql) === true) {
		var a = querytext.quotesql;
		querytext.quotesql = a.replace(regex, " AND ");
	}

	var regex = /(AND\s+?OR|OR\s+?OR)/i;
	while(regex.test(querytext.quotesql) === true) {
		var a = querytext.quotesql;
		querytext.quotesql = a.replace(regex, " OR ");
	}
	var a = querytext.quotesql;
	querytext.quotesql = a.replace(/~~qsp~~/g, " ");
	querytext.quotesql = a.replace(/\s+/," ");

	checkUrl();
}

function simpleBlanks(id, msgid, field, allanyid, querytext) {

	var a = "";

	$( "#" + msgid ).html("");

	if ($( "#" + id ).val() != "") {

		a = $( "#" + id ).val();

		a = a.replace(/,\s+/g, ",");

		a = a.replace(/,$/, "");

		a = a.split(",");

		for (var i = a.length - 1; i >= 0; i--) {

			$.ajax ({
				type: 'GET',
				url: "/simple_exists_test.php?f=" + field + "&v=" + a[i],
				async: false,
				dataType: 'html',
				success: function(p) {
					if (p == 0) {
						field = field.replace("_", " ");
						$( "#" + msgid ).append("&ldquo;" + a[i] + "&rdquo; is not a " + field + " in the database. Ignoring it. " ).css("color", "red");
						a.splice(i,1);
					}
				}
			})
		}

		if($( "#" + allanyid ).val() == "all" ) {
			a = a.join( "%' AND " + field + " LIKE '%")
		}

		if($( "#" + allanyid ).val() == "any" ) {
			a = a.join( "%' OR  " + field + " LIKE '%")
		}

		if (a != "") {
			if (querytext != "") {
				a = " AND ( " + field + " LIKE '%" + a + "%') ";
			}
			else {
				a = " (" + field + " LIKE '%" + a + "%') ";
			}
		}
	}

	return a;
}

function simpleNot(id, msgid, field, querytext) {

	var a = "";

	if ($( "#" + id ).val() != "") {

		a = $( "#" + id ).val();

		a = a.replace(/,\s+/g, ",");

		a = a.replace(/,$/, "");

		a = a.split(",");

		for (var i = a.length - 1; i >= 0; i--) {

			$.ajax ({
				type: 'GET',
				url: "/simple_exists_test.php?f=" + field + "&v=" + a[i],
				async: false,
				dataType: 'html',
				success: function(p) {
					if (p == 0) {
						field = field.replace("_", " ");
						$( "#" + msgid ).append("&ldquo;" + a[i] + "&rdquo; is not a " + field + " in the database. Ignoring it. " ).css("color", "red");
						a.splice(i,1);
					}
				}
			})
		}

		a = a.join( "%' AND " + field + " NOT LIKE '%")

		if (a != "") {
			if (querytext != "") {
				a = " AND ( " + field + " NOT LIKE '%" + a + "%') ";
			}
			else {
				a = " (" + field + " NOT LIKE '%" + a + "%') ";
			}
		}
	}

	return a;
}

function justBlanks(id, msgid, field, table, querytext) {

	var a = "";

	$( "#" + msgid ).html("");

	if ($( "#" + id ).val() != "") {

		a = $( "#" + id ).val();

		a = a.replace(/,\s+?$/, "");

		$.ajax ({
			type: 'GET',
			url: "/simple_exists_test.php?f=" + field + "&t=" + table + "&v=" + a,
			async: false,
			dataType: 'html',
			success: function(p) {
				if (p == 0) {
					field = field.replace("_", " ");
					$( "#" + msgid ).html("&ldquo;" + a + "&rdquo; doesn&rsquo;t appear in a " + field + " in the database. Ignoring it. " ).css("color", "red");
					a = "";
				}
			}
		})

		a = a.replace("'", "\\\'", a);

		if (a != "") {
			if (querytext != "") {
				a = " AND ( " + field + " LIKE '%" + a + "%') ";
			}
			else {
				a = " (" + field + " LIKE '%" + a + "%') ";
			}
		}
	}

	return a;
}

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

	$( "#country_include, #country_not, #program_include, #program_not, #tag_include, #tag_not, #agency_include, #agency_not, #author_include, #law_words" ).focus(function() {
		var f = this.id;
		var c = f.indexOf("_");
		focid = f.substr(0,c);
		availableTags = aTags(focid);
	});

	$( "#report_title_words" ).focus(function() {
		availableTags = aTags("report_name");
	});

	$( "#data_enterer_include, #data_enterer_not" ).focus(function() {
		availableTags = aTags("data_enterer");
	});

	$( "#cite_title_words" ).focus(function() {
		availableTags = aTagsTwo("cite_title", "filename");
	});

	function split( val ) {
	  return val.split( /,\s*/ );
	}

	function extractLast( term ) {
	  return split( term ).pop();
	}

	$( "#country_include, #country_not, #program_include, #program_not, #tag_include, #tag_not, #agency_include, #agency_not, #author_include, #law_words, #report_title_words, #data_enterer_include, #data_enterer_not, #cite_title_words" )
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

	$.ajax({
		type: 'GET',
		url: "/min_date.php",
		dataType: "html",
		async: false,
		success: function(h) {
			min_date = h;
		}
	});

	$.ajax({
		type: 'GET',
		url: "/max_date.php",
		dataType: "html",
		async: false,
		success: function(h) {
			max_date = h;
		}
	});


	$( "#clipdate1, #clipdate2" ).datepicker({
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		constrainInput: false,
		minDate: min_date,
		maxDate: max_date,
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
				$( "#clipdate_msg" ).html("No day was specified. Substituting &ldquo;00.&rdquo;").css("color", "goldenrod");
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
				$( "#clipdate_msg" ).html("No day was specified. Substituting &ldquo;00.&rdquo;").css("color", "goldenrod");
				$( this ).css("border-color","goldenrod");
			}
			if (yo.test(t) === true) {
				var j = /[0-9]{4}/;
				if (j.test(t) === false) {
					var k = t.slice(-2);
					var t = "20" + k;
				}
				$( this ).val(t + "-00-00");
				$( "#clipdate_msg" ).html("Only a year was specified. Substituting &ldquo;00-00&rdquo; for month and date.").css("color", "goldenrod");
				$( this ).css("border-color","goldenrod");		
			}
		}
		else {
			if (g.test(t) === false && this.value != "") {
				var a = Date.parse(this.value);
				a = new Date(a);
				var year = a.getFullYear();
				var month = "0" + (a.getMonth()+1);
				month = month.slice(-2);
				var date = "0" + a.getDate();
				date = date.slice(-2);
				$( this ).val(year + "-" + month + "-" + date);
			}
			if ( this.value == "NaN-aN-aN" && this.value != "") {
				$( this ).val("");
				$( "#clipdate_msg" ).append("That was not a recognizable date.").css("color", "red");
				$( this ).css("border-color","red");
			}
			else {
				$( "#clipdate_msg" ).html("").css("color", "initial");
				$( this ).css("border-color","initial");		
			}
		}
	});
});

function snipSearchReset(event) {
	$("#snipsearch div[id$='msg']" ).html("");
	$("#snipsearch input, select" ).css("border-color", "initial");
	$("#snipsearch input" ).val("");
	snipSearchMe();
//	querytext = " quote LIKE '%' ";
//	checkUrl(querytext);
}