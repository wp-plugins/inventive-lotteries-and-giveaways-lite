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
jQuery(document).ready(function($){
	
	$(".ivl-nav-tab").click(function()
	{
	$(".ivl-nav-tab").removeClass("nav-tab-active");
	$(this).addClass("nav-tab-active");
	$(".ivl_tabs").hide(500);
	$("."+$(this).attr("id")).show(500);
	
		
	});

$("#lottery_mode").change(function(){
if ($(this).val() == "random") $(".ivl_random_lottery_mode").show(500);
else $(".ivl_random_lottery_mode").hide(500);
});



	$('#start_date, #end_date').datetimepicker(
	{
	showMillisec: null,
	showMicrosec: null
	});
 
    var custom_uploader;
 
 
    $('#upload_image_button').click(function(e) {
 
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#upload_image').val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
	

	
$( document ).on( "click", ".contenitore-premi-scelti span", function() {
    var array_products = new Array();
	var parent = $(this).parent(".contenitore-premi-scelti");
	var ivl_container = $(this).closest(".ivl-search-container");
	$( this ).remove();
	
	parent.children("span").each(function () {

  product_id = $(this).attr("class").split("_");
  array_products.push(product_id[1]);
  
 
});

	ivl_container.find(".ivl-values").val(array_products);
	
});
	
$( document ).on( "click", ".contenitore-ricerca-premi span", function() {
	var array_products = new Array();
	
	if ($(this).closest(".ivl-search-container").find(".premi-scelti span").length >= $(this).closest(".ivl-search-container").attr("data-how-many")) return false;
	var dest = $(this).closest(".ivl-search-container").find(".contenitore-premi-scelti");
	
	$( this ).clone().appendTo(dest);
	////ciclo di tutti gli oggetti presenti all'interno del contenitore
$(this).closest(".ivl-search-container").find(".premi-scelti span").each(function () {
  product_id = $(this).attr("class").split("_");
  array_products.push(product_id[1]);
});

  $(this).closest(".ivl-search-container").find(".ivl-values").val(array_products);

});
	
$( document ).on( "keyup", ".ivl-ivl-search-product", function() {

var result = $(this).closest(".ivl-search-container").find(".contenitore-ricerca-premi");
$.post(
    ivlAjax.ajaxurl,
    {
        action : 'ivl-myajax-submit',
        operation : "ivl-search-product",
		where: $(this).attr("data-where"),
		stringa_ricerca: $(this).val()
    },
    function( response ) {
		
		result.html(response);
	   
    }
);


});

$(document).on("change","#select_participation_mode", function() {
	
$(".ivl_modes").hide(500);	

$(".ivl_mode_"+$(this).val()).show(500);	
	
});


 
 
});

