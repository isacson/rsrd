// initialize arrays of categories and ids that have been chosen by the user.

$(function() {
	$( "#mainsearch" ).focus();
	querytext = {
		quotesql: "",
		havingsql: ""
	};
	checkb = {
		prisec: "",
		language: "",
		important: "",
		is_audio: 1,
		is_image: 2,
		ping: 1
	};
	browse = {
	// "category" is an array of types of things we'd be searching for.
	// the categories I have in mind are "country," "program," "tag," and "agency."
	// each category array has the name to be used in searches, the name to show on screen,
	// and a sub-array of all possible items that fit the category (like a list of all countries).

	    category: [
	        {
	            name: "",
	            show: "",
	            items: [
	                {
	                    id: "",
	                    occurs: ""
	                }
	            ]
	        }
	    ]
	};

	quoteblank = "";
	justthequote = "";

	//initialize an object to hold all the values in the checkboxes. They start out all checked
	//initialize an object to hold page number and number of clips per page. They start out as 1 and 20.
	page = {
		mypage: 1,
		perpage: 20
	};

	searchchosen = 0;
	searchused = 0;
	clickedcats = [];
	clickedids = [];
	changedpage = 0;
	checkUrl();
//	getIds(checkb, "", querytext);
});

// make "browse," an object that holds all active search terms the user can choose by clicking choices.


function emptyBrowse() {
	// this function empties the "browse" search object.
	browse = {
	    category: [
	        {
	            name: "",
	            show: "",
	            items: [
	                {
	                    id: "",
	                    occurs: ""
	                }
	            ]
	        }
	    ]
	};
	return;
};

function emptyPage() {
	page = {
		mypage: 1,
		perpage: 20
	};
	return;
}

function paginateLink(a,b,total) {
//	console.log("a,b,total: " + a + "," + b + "," + total);
	event.preventDefault;
	if (a > total) {
		a = total;
	}
	page.mypage = a;
	if (page.mypage > total) {
		page.mypage = total;
	}
	page.perpage = b;
//	var newtotal = Math.ceil(total*page.perpage/page.mypage);
	if (page.mypage > total) {
		page.mypage = total;
	}
	var tresults = Math.ceil(total / page.perpage);
	if (page.mypage > tresults) {
		page.mypage = 1;
	}
	changedpage = 1;
	return(checkUrl(querytext));
}

function paginateButt(logged, total) {
	event.preventDefault;
	var a = $( "#getpage" ).val();
	var b = $( "#getclipspage" ).val();

	var c = /^[0-9]{1,5}$/;

	if (c.test(a) === true) {
		page.mypage = a;
	}
	if (c.test(b) === true) {
		if (logged != 1) {
			if (b > 50) {
				b = 50;
			}
		}
		page.perpage = b;
	}
	var tresults = Math.ceil(total / page.perpage);
	if (page.mypage > tresults) {
		page.mypage = 1;
	}
	$('html, body').animate({ scrollTop: 0 }, 'fast');
	
	changedpage = 1;
	return(checkUrl(querytext));	
}

function paginateButta(logged, total, normalformat, event) {
	var a = $( "#getpagea" ).val();
	var b = $( "#getclipspagea" ).val();

	var c = /^[0-9]{1,5}$/;

	if (c.test(a) === true) {
		page.mypage = a;
	}
	if (c.test(b) === true) {
		if (logged != "zaxxon") {
			if (b > 50) {
				b = 50;
			}
		}
		page.perpage = b;
	}
	var tresults = Math.ceil(total / page.perpage);
	if (page.mypage > tresults) {
		page.mypage = 1;
	}
	changedpage = 1;
	$('html, body').animate({ scrollTop: 0 }, 'fast');

	return(checkUrl(normalformat));	
}

