/**
 * Created by mrecho on 5/16/14.
 */


$( document ).ready(function() {

    $( document ).tooltip();

    $( "#button_vote_tig" ).bind( "click", function() {
        button_vote_ti("/view/voteTIG");
    });

    $( "#button_vote_tib" ).bind( "click", function() {
        button_vote_ti("/view/voteTIB");
    });
});

function button_vote_ti(url){


    var session = $("#vote_data").data("session");
    var id = $("#vote_data").data("id");
    var hasvoted = $("#vote_data").data("hasvoted");

    if(hasvoted == 0){//fyi there is a check in php
        $.post( url, { id: id, session: session})
            .done(function( data ) {
                var json = jQuery.parseJSON(data);
                $("#vote_tig").text(json.tig);
                $("#vote_tib").text(json.tib);
                $("#vote_text").text(json.text);
                $("#vote_data").data("hasvoted", 1);

                $("#button_vote_tig").css("color", "gray");
                $("#button_vote_tib").css("color", "gray");

                m('Thanks for the vote!');
            }), {dataType: 'json'};
    } else {
        m("Already voted :P");
        $("#button_vote_tig").css("color", "gray");
        $("#button_vote_tib").css("color", "gray");
    }

}

function m(text){
    $("#modal div").empty();
    $("#modal div").append(text);
    $("#modal div").css("visibility", "visible");
    $('#modal').fadeIn(100)
    setTimeout("$('#modal').fadeOut(1000)", 1000);
}