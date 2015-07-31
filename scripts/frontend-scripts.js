/*
Inventive lotteries and giveaways

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA

*/
"use strict";
var ivl_response_reply;
var inventive_lottery_answer = 0;
	
	function ivl_response_vincita(ivl_response)
	{
	var response = jQuery.parseJSON(ivl_response);

	jQuery.post(
    // see tip #1 for how we declare global javascript variables
    ivlAjax.ajaxurl,
    {
        // here we declare the parameters to send along with the request
        // this means the following action hooks will be fired:
        // wp_ajax_nopriv_ivl-myajax-submit and wp_ajax_ivl-myajax-submit
        action : 'ivl-myajax-submit',
 
        operation : "screen-ivl_response",
		product_id : response.product_id,
		response_id: response.response,
		id_lottery: jQuery("#ivl_calendar_id").val()
    },
    function( response ) {
	jQuery('#overlay-inventive_lottery-contenitore').show();
////DIAMO IL RISULTATO DELLA PARTECIPAZIONE
jQuery("#overlay-inventive_lottery-contenitore").html(response);

	   
    }
);  	
		
	}
	
function ivl_extraction_logic(code)
{
	
	 jQuery.post(ivlAjax.ajaxurl,
    {
        action : 'ivl-myajax-submit',
        operation : "win-mechanism",
		code: code,
		id_programmazione_lottery: jQuery("#ivl_calendar_id").val(),
		id_lottery: jQuery("#ivl_lottery_id").val(),
    },
    function( response ) {
		 
ivl_response_vincita(jQuery.trim(response));
	   
	});
}

jQuery(document).ready(function($){
	
if ($("#ivl_lottery_end_date").val()){
var s = $("#ivl_lottery_end_date").val(); //la scadenza del lottery attuale
var bits = s.split(/\D/);
var date = new Date(bits[0], --bits[1], bits[2], bits[3], bits[4]);
$('#ivl-count').countdown({until: date, description: ''});//avviamo il countdown
$('#ivl-count').countdown($.countdown.regionalOptions['it']); 
}
	
$(".overlay-inventive-lottery").on("click", "#chiudi-inventive_lottery", function(event) {
	jQuery(".overlay-inventive-lottery").fadeOut();
});

///AZIONI CLICK SULL'INPUT DELLA answer
$(".overlay-inventive-lottery").on("click", "#inventive_lottery_answer", function(event) {
	
	$(this).val("").css({"color":"#555555","font-style":"normal"});
	
});
	
	

$(".ivl-participate-now").click(function(){
	
	$(".overlay-inventive-lottery").css({"display":"block"});
	$("#overlay-inventive_lottery-contenitore").html($(".ivl-participate-now-step-2").html());
});

jQuery("#overlay-inventive_lottery-contenitore").on("click", ".partecipazione-lottery",function(){
	

	$("#inventive_lottery-caricamento").html(ivl_loading).show();
	jQuery("#overlay-inventive_lottery-contenitore").hide();
jQuery.post(
    ivlAjax.ajaxurl,
    {
        action : 'ivl-myajax-submit',
        operation : "lottery-mechanism",
		id_calendar: $("#ivl_calendar_id").val(),
		id_lottery: $("#ivl_lottery_id").val()
    },
    function( response ) {
       $('#inventive_lottery-caricamento').hide(); 
	   var questions = $.parseJSON(response);
	   
	   if (questions.no_questions === true){
		  ivl_extraction_logic(); 
	   }
	   else
	   {
	   ivl_response_domande('<div class="contenitore-question">'+questions.questions+'</div>');
	   }
	   
    }
);

});
	
	
});