function fromCatslist(cat, value) {
// this function is activated when the user clicks one of the category results in the snippet on the right of the page.
	// first, get rid of whatever's in the navigation on the left
	$( "#lbcontent" ).empty();
	// also, get rid of the clicked categories listed above the left title. All of them up to the content container.
	$( "#lbtitletext" ).nextUntil( "#lbcontent" ).remove();
	// can't have spaces in the div id names, replace with _
	var p = replaceAll(" ", "_", value);
	// add new clicked category before title on the left.
	$( "#lbtitle" ).append("<span class='item_title' id='" + cat + "' onClick='oblitItem(\"" + cat + "\",\"" + p + "\")' >x " + value + "<br></span>");
	$ ( "#" + cat ).hide().fadeIn("slow");
	// clear out clicked categories and items
	clickedcats = [];
	clickedids = [];
	// add the category and item that were just clicked
	clickedcats.push(cat);
	clickedids.push(value);
	// empty the "browse" search variable and repopulate it, then do the getIDs function
//	emptyBrowse();
	querytext = {
		quotesql: "",
		havingsql: ""
	};
	browse.category[0].name = cat;
	browse.category[0].items[0].id = value;
	$( "#browsebutton" ).attr("class", "browse_search_butt_sel");
	$( "#searchbutton" ).attr("class", "browse_search_butt_unsel");
	return(checkUrl());
}

function checkUrl(normalformat) {
	checkb = {
		prisec: "",
		language: "",
		important: "",
		is_audio: 1,
		is_image: 2,
		ping: 1
	};
	if ($( "#primarysource" ).prop('checked') === true) {
		
		if ($( "#secondarysource" ).prop('checked') === true) {

			checkb.prisec = "";
		}
		else {

			checkb.prisec = 1;
		}
	}
	else {

		if ($( "#secondarysource" ).prop('checked') === true) {

			checkb.prisec = 0;
		}
		else {

			checkb.prisec = "";
			$( "#primarysource" ).prop('checked', true);
			$( "#secondarysource" ).prop('checked', true);

		}
	}

	if ($( "#english" ).prop('checked') === true) {

		if ($( "#spanish" ).prop('checked') === true) {

			checkb.language = "";
		}
		else {
			checkb.language = "English";
		}
	}
	else {
		if ($( "#spanish" ).prop('checked') === true) {

			checkb.language = "Spanish";
		}
	else {
			checkb.language = "";
			$( "#english" ).prop('checked', true);
			$( "#spanish" ).prop('checked', true);
		}
	}
	if ($( "#important" ).prop('checked') === true) {
		checkb.important = 1;
	}
	if ($( "#important" ).prop('checked') === false) {
		checkb.important = 0;
	}	
	if (($( "#isaudio" ).prop('checked') === true) && ($( "#isimage" ).prop('checked') === false) && ($( "#istext" ).prop('checked') === false)) {
		checkb.is_audio = 1;
		checkb.is_image = 0;
		checkb.ping = 0;
	}
	if (($( "#isaudio" ).prop('checked') === true) && ($( "#isimage" ).prop('checked') === true) && ($( "#istext" ).prop('checked') === false)) {
		checkb.is_audio = 1;
		checkb.is_image = 2;
		checkb.ping = 0;
	}
	if (($( "#isaudio" ).prop('checked') === false) && ($( "#isimage" ).prop('checked') === true) && ($( "#istext" ).prop('checked') === true)) {
		checkb.is_audio = 0;
		checkb.is_image = 2;
		checkb.ping = 0;
	}
	if (($( "#isaudio" ).prop('checked') === false) && ($( "#isimage" ).prop('checked') === false) && ($( "#istext" ).prop('checked') === true)) {
		checkb.is_audio = 0;
		checkb.is_image = 0;
		checkb.ping = 0;
	}
	if (($( "#isaudio" ).prop('checked') === true) && ($( "#isimage" ).prop('checked') === false) && ($( "#istext" ).prop('checked') === true)) {
		checkb.ping = 1;
		checkb.is_audio = "";
		checkb.is_image = 0;
	}
	if (($( "#isaudio" ).prop('checked') === false) && ($( "#isimage" ).prop('checked') === true) && ($( "#istext" ).prop('checked') === false)) {
		checkb.is_audio = "";
		checkb.is_image = 2;
		checkb.ping = "";
	}
	if (($( "#isaudio" ).prop('checked') === true) && ($( "#isimage" ).prop('checked') === true) && ($( "#istext" ).prop('checked') === true)) {
		checkb.is_audio = "";
		checkb.is_image = 2;
		checkb.ping = 1;
	}
	if (($( "#isaudio" ).prop('checked') === false) && ($( "#isimage" ).prop('checked') === false) && ($( "#istext" ).prop('checked') === false)) {
		checkb.is_audio = "";
		checkb.is_image = 2;
		checkb.ping = 1;
		$( "#isaudio" ).prop('checked', true);
		$( "#isimage" ).prop('checked', true);
		$( "#istext" ).prop('checked', true);
	}

	if (querytext.quotesql != undefined && querytext.quotesql != "" && searchused == 1) {

		return(srchLbContentRefresh(normalformat));
	}
	else {
		emptyBrowse();
		$( "#lbcontent" ).empty();
		return(theClickedcats(normalformat));
	}
}

