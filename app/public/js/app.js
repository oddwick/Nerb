/* Nurb framework app.js  */

/* 
-----------------------------------------------------
this script file is for global scripts to be used throughout the site

*/

$(window).on('load', function () {
		
	// toggles the menu for mobile devices
	$( "#nav-mobile-menu" ).on('click', function(e) {
		$("#nav-list").toggle();
	});

	// closes the message and error bars
	$("[data-close]").on('click', function() {
		$( "#" + $( this ).attr( 'data-close' )).hide();
	});
});
 

$(function(){
   $(document).foundation({
     abide: {
       patterns: {
         password: /^(.){12,}$/
         }
       }
     });
 });
