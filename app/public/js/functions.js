
$(function() {
    $('.selectable').each(function() {
						
	$(this).click(function() {
		$(this).not(".used").toggleClass('selected');
		check = $(this).find("input[type=checkbox]");
		if ($(this).hasClass('selected')) {
			check.not("[disabled]").prop("checked", true);
		} else {
			check.not("[disabled]").prop("checked", false);
		}
	});
						
/*        var span = $('<span class="' + $(this).attr('type') + ' ' + $(this).attr('class') + '"></span>').click(doCheck).mousedown(doDown).mouseup(doUp);
        if ($(this).is(':checked')) {
            span.addClass('checked');
        }
        $(this).wrap(span).hide();
*/    });


    function doDown() {
        $(this).addClass('clicked');
    }

    function doUp() {
        $(this).removeClass('clicked');
    }
});	
	   
	function selectAll(){
		$("input:checkbox").not("[disabled]").prop('checked', true);
		$(".selectable").each( function() {
			$(this).not(".used").addClass("selected");
		})
	}
		
	function deselectAll(){
		$("input:checkbox").prop('checked', false);
		$(".selectable").each( function() {
			$(this).removeClass("selected");
		})
	}
	
	
	
$(function() {
    $( ".sortable" ).sortable({ 
    		axis: "y",
			containment: "parent",
			opacity: 0.9,
			distance: 5,
			tolerance: "drag",
			start: function( event, ui ) {
					$(ui.item).addClass('drag');
				},
			stop: function(event, ui) {
					$(ui.item).removeClass('drag');
					stop = true;
				},
			update : function () {
				var order = $(this).sortable('serialize');
				var id = $(this).attr('id');
				$.get('/api.php?mode=order&group='+id+'&'+order, function(data) {
				  //alert(data);
				});
			}
			
    });
    $( ".sortable" ).disableSelection();
});
		


function likePage(id){
	$('#rating-'+id).load('/api.php?mode=likePage&id='+id);		
}

function unlikePage(id){
	$('#rating-'+id).load('/api.php?mode=unlikePage&id='+id);		
}
	
		
