jQuery(document).ready( function ($) {

	var current_element 	= $(element),
	minibarFadeouttime 		= 200, // in ms
	minibarFadeintime 		= 500, // in ms
	minibarMenu 			= $("#smooth_minibar_menu"),
	minibarMenuNoSelect 	= $("#smooth_minibar_menu_noselect"),
	minibarMenuBoth 		= $("#smooth_minibar_menu, #smooth_minibar_menu_noselect"),
	$minibarCaretTextarea 	= $('<textarea/>'),
	$minibarCaretDiv 		= $('<div/>'),
	$minibarCaretSpan 		= $('<span/>');
	console.log(current_element);
	
	// caret pixel calculation helper
	$('body').append($minibarCaretTextarea).append($minibarCaretDiv).append($minibarCaretSpan);
	
	$minibarCaretTextarea.css({
		'position':'absolute',
		'left':'-9999px',
		'overflow-y':'scroll',
		'width':current_element.width(),
		'height':'1em',
		'font':current_element.css('font')
	});
	
	$minibarCaretDiv.css({
		'display':'none',
		// 4 debugging
		'position':'fixed',
		'top':'0px',
		'left':'0px',
		'background-color':'blue',
		'width':current_element.width(),
		'overflow-y':'scroll',
		'padding':current_element.css('padding'),
		'font':current_element.css('font')
	}).text('a');
	
	$minibarCaretSpan.css({
		'display':'none',
		// 4 debugging
		'position':'fixed',
		'bottom':'30px',
		'left':'0px',
		'background-color':'red',
		'font':current_element.css('font')
	 });

	// hide on mousedown
	current_element.bind('scroll mousedown',function() {
		minibarMenuBoth.fadeOut(minibarFadeouttime);
	});
	
	// first minibar menu
	current_element.select(function() {
		var before = $(this).val().substring(0,$(this).caret().start),
		minibarCaretDivHeight 	= $minibarCaretDiv.val('a').height(),
		minibarCaretDivContent 	= '',
		minibarCaretDivHTML 	= '',
		beforeLines 			= before.split('\n'),
		beforeWords 			= beforeLines[beforeLines.length - 1].replace(/\-/gi,' ').split(' ');
		beforeLines.pop();
		
		var beforeRemove 		= beforeLines.join('\n');
		
		for (i=0;i<beforeWords.length;i++) {
			minibarCaretDivHTML = (
				minibarCaretDivContent + ' ' + beforeWords[i]).replace(/[\r\n]/gi,
				'<br />'
			);
			$minibarCaretDiv.html(minibarCaretDivHTML);
			if ($minibarCaretDiv.height() > minibarCaretDivHeight) {
				beforeRemove += minibarCaretDivContent + ' ';
				minibarCaretDivContent = beforeWords[i];
			} else {
				minibarCaretDivContent += ' ' + beforeWords[i];
			}
		}
		
		var beforeLast = before.substring(beforeRemove.length);
		console.log(beforeRemove);
		$minibarCaretTextarea.val(before).scrollTop(10000);
		$minibarCaretSpan.text(beforeLast);
		
		var top = Math.max(
			current_element.offset().top,
			current_element.offset().top + (
				$minibarCaretTextarea.scrollTop() - current_element.scrollTop()
			)
		),
		left = current_element.offset().left + $minibarCaretSpan.width();
		minibarMenu.css({
			"top": top - 50,
			"left": left + 5
		}).fadeIn(minibarFadeintime);
	});
	
	// second minibar menu
	current_element.dblclick(function(e) {
		minibarMenuNoSelect.css({
			"top": e.pageY + 10,
			"left": e.pageX
		}).fadeIn(minibarFadeintime);
	});
	
	/****************************
	Aktionen werden über HTML5 data-Attribut am Knopf definiert.
	Markup Varianten:
	
	<a href="javascript:return false;" 
	data-minibar='{"wrapTextBefore":"<h3>","wrapTextAfter":"</h3>"}' 
	title="Heading">h3</a> 

	oder

	<a href="javascript:return false;" 
	data-minibar='{"wrapTextBefore":"<img \/>","wrapTextAfter":"","attributes":{"src":"Enter the URL of the image","alt":"Enter a description of the image"}}' 
	title="Image">img</a> 

	****************************/
	minibarMenuBoth.delegate('a','click',function(e){
		var data = jQuery.parseJSON($(this).attr('data-minibar')),
		wrapTextBefore = data.wrapTextBefore,
		wrapTextAfter = data.wrapTextAfter;
		
		if (typeof data.attributes != 'undefined') for (var key in data.attributes){
			var value = prompt(data.attributes[key],'');
			wrapTextBefore = wrapTextBefore.replace(/(\/?>)$/,' ' + key + '="' + value + '"$1');
		}
		
		wrapText(wrapTextBefore, wrapTextAfter);
		minibarMenuBoth.fadeOut(minibarFadeouttime);
		e.preventDefault();
	})
	
	function wrapText(startText, endText){
		// Get the text before the selection
		var before = current_element.val().substring(
			0,
			current_element.caret().start
		);
		// Get the text after the selection
		var after = current_element.val().substring(
			current_element.caret().end,
			current_element.val().length
		);
		// merge text before the selection, a selection wrapped with inserted text and a text after the selection
		current_element.val(
			before + startText + current_element.caret().text + endText + after
		);
	}
	
});