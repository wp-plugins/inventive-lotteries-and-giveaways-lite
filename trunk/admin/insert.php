<?php
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

$helpers = new inventive_lottery_helpers();

// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'scripts/ajax.js', array( '$' ) );
 
// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
?>
<?php
//http://www.itworld.com/development/366344/how-build-wordpress-plugin-administration-section

global $wpdb;

$giorni_settimana = array(__("Monday","inventive_lottery") =>"1",__("Tuesday","inventive_lottery")=>"2",__("Wednesday","inventive_lottery")=>"3",__("Thursday","inventive_lottery")=>"4",__("Friday","inventive_lottery")=>"5",__("Saturday","inventive_lottery")=>"6",__("Sunday","inventive_lottery")=>"7");

////SALVATAGGIO DATI
if (isset($_POST['title_lottery']))
{
	$_POST = array_map( 'stripslashes_deep', $_POST);
	///creiamo l'array dei giorni della settimana

	$giorni_post = '1:1,2:1,3:1,4:1,5:1,6:1,7:1,';

////participation mode
if (isset($_POST['ivl_select_participation_mode'])):

$participation_mode = json_encode(
array("selected_mode"=>sanitize_text_field($_POST['ivl_select_participation_mode']),
"registration_value"=>array(
					"data"=>
					array("times_per_day"=>sanitize_text_field($_POST['ivl_participation_mode_registration_value_1']),
						  "times_total"=>sanitize_text_field($_POST['ivl_participation_mode_registration_value_2']),
						  "access_duration"=>sanitize_text_field($_POST['ivl_participation_mode_registration_value_3']),
						  "registration_message"=>sanitize_text_field($_POST['ivl_registration_message']),
						  "instructions"=>sanitize_text_field($_POST['ivl_registration_instructions']),
						  "access_success"=>sanitize_text_field($_POST['ivl_registration_access_success']),
						  "thanks"=>sanitize_text_field($_POST['ivl_registration_thanks']),
						  "win"=>sanitize_text_field($_POST['ivl_registration_win'])
						  )
					)

));


endif;

///lottery mode
$lottery_mode = json_encode(
						  array(
						  "mode"=>sanitize_text_field($_POST['lottery_mode']),
						  "chance_to_win"=>sanitize_text_field($_POST['chance_to_win'])
						  ));
						  
	
if (isset($_POST['edit_lottery']))
{

	$wpdb->update( 
	$wpdb->prefix . "inventive_lottery_lotteries", 
	array( 
		'title' => sanitize_text_field($_POST['title_lottery']), 
		'description' => sanitize_text_field($_POST['description']),
		'image' => sanitize_text_field($_POST['image']),
		//'linked_product' => $_POST['linked_product'],
		'lottery_mode' => $lottery_mode,
		'email_message' => sanitize_text_field($_POST['email_message']),
		'email_subject' => sanitize_text_field($_POST['email_subject']),
		'email_sender' => sanitize_text_field($_POST['email_sender']),
		'email_email' => sanitize_text_field($_POST['email_email']),
		'linked_prizes' => sanitize_text_field($_POST['array_linked_prizes']),
		'working_days' => $giorni_post,
		'participation_mode' => $participation_mode,
		'show_date' => sanitize_text_field($_POST['show_date'])
		
	),
	array( 'ID' => intval($_POST['edit_lottery']) )
);


}
else
{

$wpdb->insert( 
	$wpdb->prefix . "inventive_lottery_lotteries", 
	array( 
		'title' => sanitize_text_field($_POST['title_lottery']), 
		'description' => sanitize_text_field($_POST['description']),
		'image' => sanitize_text_field($_POST['image']),
		'lottery_mode' => $lottery_mode,
		'email_message' => sanitize_text_field($_POST['email_message']),
		'email_subject' => sanitize_text_field($_POST['email_subject']),
		'email_sender' => sanitize_text_field($_POST['email_sender']),
		'email_email' => sanitize_text_field($_POST['email_email']),
		'linked_prizes' => sanitize_text_field($_POST['array_linked_prizes']),
		'working_days' => $giorni_post,
		'participation_mode' => $participation_mode,
		'show_date' => sanitize_text_field($_POST['show_date'])
));
}

}


///se stiamo edit_lotteryndo un lottery
if (isset($_GET['id']))
{
$lottery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries WHERE id = %s",intval($_GET['id'])));
}
else
{
$lottery = (object) array(
		'title' => '', 
		'description' => '',
		'image' => '',
		'linked_product' => '',
		'lottery_mode' => '',
		'email_message' => '',
		'email_subject' => '',
		'linked_prizes' => '',
		'working_days' => '',
		'move_unassigned_prizes' => '',
		'min_subscribers' => ''
);	
}
?>
<div class="wrap">
<h2><?php _e("Inventive lottery","inventive_lottery"); ?></h2>