function theClickedcats(normalformat) {
	// this function takes whatever has been clicked (in the clickedcats and clickedids arrays) and populates the (presumably emptied) "browse" object with what it finds. 
	// There are 2 sub-functions: one if there's no clicked categories (which replaces the empty stuff in the [0] key of the browse category and item arrays), and one if there already are some clicked categories, which goes on to populate the next one(s).

	if(clickedcats.length == 1) {

		noClickedcats();
	}

	if(clickedcats.length == 2) {

		noClickedcats();
		moreClickedcats(1);
	}

	if(clickedcats.length == 3) {

		noClickedcats();
		moreClickedcats(1);
		moreClickedcats(2);
	}

	if(clickedcats.length == 4) {

		noClickedcats();
		moreClickedcats(1);
		moreClickedcats(2);
		moreClickedcats(3);
	}

	var	i = browse.category.length;

	if (i == 5) {
		emptyBrowse();
		clickedcats = [];
		clickedids = [];
		getIds(normalformat);
	}
	return(getIds(normalformat));
}

function getIds(normalformat) {
	// this function pulls from the "browse" variable a list of category and item names, for the navigation on the left of the snippet page, then sends them to the all-powerful lbContentRefresh function.
	for (var i = 0; i < browse.category.length; i++) {
		switch(browse.category[i].name) {
			case "country":
				var country = browse.category[i].items[0].id;
				break;
			case "program":
				var program = browse.category[i].items[0].id;
				break;
			case "tag":
				var tag = browse.category[i].items[0].id;
				break;
			case "agency":
				var agency = browse.category[i].items[0].id;
				break;
			default:
				var country = "";
				var program = "";
				var tag = "";
				var agency = "";
				break;
		}
	}
	return(lbContentRefresh(country,program,tag,agency,normalformat));
}

