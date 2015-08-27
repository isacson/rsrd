function replaceAll(find, replace, str) {
	// a handy function because javascript doesn't have a function that lets you replace everytime something appears in a string (it only does it once)
	return str.replace(find, replace);
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

	var focid = $( "#replace_cat option:selected" ).text();

	availableTags = "";

	$( "#replace_blank1, #replace_blank2" ).focus(function() {
		var focid = $( "#replace_cat option:selected" ).text();
		if (focid == "government agency") {
			focid = "agency";
		}
		availableTags = aTags(focid);
	});

	function split( val ) {
	  return val.split( /,\s*/ );
	}

	function extractLast( term ) {
	  return split( term ).pop();
	}

	$( "#replace_blank1, #replace_blank2" )
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

	$( "#cat_replace_butt" ).click(function(event) {
		event.preventDefault();
		var catReplaceBlank1 = $( "#replace_blank1" ).val();
		catReplaceBlank1 = "\"" + catReplaceBlank1 + "\"";
		var focid = $( "#replace_cat option:selected" ).text();
		if (focid == "government agency") {
			focid = "agency";
		}
		$.ajax({
			type: 'GET',
			url: "/jsonmaker.php?val=" + focid,
			dataType: "text",
			success: function(h) {
				if (h.indexOf(catReplaceBlank1) == -1) {
					$( "#replace_cat_msg" ).html("Sorry, there&rsquo;s no " + focid + " in the database called " + catReplaceBlank1 + ". Want to try that again?").css("color", "red");
				}
				else {
					$( "#replace_cat_msg" ).html("");
					var catReplaceBlank2 = $( "#replace_blank2" ).val();
					catReplaceBlank2 = "\"" + catReplaceBlank2 + "\"";
					$.ajax({
						type: 'GET',
						url: "/jsonmaker.php?val=" + focid,
						dataType: "text",
						success: function(h) {
							if (h.indexOf(catReplaceBlank2) == -1) {
								$( "#replace_cat_txt" ).val("rename");
								$( "#replace_cat_msg" ).html("You are about to rename " + catReplaceBlank1 + " as " + catReplaceBlank2 + " a brand-new " + focid + ". The " + focid + " name " + catReplaceBlank1 + " will no longer appear in the database. It will be gone. Is that what you want to do? <button id='confirm_tag_replace' value='Yes' onclick='catRep(1)'>Yes it is</button>").css("color", "goldenrod");
							}
							else {
								$( "#replace_cat_txt" ).val("replace");
								$( "#replace_cat_msg" ).html("You are about to replace " + catReplaceBlank1 + " with " + catReplaceBlank2 + ", a previously existing " + focid + ". The " + focid + " name " + catReplaceBlank1 + " will no longer appear in the database. It will be gone. Is that what you want to do? <button id='confirm_tag_replace' value='Yes' onclick='catRep(2)'>Yes it is</button>").css("color", "goldenrod");							
							}
						}
					})
				}
			}
		});
	});

});

function catRep(val) {
		var catReplaceBlank1 = $( "#replace_blank1" ).val();
		var focid = $( "#replace_cat option:selected" ).text();
		var catReplaceBlank2 = $( "#replace_blank2" ).val();

	if (val == 1) {
		$.ajax ({
			type: 'GET',
			url: "/replacetag.php?table=" + focid + "&b1=" + catReplaceBlank1 + "&b2=" + catReplaceBlank2 + "&val=1",
			datatype: "html",
			success: function(h) {
				$( "#replace_cat_msg" ).html(h).css("color","green");
				$( "#replace_blank1, #replace_blank2" ).val("");
			}
		})
	}
	if (val == 2) {
		$.ajax ({
			type: 'GET',
			url: "/replacetag.php?table=" + focid + "&b1=" + catReplaceBlank1 + "&b2=" + catReplaceBlank2 + "&val=2",
			datatype: "html",
			success: function(h) {
				$( "#replace_cat_msg" ).html(h).css("color","green");
				$( "#replace_blank1, #replace_blank2" ).val("");
			}
		})
	}
}

function htmlEntities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/’/g, '&rsquo;').replace(/“/g, '&ldquo;').replace(/”/g, '&rdquo;').replace(/‘/g, '&lsquo;').replace(/—/g, '&mdash;').replace(/–/g, '&ndash;');
}