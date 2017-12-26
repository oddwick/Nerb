
/*
(function ($, window, undefined) {
  'use strict';

  var $doc = $(document),
      Modernizr = window.Modernizr;

  $(document).ready(function() {
    $.fn.foundationAlerts           ? $doc.foundationAlerts() : null;
    $.fn.foundationButtons          ? $doc.foundationButtons() : null;
    $.fn.foundationAccordion        ? $doc.foundationAccordion() : null;
    $.fn.foundationNavigation       ? $doc.foundationNavigation() : null;
    $.fn.foundationTopBar           ? $doc.foundationTopBar() : null;
    $.fn.foundationCustomForms      ? $doc.foundationCustomForms() : null;
    $.fn.foundationMediaQueryViewer ? $doc.foundationMediaQueryViewer() : null;
    $.fn.foundationTabs             ? $doc.foundationTabs({callback : $.foundation.customForms.appendCustomMarkup}) : null;
    $.fn.foundationTooltips         ? $doc.foundationTooltips() : null;
    $.fn.foundationMagellan         ? $doc.foundationMagellan() : null;
    $.fn.foundationClearing         ? $doc.foundationClearing() : null;

    $.fn.placeholder                ? $('input, textarea').placeholder() : null;
  });

  // UNCOMMENT THE LINE YOU WANT BELOW IF YOU WANT IE8 SUPPORT AND ARE USING .block-grids
  // $('.block-grid.two-up>li:nth-child(2n+1)').css({clear: 'both'});
  // $('.block-grid.three-up>li:nth-child(3n+1)').css({clear: 'both'});
  // $('.block-grid.four-up>li:nth-child(4n+1)').css({clear: 'both'});
  // $('.block-grid.five-up>li:nth-child(5n+1)').css({clear: 'both'});

  // Hide address bar on mobile devices (except if #hash present, so we don't mess up deep linking).
  if (Modernizr.touch && !window.location.hash) {
    $(window).load(function () {
      setTimeout(function () {
        window.scrollTo(0, 1);
      }, 0);
    });
  }

})(jQuery, this);
*/
$(window).on('load', function () {
	
	$( "[data-maxchar]" ).on('input focus keyup keypress change', function(e) {
		countChar( this );
	});

	$( "[data-maxchar]" ).on('blur', function(e) {
		var label = $("[for=" + $( this ).prop('name') + "]");
		$(label).text(  $(label).attr('data-label') );
	});
	
	$("label").each(function() {
		$( this ).attr( 'data-label', $(this).text() );
	});
	
	//$(".msg-bar").delay(2000).slideUp();
	//$(".error-bar").delay(2000).slideUp();




	
 });
 
 
function toggleBatchFields(){
	
	var radioValue = $("input[name='mode']:checked").val();
	$(".field").hide();
	$("#mode_"+radioValue).show();
	
	
	//alert(field);
	
}	


function countChar( val ) {
	var len = val.value.length;
	var maxchar = $(val).attr('data-maxchar');
	var label = $("[for=" + $(val).prop('name') + "]");
	//alert( $(label).attr('data-label') );
	if (len > maxchar ) {
		val.value = val.value.substring(0, maxchar );
	} else {
		$(  "#" + $(val).prop('name') + "-charcount" ).text( maxchar - len);
		$(label).html(  $(label).attr('data-label') + "  &mdash; <em>" + (maxchar - len) +" char remaining</em>" );
	}
}


function outputUpdate( val, id, dir){

	$("#trim_"+dir+"_"+id).val(val);
	$("#trim_"+dir+"_output_"+id).val( val+"%");
	
	if(dir=="top"){
		$("#image-crop-guideline-"+id).css( "top", val+"%" );
	}else if(dir=="bottom"){
		$("#image-crop-guideline-"+id).css( "bottom", val+"%" );
	}else if(dir=="right"){
		$("#image-crop-guideline-"+id).css( "right", val+"%" );
	} else {
		$("#image-crop-guideline-"+id).css( "left", val+"%" );
	}
}


function resetSliders( id ){
	
	// left
	val = $("#trim_left_slide_"+id).attr('data-init-value');
	$("#trim_left_"+id).val( val );
	$("#trim_left_output_"+id).val(  val+"%" );
	$("#image-crop-guideline-"+id).css( "left",  val+"%" );
	$("#trim_left_slide_"+id).val( val );

	// right
	val = $("#trim_right_slide_"+id).attr('data-init-value');
	$("#trim_right_"+id).val( val );
	$("#trim_right_output_"+id).val(  val+"%" );
	$("#image-crop-guideline-"+id).css( "right",  val+"%" );
	$("#trim_right_slide_"+id).val( val );

	// top
	val = $("#trim_top_slide_"+id).attr('data-init-value');
	$("#trim_top_"+id).val( val );
	$("#trim_top_output_"+id).val(  val+"%" );
	$("#image-crop-guideline-"+id).css( "top",  val+"%" );
	$("#trim_top_slide_"+id).val( val );

	// bottom
	val = $("#trim_bottom_slide_"+id).attr('data-init-value');
	$("#trim_bottom_"+id).val( val );
	$("#trim_bottom_output_"+id).val(  val+"%" );
	$("#image-crop-guideline-"+id).css( "bottom",  val+"%" );
	$("#trim_bottom_slide_"+id).val( val );

}


$(function(){
   $(document).foundation({
     abide: {
       patterns: {
         password: /^(.){12,}$/
         }
       }
     });
 });
