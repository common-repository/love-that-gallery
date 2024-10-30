jQuery(document).ready(function($) {
    $('#colorpicker').hide();
    $('#colorpicker').farbtastic('#color');

    $('#color').click(function() {
        $('#colorpicker').fadeIn();
    });

    $(document).mousedown(function() {
        $('#colorpicker').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });
	
	//font
	$('#colorpickerF').hide();
    $('#colorpickerF').farbtastic('#colorFont');

    $('#colorFont').click(function() {
        $('#colorpickerF').fadeIn();
    });
	
	$(document).mousedown(function() {
        $('#colorpickerF').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });
	
	//overlay
	$('#colorpickerO').hide();
    $('#colorpickerO').farbtastic('#colorOver');

    $('#colorOver').click(function() {
        $('#colorpickerO').fadeIn();
    });
	
	$(document).mousedown(function() {
        $('#colorpickerO').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });
	
	
	
});