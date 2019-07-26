
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
			//if( $(event.target).attr('class') == 'modal-window' ) {
				//$( this ).fadeOut();
			//}   
			centerModal();

		});
		
		
		// flyout trigger
		$( "[data-flyout-id]" ).click(function(event) {
			toggleFlyout( $( "#" + $(this).attr('data-flyout-id')) );
		});
		
		$( ".flyout" ).click(function(event) {
			if( $(event.target).attr('class') == 'flyout' ) {
				toggleFlyout( this );
			}
		});
		
		
		$( "[data-modal-close]" ).click(function(event) {
			if( $(this).parent().parent().attr('class') == 'modal-window' ){
				$(this).parent().parent().fadeOut("fast");
			} else if( $(this).attr('data-modal-close') != '' ){
				$("#"+$(this).attr('data-modal-close') ).fadeOut("fast");
			}
		});
		
		
		$( "[data-alert-close]" ).click(function(event) {
			if( $(this).attr('data-alert-close') != '' ){
				$("#"+$(this).attr('data-alert-close') ).fadeOut("fast");
			} else {
				$(this).parent().fadeOut("fast");
			}
		});
		
		
 });	







//data-flyout-id="stamp-list"




function toggleFlyout( id ){
	if( $( id ).is(":visible") ){
		$("[data-flyout-panel]").toggle("slide", {direction:'right', speed:'fast'}, function (){
			$( id ).fadeOut("fast");
		});
	} else {
		$( id ).fadeIn("fast", function(){
			$("[data-flyout-panel]").toggle("slide", {speed: 100, direction:'right'} );
		});
	}
}





$(window).resize(function(){
	centerModal();
});


function centerModal(){
	
	$( ".modal-content" ).position({
	    my: "center",
	    at: "center",
	    of: window,
	    using: function (pos, ext) {
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


function toggleModalDialog( target, text1, text2 ){
	
	currentText = $(event.target).text();
	$(event.target).text( currentText == text1 ? text2 : text1 );
	$('#'+target ).slideToggle( 200, function(){
		 centerModal();
		 });
	//centerModal(); 
	//alert( $(event.target).text() );
	
}

/*
function openModal(id){
	$('#rating-'+id).load('/api.php?mode=likePage&id='+id);		
}

function closeModal(id){
	$(id).hide();		
}
*/