function lbContentRefresh(country, program, tag, agency, normalformat) {
	// this function takes the variables extracted from "browse" and uses them to perform ajax calls that update the rest of the snippet page.

	$( "#lbcontent" ).html("");

	// first, make sure that even if undefined, the four categories show up in the GET url string.
	if (country === undefined) {
		country = "";
	}
	if (program === undefined) {
		program = "";
	}
	if (tag === undefined) {
		tag = "";
	}
	if (agency === undefined) {
		agency = "";
	}

	if (changedpage == 0) {
		page.mypage = 1;
	}

	$.ajax({
		type: 'POST',
		url: "num_results.php",
		data:  {
			prisec: checkb.prisec,
			language: checkb.language,
			important: checkb.important,
			is_audio: checkb.is_audio,
			is_image: checkb.is_image,
			ping: checkb.ping,
			page: page.mypage,
			perpage: page.perpage,
			quotequery: querytext.quotesql,
			havingquery: querytext.havingsql,
			country: country,
			program: program,
			tag: tag,
			agency: agency
		},
		async: false,
		dataType: 'json',
		success: function(json) {
			results = json.results;
			page.mypage = json.page;
			totalpages = json.totalpages;
			pgbeg = json.pgbeg;
			pgend = json.pgend;
			page.perpage = json.perpage;
			pageminusone = json.pageminusone;
			pageplusone = json.pageplusone;
			if (results != 1) {
				$( "#lbtitletext" ).html(results + " clips in the database about:");
			}
			else {
				$( "#lbtitletext" ).html(results + " clip in the database about:");				
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log("An error occurred! " + errorThrown);
		}
	})

	$.ajax({
		type: 'POST',
		url: "lbcontent.php",
		data:  {
			prisec: checkb.prisec,
			language: checkb.language,
			important: checkb.important,
			is_audio: checkb.is_audio,
			is_image: checkb.is_image,
			ping: checkb.ping,
			quotequery: querytext.quotesql,
			country: country,
			program: program,
			tag: tag,
			agency: agency
		},
		async: false,
		dataType: 'json',
		success: function(json) {
			browse = json;
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log("An error occurred! " + errorThrown);
		}
	})

	// send that url to the small php page that generates the title on the right of the snippet page (number of Clips)
	$.ajax({
		type: 'POST',
		url: "rbtitle.php",
		data:  {
			allresults: results,
			page: page.mypage,
			totalpages: totalpages,
			pgbeg: pgbeg,
			pgend: pgend,
			perpage: page.perpage,
			pageminusone: pageminusone,
			pageplusone: pageplusone
		},
		dataType: 'html',
		success: function(data) {
			$( "#rbtitle" ).html(data);
			$( "#getpagea" ).val(page.mypage);
			$( "#getclipspagea" ).val(page.perpage);
		}
	})

	// send that url to the big php page that generates the Clips themselves, on the right
	if (normalformat == 1) {
		$.ajax({
			type: 'POST',
			url: "rbcontxt.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				allresults: results,
				page: page.mypage,
				totalpages: totalpages,
				pgbeg: pgbeg,
				pgend: pgend,
				perpage: page.perpage,
				pageminusone: pageminusone,
				pageplusone: pageplusone,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql,
				country: country,
				program: program,
				tag: tag,
				agency: agency
			},
			dataType: 'html',
			success: function(data) {
				$( "#rbcontent" ).html(data);
				$( "#getpage" ).val(page.mypage);
				$( "#getclipspage" ).val(page.perpage);
				$( "#rbview" ).html("<span id='rbviewas' onclick='paginateButta(\"\", \"\", 0)'>View this page in normal format</span>");
			}
		})
	}
	if (normalformat == 2) {
		$.ajax({
			type: 'POST',
			url: "rbcontxt.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				allresults: results,
				page: page.mypage,
				totalpages: totalpages,
				pgbeg: pgbeg,
				pgend: pgend,
				perpage: page.perpage,
				pageminusone: pageminusone,
				pageplusone: pageplusone,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql,
				country: country,
				program: program,
				tag: tag,
				agency: agency
			},
			dataType: 'html',
			success: function(h) {
				var fname = new Date();
				var x=window.open();
				if (x == undefined) {
					alert("Your browser's pop-up blocker is preventing you from downloading the plain text.");
				}
				else {
					x.document.open();
					x.document.write(h);
					x.document.close();
				}
			}
		})
	}
	if (normalformat != 1 && normalformat != 2) {
		$.ajax({
			type: 'POST',
			url: "rbcontent.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				allresults: results,
				page: page.mypage,
				totalpages: totalpages,
				pgbeg: pgbeg,
				pgend: pgend,
				perpage: page.perpage,
				pageminusone: pageminusone,
				pageplusone: pageplusone,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql,
				country: country,
				program: program,
				tag: tag,
				agency: agency
			},
			dataType: 'html',
			success: function(data) {
				$( "#rbcontent" ).html(data);
				$( "#getpage" ).val(page.mypage);
				$( "#getclipspage" ).val(page.perpage);
			}
		})
	}

	if (querytext.quotesql != "" && querytext.quotesql != " quote LIKE '%' ") {
		$("<form/>", {
			id: "quoteb",
			html: "Word(s) in the <strong>clip text</strong><br>"
		})
		.appendTo( "#lbcontent" );

		$( "<input />", {
			id: "quotebrowse",
			name: "quotebrowse",
			type: "text",
			tabindex: 5,
			maxlength: 100,
			placeholder: "phrases in quotes; OK to use 'and,' 'or,' 'not'",
			val: quoteblank
		})
		.appendTo( "#quoteb" );

		$( "<button />", {
			id: "quotebrowsebutt",
			tabindex: 6,
			value: "Go",
			html: "Go"
		})
		.appendTo( "#quoteb" );

		$( "<button />", {
			type: "reset",
			id: "quotebrowseresbutt",
			tabindex: 7,
			value: "Clear",
			html: "Clear"
		})
		.appendTo( "#quoteb" );

		$( "#quotebrowsebutt" ).on("click", function(event) {

			event.preventDefault();
			searchButt("quotebrowse");
		});

		$( "#quotebrowseresbutt" ).on("click", function(event) {

			event.preventDefault();
			$( "#quotebrowse" ).val("");
			searchButt("quotebrowse");
		});
	}

	// turn the "browse" object into a navigation menu on the left of the page
	// get each category name
	$.each(browse.category, function(key, value) {
		// "now" is the category name
		var now = this.name;
		// show the category name
		$("<div/>", {
			class: "browsecategory",
			id: now,
			// make it toggle the corresponding items list
			click: function () {
				$( "#" + now + "_items_list" ).toggle();
			},
			html: this.show + " (" + this.items.length + ")",
		})
		.appendTo( "#lbcontent" );

		// the items list that appears when the category name is clicked
		$("<div/>", {
			id: now + "_items_list"
		})
		.appendTo( "#lbcontent")
		.hide();

		// generate the items list
		$.each(this.items, function(key, value) {
			// nitem is the item name
			var nitem = this.id;
			$("<div/>", {
				class: "items_list",
				id: this.id,
				click: function() {
					var p = replaceAll(" ", "_", nitem);
					// when the item name is clicked, add it to the top of the left column with the option to obliterate it if clicked
					$( "#lbtitle" ).append("<span class='item_title' id='" + now + "' onClick='oblitItem(\"" + now + "\",\"" + p + "\")' >x " + nitem + "<br></span>");
					$ ( "#" + now ).hide().fadeIn("slow");
					// when the item name is clicked, clear the right column
					$( "#lbcontent" ).empty();
					// add the clicked item and its category to the arrays of what was clicked
					clickedcats.push(now);
					clickedids.push(nitem);

					// empty out the "browse" variable and repopulate it with everything that's been clicked
					emptyBrowse();
					checkUrl();
				},
				html: this.id + " (" + this.occurs + ")",
			})
			.appendTo( "#" + now + "_items_list" )
		});
	});

	changedpage = 0;
}

