jQuery(document).ready( function ($) {

	var mouseX = 0;
	var mouseY = 0;
	
	var fadeouttime = 200; //in millisecond(ms); 1000ms = 1second
	var fadeintime  = 500; // in ms
	
	var now = now || new Date(),
		datetime = ISODateString(now);
	
	$(element).mousemove(function(e) {
		// track mouse position
		mouseX = e.pageX;
		mouseY = e.pageY;
	});
	// hide on mousedown
	$(element +', body').mousedown(function() {
		
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	// first minibar menu
	$(element).select(function() {
		// get the mouse position an show the menu
		$("#smooth_minibar_menu").css("top", mouseY - 30).css("left", mouseX + 10).fadeIn(fadeintime);
	});
	// second minibar menu
	$(element).dblclick(function() {
		// get the mouse position an show the menu
		$("#smooth_minibar_menu_noselect").css("top", mouseY + 5).css("left", mouseX).fadeIn(fadeintime);
	});
	
	$("#h3").click(function() {
		wrapText("<h3>", "</h3>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#h4").click(function() {
		wrapText("<h4>", "</h4>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#bold").click(function() {
		wrapText("<strong>", "</strong>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});

	$("#italic").click(function() {
		wrapText("<em>", "</em>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});

	$("#link").click(function() {
		var url = prompt("Enter URL", "http://");
		if (url != null)
			wrapText('<a href="' + url + '">', '</a>');
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});

	$("#blockquote").click(function() {
		wrapText("<blockquote>", "</blockquote>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#cite").click(function() {
		wrapText("<cite>", "</cite>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#delete").click(function() {
		wrapText('<del datetime="' + datetime + '">', '</del>');
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});

	$("#insert").click(function() {
		wrapText('<ins datetime="' + datetime + '">', '</ins>');
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#unorderedlist").click(function() {
		wrapText("<ul>\n", "\n</ul>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#orderedlist").click(function() {
		wrapText("<ol>\n", "\n</ol>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#list").click(function() {
		wrapText("\n<li>", "</li>\n");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#code").click(function() {
		wrapText("<code>", "</code>");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#pre").click(function() {
		wrapText("\n<pre>", "</pre>\n");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	// elements of second bar via dblclick
	$("#img").click(function() {
		var url = prompt("Enter the URL of the image", "http://");
		var alt = prompt("Enter a description of the image", "");
		if (alt != '' && alt != null)
			alt = ' alt="' + alt + '"';
		if (url != '' && url != null)
			wrapText('<img src="' + url + '"' + alt + ' />', '');
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#more").click(function() {
		wrapText("<!--more-->", "");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	$("#nextpage").click(function() {
		wrapText("<!--nextpage-->", "");
		$("#smooth_minibar_menu, #smooth_minibar_menu_noselect").fadeOut(fadeouttime);
	});
	
	function wrapText(startText, endText){
		// Get the text before the selection
		var before = $(element).val().substring( 0, $(element).caret().start );
		
		// Get the text after the selection
		var after = $(element).val().substring( $(element).caret().end, $(element).val().length );
		
		// merge text before the selection, a selection wrapped with inserted text and a text after the selection
		$(element).val( before + startText + $(element).caret().text + endText + after );
	}
	
	// Inspiriert von https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Date#Methods
	function ISODateString(d) {
		
		function pad(n) {
			return n<10 ? '0'+n : n
		}
		
		return d.getUTCFullYear()+'-'
		+ pad(d.getUTCMonth()+1)+'-'
		+ pad(d.getUTCDate())+'T'
		+ pad(d.getUTCHours())+':'
		+ pad(d.getUTCMinutes())+':'
		+ pad(d.getUTCSeconds())+'+00:00'
	}
});
