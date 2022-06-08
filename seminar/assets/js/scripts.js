
/**
 * @author MaiChau
 * @since 27/05/2016
 */
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

