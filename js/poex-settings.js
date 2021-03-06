function poexSettingsInit() {
    var answerHTML = jQuery(".poex_answers").html();
    jQuery(".poex_answers").html("");

    jQuery.each( poexAnswers, function() {
        jQuery('.poex_answers').append( answerHTML );
    });

    jQuery(".poex_answers").on( "click", ".poex_answer_remove", function() {
        if( poexAnswers.length > 1 ) {
            get_poex_answers();
            var key = jQuery(this).data("key");
            jQuery( "#poex_answer_" + key).detach();
            poexAnswers.splice(key, 1);
            set_poex_answers();
        } else {
            alert("At least one answer is required.");
        }
    });

    jQuery(".poex_settings_form").on("click", ".poex_answer_add", function() {
        get_poex_answers();
        poexAnswers.push( "New answer" );
        jQuery('.poex_answers').append( answerHTML );
        set_poex_answers();
    });

    set_poex_answers();
}

function get_poex_answers() {
    poexAnswers = [];
    jQuery('.answer_input').each( function() {
        poexAnswers.push( jQuery(this).val() );
    });
}

function set_poex_answers() {
    console.log( poexAnswers );
    jQuery.each( jQuery(".poex_answer"), function( i, val ) {
        var id = "poex_answer_" + i;
        jQuery(this).attr( "id", id );
        jQuery("#" + id + " .answer_input").val( poexAnswers[i] );
    });

    jQuery.each( jQuery(".poex_answer_remove"), function( i, val ) {
        jQuery(this).data( "key", i );
    });
}