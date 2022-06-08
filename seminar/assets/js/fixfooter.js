/**
 * show footer always at bottom
 * @author MaiChau
 * @since 06/05/2016
 */

function setGlobalFunc()
{    
   window.fixFooter = (function(){
	   var winH = window.innerHeight;
		var minH = winH - 60;
		$('#wrapper').css({'min-height':minH+'px'});
   });
} 
setGlobalFunc();

$(function(){
	$(window).load(function(){
		fixFooter();
		setTimeout(function(){fixFooter();},500);
	});
	$(window).resize(function(){fixFooter();});
	$(window).scroll(function(){fixFooter();});
});