function srchLbContentRefresh(normalformat) {
	// this function takes the variables extracted from "search" and uses them to perform ajax calls that update the rest of the snippet page.

if (checkb === undefined) {
		checkb = {
		prisec: "",
		language: "",
		important: "",
		is_audio: "",
		is_image: "",
		ping: ""
	};
}

if (changedpage == 0) {
	page.mypage = 1;
}

	// console.log(lbcontenturl + checkburl);
	
	// send that url to the php result page that generates a json file of the "browse" object resulting from the user's click

	if (searchchosen == 1) {
		$.ajax({
			type: 'POST',
			url: "/lbsearch.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				page: page.mypage,
				perpage: page.perpage,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql
			},
			async: false,
			dataType: 'html',
			success: function(h) {
				if (searchused != 1) {
					$( "#lbcontent" ).html(h);
				}
//				if ( $( "#mainsearch" ).val() != "" && $( "#mainsearch" ).val() != undefined) {
//					var a = $( "#mainsearch" ).val();
				$( "#quotesearch" ).val(quoteblank);
				searchchosen = 0;
				$( "#mainsearch" ).val("");
//				}
			},
			error: function(xhr, textStatus, errorThrown) {
				console.log("An error occurred! " + errorThrown);
			}
		})
	}
	// send that url to the small php page that generates the title on the left of the snippet page (number of Clips)


	$.ajax({
		type: 'POST',
		url: "num_results.php",
		data:  {
			prisec: checkb.prisec,
			language: checkb.language,
			important: checkb.important,
			is_audio: checkb.is_audio,
			is_image: checkb.is_image,
			ping: checkb.ping,
			page: page.mypage,
			perpage: page.perpage,
			quotequery: querytext.quotesql,
			havingquery: querytext.havingsql,
		},
		async: false,
		dataType: 'json',
		success: function(json) {
			results = json.results;
			page.mypage = json.page;
			totalpages = json.totalpages;
			pgbeg = json.pgbeg;
			pgend = json.pgend;
			page.perpage = json.perpage;
			pageminusone = json.pageminusone;
			pageplusone = json.pageplusone;
			if (results != 1) {
				$( "#lbtitletext" ).html(results + " clips in the database about:");
			}
			else {
				$( "#lbtitletext" ).html(results + " clip in the database about:");				
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log("An error occurred! " + errorThrown);
		}
	})

	// send that url to the small php page that generates the title on the right of the snippet page (number of Clips)
	$.ajax({
		type: 'POST',
		url: "rbtitle.php",
		data:  {
			allresults: results,
			page: page.mypage,
			totalpages: totalpages,
			pgbeg: pgbeg,
			pgend: pgend,
			perpage: page.perpage,
			pageminusone: pageminusone,
			pageplusone: pageplusone
		},
		dataType: 'html',
		success: function(data) {
			$( "#rbtitle" ).html(data);
			$( "#getpagea" ).val(page.mypage);
			$( "#getclipspagea" ).val(page.perpage);
		}
	})

	// send that url to the big php page that generates the Clips themselves, on the right
	if (normalformat == 1) {
		$.ajax({
			type: 'POST',
			url: "rbcontxt.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				allresults: results,
				page: page.mypage,
				totalpages: totalpages,
				pgbeg: pgbeg,
				pgend: pgend,
				perpage: page.perpage,
				pageminusone: pageminusone,
				pageplusone: pageplusone,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql
			},
			dataType: 'html',
			success: function(data) {
				$( "#rbcontent" ).html(data);
				$( "#getpage" ).val(page.mypage);
				$( "#getclipspage" ).val(page.perpage);
				$( "#rbview" ).html("<span id='rbviewas' onclick='paginateButta(\"\", \"\", 0)'>View this page in normal format</span>");
			}
		})
	}
	if (normalformat == 2) {
		$.ajax({
			type: 'POST',
			url: "rbcontxt.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				allresults: results,
				page: page.mypage,
				totalpages: totalpages,
				pgbeg: pgbeg,
				pgend: pgend,
				perpage: page.perpage,
				pageminusone: pageminusone,
				pageplusone: pageplusone,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql
			},
			dataType: 'html',
			success: function(h) {
				var fname = new Date();
				var x=window.open();
				if (x == undefined) {
					alert("Your browser's pop-up blocker is preventing you from downloading the plain text.");
				}
				else {
					x.document.open();
					x.document.write(h);
					x.document.close();
				}
			}
		})
	}
	if (normalformat != 1 && normalformat != 2) {
		$.ajax({
			type: 'POST',
			url: "rbcontent.php",
			data:  {
				prisec: checkb.prisec,
				language: checkb.language,
				important: checkb.important,
				is_audio: checkb.is_audio,
				is_image: checkb.is_image,
				ping: checkb.ping,
				page: page.mypage,
				perpage: page.perpage,
				quotequery: querytext.quotesql,
				havingquery: querytext.havingsql,
				allresults: results,
				page: page.mypage,
				totalpages: totalpages,
				pgbeg: pgbeg,
				pgend: pgend,
				perpage: page.perpage,
				pageminusone: pageminusone,
				pageplusone: pageplusone,
			},
			dataType: 'html',
			success: function(data) {
				$( "#rbcontent" ).html(data);
				$( "#getpage" ).val(page.mypage);
				$( "#getclipspage" ).val(page.perpage);
			}
		})
	}

	changedpage = 0;
}