<h3><?php _e("insert lottery","inventive_lottery"); ?></h3>

<h2 class="nav-tab-wrapper">
    <a href="#" class="ivl-nav-tab nav-tab nav-tab-active" id="ivl_tabs_main_infos"><?php _e("Main infos","inventive_lottery"); ?></a>
    <a href="#" class="ivl-nav-tab nav-tab" id="ivl_tabs_main_config"><?php _e("Main config","inventive_lottery"); ?></a>
    <a href="#" class="ivl-nav-tab nav-tab" id="ivl_tabs_linked_prizes"><?php _e("Linked prizes","inventive_lottery"); ?></a>
    <a href="#" class="ivl-nav-tab nav-tab" id="ivl_tabs_participation_mode"><?php _e("Participation mode","inventive_lottery"); ?></a>
    <a href="#" class="ivl-nav-tab nav-tab" id="ivl_tabs_email_settings"><?php _e("Email settings","inventive_lottery"); ?></a>
</h2>

<form method="post" action="admin.php?page=inventive-lottery-list-lotteries">

<div class="ivl_tabs ivl_tabs_main_infos">

<table class="form-table">

<tr valign="top">

<th scope="row"><?php _e("Title","inventive_lottery"); ?></th>

<td><input type="text" name="title_lottery" value="<?php echo $lottery->title; ?>" class="instantWin_input_100"/></td>

</tr>

<tr valign="top">
<th scope="row"><?php _e("image","inventive_lottery"); ?></th>
<td>
<label for="upload_image">
    <input id="upload_image" type="text" size="36" name="image" value="<?php echo $lottery->image; ?>" /> 
    <input id="upload_image_button" class="button" type="button" value="<?php _e("insert image","inventive_lottery"); ?>" />
    <br /><?php _e("insert an url or load an image","inventive_lottery"); ?>
</label>
</td>
</tr>

<tr valign="top">

<th scope="row"><?php _e("description","inventive_lottery"); ?></th>

<td>
<?php
$content = $lottery->description;
$editor_id = 'description';

wp_editor( $content, $editor_id );
?>
</td>

</tr>
</table>
</div>

<div class="ivl_tabs ivl_tabs_linked_prizes">
<table class="form-table">

<tr valign="top">

<th scope="row"><?php _e("Linked prizes","inventive_lottery"); ?></th>

<td>
<?php
$helpers->ajax_product_search( _e("Search page to assign as prize","inventive_lottery"),100000,'array_linked_prizes','array_linked_prizes',$lottery->linked_prizes,array("page"));
?>

</td>

</tr>

</table>
</div>

<div class="ivl_tabs ivl_tabs_main_config">

<table class="form-table">
<tr valign="top">

<th scope="row"><?php _e("Show lottery start/end date?","inventive_lottery"); ?></th>
<td>
<?php
if ($helpers->issetor($lottery->show_date) == 1) {$checked1 = "CHECKED"; $checked2 = ""; }
else {$checked1 = ""; $checked2 = "CHECKED"; }
?>
<?php _e("Yes","inventive_lottery"); ?> <input type="radio" name="show_date"  value="1"  <?php echo $checked1; ?>/>
<?php _e("No","inventive_lottery"); ?> <input type="radio" name="show_date"  value="0"  <?php echo $checked2; ?>/>
<em><?php _e("","inventive_lottery"); ?></em>
</td>
</tr>

<tr valign="top">

<th scope="row"><?php _e("Lottery mode","inventive_lottery"); ?></th>

<td>
<?php
$lottery_mode = json_decode($lottery->lottery_mode);
?>
<select name="lottery_mode" id="lottery_mode">
<?php
/*
Modalità 1: all'avvio del lottery viene decisa un'ora automaticamente, il primo che inserirà il codice a quell'ora vince
Modalità 2: all'avvio del lottery viene selezionato un numero random, se il partecipante nell'ordine di iscrizione ha quel numero vince
*/


$modalita = array("random" => __("Random","inventive_lottery"));
foreach ($modalita as $key=>$moda)
{
if ($lottery_mode->mode == $key) $sel = "SELECTED";
else $sel = ""; 
?>
<option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $moda; ?></option>
<?php
}
?>
</select>
<?php
$display = 'style="display:block"';
?>
<div class="ivl_random_lottery_mode" <?php echo $display; ?>>
<?php echo '<p>'.__("Chance to win","inventive_lottery").' '.'<input type="text" name="chance_to_win" id="chance_to_win" value="'.$helpers->issetor($lottery_mode->chance_to_win).'"> '.__("over 100","inventive_lottery").'</p>';
?>
</div>


</td>

</tr>
</table>
</div>

<div class=" ivl_tabs ivl_tabs_participation_mode">
<table class="form-table">

<tr valign="top">

