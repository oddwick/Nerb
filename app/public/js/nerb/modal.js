
// pop modal window
$(window).on('load', function () {
	$( ".modal-window" ).click(function(event) {
		if( $(event.target).attr('class') == 'modal-window' ) {
			$( this ).fadeOut("fast");
		}
	});
		
		
	// modal trigger
	$( "[data-modal-id]" ).click(function(event) {
		$( "#" + $(this).attr('data-modal-id') ).fadeIn();
		centerModal();
	});
		
	$( "[data-modal-close]" ).click(function(event) {
		$("#"+$(this).attr('data-modal-close') ).fadeOut("fast");
	});
		
	$( "[data-alert-close]" ).click(function(event) {
		$("#"+$(this).attr('data-alert-close') ).fadeOut("fast");
	});
		
 });	


$(window).resize(function(){
	centerModal();
});

/**
 * centerModal function.
 * 
 * @access public
 * @return void
 */
function centerModal(){
	$( ".modal-content" ).position({
	    my: "center",
	    at: "center",
	    of: window,
	    using: function (pos) {
	        $(this).animate({ top: pos.top }, "fast", "linear" );
	    }
	});
	/*
    $('.modal-content').css({
        position:'absolute',
        left: ($(window).width() - $('.modal-content').outerWidth())/2,
        top: ($(window).height() - $('.modal-content').outerHeight())/2.5
    });
    */
	
}