function oblitItem(item, nitem) {
	// this function is what happens when the user clicks one of the struck-through chosen items, making it "un-clicked."
	// put spaces back into the item id names received from the GET url
	var p = replaceAll("_", " ", nitem);
	// find out if what was clicked matches something in the categories and items arrays. If so, delete it from both.
	var q = clickedcats.indexOf(item);
	var r = clickedids.indexOf(p);

	if (q > -1) {
		clickedcats.splice(q, 1);
	}

	if (r > -1) {
		clickedids.splice(r, 1);
	}
	// delete the item from the list above the title
	$( "#" + item ).remove();

	// empty the navigation on the left in order to repopulate it
	$( "#lbcontent" ).empty();
	
	// empty the "browse" object and repopulate it with whatever categories and items remain clicked
	emptyBrowse();
	return(checkUrl());
}

function noClickedcats() {

	browse.category[0].name = clickedcats[0];
	browse.category[0].items[0].id = clickedids[0];
	return;
}

function moreClickedcats(n) {

		browse.category[n] = {
			name: "",
			show: "",
			items: []
		};
		browse.category[n].name = clickedcats[n];
		browse.category[n].items[0] =  {
    		id: "",
    		occurs: ""
		};
		browse.category[n].items[0].id = clickedids[n];
		return;
}

