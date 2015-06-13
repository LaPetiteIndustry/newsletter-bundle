jQuery( document ).ready(function( $) {

    $('form[name="lpi_newsletter_registration"]').submit(function(event){

        var action = $( this).attr('action');
        var data = $( this ).serializeArray();

        $.ajax({
            type: "POST",
            url: action,
            data: data,
            success: success,
            error: error,
            dataType : 'text'
        });

        event.preventDefault();
    });


    function success(data, status){
        $('form[name="lpi_newsletter_registration"]').trigger("reset");

        if( $('#message').hasClass('error'))
            $('#message').removeClass('error');

        $('#message').html(data);
    }

    function error(jqXHR, textStatus, errorThrown){
        $('#message').addClass('error');
        $('#message').html(jqXHR.responseText);
    }
});