<th scope="row"><?php _e("Participation mode","inventive_lottery"); ?></th>
<td>
<?php
$participation_mode = json_decode($helpers->issetor($lottery->participation_mode));
?>
<select name="ivl_select_participation_mode" id="select_participation_mode">
<?php


$participation_modes = array("Website registration"=>"registration");
foreach ($participation_modes as $modes => $values):
if ($participation_mode->selected_mode == $values) $selected = "SELECTED";
else $selected = "";
echo '<option value="'.$values.'" '.$selected.' class="participation_modes">'.$modes.'</option>';
endforeach;
?>
</select>
<?php
$show_pass = "";
$show_registration = "";
$show_order = "";
$show_question = "";
$show_code = "";

$show_registration = 'style="display:block"';
?>
<div class="ivl_mode_registration ivl_modes" <?php echo $show_registration; ?>>
<?php
$times_per_day = $helpers->issetor($participation_mode->registration_value->data->times_per_day);
$times_total = $helpers->issetor($participation_mode->registration_value->data->times_total);
$access_duration = $helpers->issetor($participation_mode->registration_value->data->access_duration);
?>
<p> <?php _e('How many times per day new registered user can participate to this lottery?', 'inventive_lottery'); ?> : <input type="text" name="ivl_participation_mode_registration_value_1" value="<?php echo $times_per_day; ?>" / > </p>
<p> <?php _e('How many times in total new registered user can participate to this lottery?', 'inventive_lottery'); ?> : <input type="text" name="ivl_participation_mode_registration_value_2" value="<?php echo $times_total; ?>" / > </p>
<p> <?php _e('Validity duration since user registration(in days)', 'inventive_lottery'); ?> : <input type="text" name="ivl_participation_mode_registration_value_3" value="<?php echo $access_duration; ?>" / > </p>
<p>
<label for="ivl_registration_message"><?php _e("Message to show in registration form","inventive_lottery"); ?></label>
<input class="widefat" type="text" name="ivl_registration_message"  id="ivl_registration_message" value="<?php echo $helpers->issetor($participation_mode->registration_value->data->registration_message); ?>" />
</p>
<p>
<label for="ivl_registration_instructions"><?php _e("Insert instructions phrase","inventive_lottery"); ?></label>
<input class="widefat" type="text" name="ivl_registration_instructions"  id="ivl_registration_instructions" value="<?php echo $helpers->issetor($participation_mode->registration_value->data->instructions); ?>" />
</p>
<p>
<label for="ivl_registration_access_success"><?php _e("Insert access success phrase","inventive_lottery"); ?></label>
<input class="widefat" type="text" name="ivl_registration_access_success"  id="ivl_registration_access_succes" value="<?php  echo $helpers->issetor($participation_mode->registration_value->data->access_success);   ?>" />
</p>
<p>
<label for="ivl_registration_thanks"><?php _e("Insert thanks but you have not won message","inventive_lottery"); ?></label>
<input class="widefat" type="text" name="ivl_registration_thanks"  id="ivl_registration_thanks" value="<?php echo $helpers->issetor($participation_mode->registration_value->data->thanks); ?>" />
</p>
<p>
<label for="ivl_registration_win"><?php _e("Insert win message","inventive_lottery"); ?></label>
<input class="widefat" type="text" name="ivl_registration_win"  id="ivl_registration_win" value="<?php echo $helpers->issetor($participation_mode->registration_value->data->win); ?>" />
</p>
</div>

</td>

</tr>

</table>
</div>

<div class=" ivl_tabs ivl_tabs_email_settings">
<table class="form-table">
<tr valign="top">

<tr>
<th scope="row"><?php _e("Sender name","inventive_lottery"); ?></th>
<td><input type="text" name="email_sender" value="<?php echo $helpers->issetor($lottery->email_sender); ?>" class="instantWin_input_100"/></td>
</tr>
<tr>
<th scope="row"><?php _e("Sender email","inventive_lottery"); ?></th>
<td><input type="text" name="email_email" value="<?php echo $helpers->issetor($lottery->email_email); ?>" class="instantWin_input_100"/></td>
</tr>
<tr>
<th scope="row"><?php _e("Email subject","inventive_lottery"); ?></th>
<td><input type="text" name="email_subject" value="<?php echo $helpers->issetor($lottery->email_subject); ?>" class="instantWin_input_100"/></td>
</tr>
<tr>
<th scope="row"><?php _e("Email message to the winner","inventive_lottery"); ?></th>
<td>
<?php _e("","inventive_lottery"); ?>
<?php
$content = $lottery->email_message;
$editor_id = 'email_message';

wp_editor( $content, $editor_id );
?>
</td>
</tr>
<tr>

</table>
</div>
<?php if (isset($_GET['id'])) :?> 
<input type="hidden" name="edit_lottery" id="edit_lottery" value="<?php echo $_GET['id']; ?>" />
<?php endif; ?>
<?php submit_button(); ?>

</form>

</div>