function replaceAll(find, replace, str) {
	// a handy function because javascript doesn't have a function that lets you replace everytime something appears in a string (it only does it once)
	return str.replace(find, replace);
}

function selectClip(quotekey) {
	 var doc = document
        , text = document.getElementById( "clip" + quotekey )
        , range, selection
    ;    
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}
function selectFoot(quotekey) {
	 var doc = document
        , text = document.getElementById( "footnote" + quotekey )
        , range, selection
    ;    
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

function htmlEntities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/’/g, '&rsquo;').replace(/“/g, '&ldquo;').replace(/”/g, '&rdquo;').replace(/‘/g, '&lsquo;').replace(/—/g, '&mdash;').replace(/–/g, '&ndash;');
}

$( "#browsebutton" ).click(function() {
	$( this ).attr("class", "browse_search_butt_sel");
	$( "#searchbutton" ).attr("class", "browse_search_butt_unsel");
	$( "#lbtitletext" ).nextUntil( "#lbcontent" ).remove();
//	emptyBrowse();
	clickedcats = [];
	clickedids = [];
	searchused = 0;
	querytext = {
		quotesql: justthequote,
		havingsql: ""
	};
	return(checkUrl());
});

$( "#searchbutton" ).click(function() {
	$( this ).attr("class", "browse_search_butt_sel");
	$( "#browsebutton" ).attr("class", "browse_search_butt_unsel");
	$( "#lbtitletext" ).nextUntil( "#lbcontent" ).remove();
//	emptyBrowse();
	clickedcats = [];
	clickedids = [];
	$.ajax({
		type: 'GET',
		url: "lbsearch.php",
		dataType: 'html',
		success: function(data) {
			$( "#lbcontent" ).html(data);
			$( "#lbtitletext" ).html("Use this search form:");
			if (quoteblank != "" && quoteblank != undefined) {
				$( "#quotesearch" ).val(quoteblank).focus();
				snipSearchMe();
			}
		}
	})
});

$( "#mainsearchbutt" ).on("click", function(event) {

	event.preventDefault();
	searchButt("mainsearch");
});

function searchButt(v) {

	querytext = {
		quotesql: "",
		havingsql: ""
	};
	searchused = 0;
// Search blank

	var a = $( "#" + v ).val();

	quoteblank = a;
	$( "#mainsearch, #quotebrowse" ).val(a);

	if (a != "") {

		quoteblank = a;
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

	searchchosen = 1;
	$( "#searchbutton" ).attr("class", "browse_search_butt_unsel");
	$( "#browsebutton" ).attr("class", "browse_search_butt_sel");

	checkUrl();
}
