/**
 * @author MaiChau
 * @since 06/05/2016
 */

// Smartphone: when click Menu, table search will show if it is closing AND hide if it is opening
$(function(){
$('.icon_menu label').click(function(){
	if($(this).is('.clicked')){
		$(this).removeClass('clicked');
		$('#btnSearch').removeClass('clicked');
		$('.searchBlock').removeClass('searchAtv');	
	}
	else {
		$(this).addClass('clicked');
		$('.btnSearch').addClass('clicked');
		$('.searchBlock').addClass('searchAtv');		
	}
});
// Smartphone: when click button, table search will hide
$('#btnSearch').click(function(){
	if($(this).is('.clicked')){
		$(this).removeClass('clicked');
		$('.icon_menu label').removeClass('clicked');
		$('.searchBlock').removeClass('searchAtv');	
	}
	else {
		$(this).addClass('clicked');
		$('.icon_menu label').addClass('clicked');
		$('.searchBlock').addClass('searchAtv');		
	}
});

/* smoothscrollTo, pagetop.. */
$(window).scroll(function() {
	$('#pos').text($(this).scrollTop());
	if ($(this).scrollTop() > 50) {
		$("body").addClass("to-top-on");
	} else {
		$("body").removeClass("to-top-on");
	}
});
$("a[href ^= '#']").click(function() {
    var speed = 300;
    var href= $(this).attr("href");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top;
    $($.browser.safari ? 'body' : 'html').animate({
		scrollTop:position
	}, speed, 'easeOutExpo');
    return false;
});
});

