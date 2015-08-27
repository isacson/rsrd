function O(obj) {
	if (typeof obj == 'object') return obj
	else return document.getElementById(obj)
}

function S(obj) {
	return O(obj).style
}

function C(name) {
	var elements 	= document.getElementsByTagName('*')
	var objects		= []

	for (var i=0 ; i<elements.length ; ++i)
		if (elements[i].classname == name)
			objects.push(elements[i])

	return objects
}

loggcount=1;

$( "#logg" ).click(function() {
	$( "#loginform" ).empty();
	$("<div/>", {
		id: "logins"
	}).appendTo( "#loginform" );
	$("<div/>", {
		id: "logform",
		html: "<div id='thelogintext'>WOLA staff have access to additional parts of the database. If you have a login and password, enter them here.</div><form id='theloginform' action='login.php' method='post'><label>Login: </label><input type='text' name='login' id='login'><br><label>Password: </label><input type='password' name='password' id='password'><br><input type='submit' value='submit'></form>"
	}).appendTo( "#logins" );
	$("<div/>", {
		id: "dontlogin",
		html: "<div id='dontlogintext'>I haven&rsquo;t been given a password. I&rsquo;ll use the public version of the database.</div>",
		click: 	function() {
			$( "#loginform" ).slideUp( 400 );
			$( "#loginform" ).html( "<br>" );
			$( "#loginform" ).slideDown( 0 );
		}
	}).appendTo( "#logins" );
	$( "#loginform" ).append( "<br>" );

	$( "#loginform" ).hide().slideDown( 400 );
})

areatitle = $( '.area_title' ).html();
if ($( ".header_button[title= '" + areatitle+ "']" ).attr("title") == areatitle)  {
	$( ".header_button[title= '" + areatitle+ "']" ).addClass( "selected_header" );
}

xc = $( "div[title='" + areatitle + "']" ).attr("title");