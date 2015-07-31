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
if ( !class_exists( 'inventive_lottery_helpers' ) ) :

class inventive_lottery_helpers {
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/***********************************		GET ACTIVE LOTTERIES      ********************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/		
public function get_active_lotteries($id)
{
	global $wpdb;
	$now = current_time( 'mysql' );
	
	if ($id):
	$active_lotteries = $wpdb->get_results($wpdb->prepare("SELECT *, calendar_lottery.lottery as id_lottery, calendar_lottery.id as id_calendar FROM ".$wpdb->prefix."inventive_lottery_calendar as calendar_lottery
	left join ".$wpdb->prefix."inventive_lottery_lotteries as lottery on (lottery.id = calendar_lottery.lottery)
	WHERE calendar_lottery.start_date <= %s and lottery.working_days like %s and lottery.id = %s and calendar_lottery.end_date >= %s", $now, '%'.date('N').':1,%', intval($id), $now), OBJECT);
	else:
	$active_lotteries = $wpdb->get_results($wpdb->prepare("SELECT *, calendar_lottery.lottery as id_lottery, calendar_lottery.id as id_calendar FROM ".$wpdb->prefix."inventive_lottery_calendar as calendar_lottery
	left join ".$wpdb->prefix."inventive_lottery_lotteries as lottery on (lottery.id = calendar_lottery.lottery)
	WHERE calendar_lottery.start_date <= %s and lottery.working_days like %s and calendar_lottery.end_date >= %s order by rand()", $now, '%'.date('N').':1,%', $now), OBJECT);
	endif;
	
	return $active_lotteries;
	
	
}

public function active_lottery($lottery_id)
{
global $wpdb;
$active_lottery = $wpdb->get_row($wpdb->prepare("SELECT participation_mode FROM ".$wpdb->prefix."inventive_lottery_lotteries where id = %s", intval($lottery_id)));	
return $active_lottery;
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/**************************************        ISSETOR       *****************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/	
public function issetor(&$var, $default = false) {
    return isset($var) ? $var : $default;
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/********************************** GET LOTTERY PARTICIPATION MODE  **********************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/	
public function get_lottery_participation_mode($lottery_id)
{
global $wpdb;

$active_lottery = $this->active_lottery($lottery_id);	
$participation_mode = json_decode($active_lottery->participation_mode);
return $participation_mode->selected_mode;
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function get_lottery_participation_mode_values($mode,$lottery_id)
{
global $wpdb;
$active_lottery = $this->active_lottery($lottery_id);		
$participation_mode = json_decode($active_lottery->participation_mode);

$mode = $this->get_lottery_participation_mode($lottery_id)."_value";

return $participation_mode->$mode->data;

}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function get_lottery_participation_instructions($lottery_id)
{
global $wpdb;
$mode = $this->get_lottery_participation_mode($lottery_id);
$active_lottery = $this->active_lottery($lottery_id);
$participation_data = json_decode($active_lottery->participation_mode);

///if the reason we can't parti
$mode = $mode."_value";

return $participation_data->$mode->data->instructions;

}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function get_lottery_participation_access_success($lottery_id)
{

global $wpdb;
$message = "";
$mode = $this->get_lottery_participation_mode($lottery_id);
$active_lottery = $this->active_lottery($lottery_id);		
$participation_data = json_decode($active_lottery->participation_mode);

$mode = $mode."_value";
$message = $participation_data->$mode->data->access_success;	

if ( $message == "" ) $message = __("Cool! Your access is granted! Click to participate!", "inventive_lottery");

return $message;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function get_lottery_participation_thanks($lottery_id)
{

global $wpdb;
$message = "";
$mode = $this->get_lottery_participation_mode($lottery_id);
$active_lottery = $this->active_lottery($lottery_id);	
$participation_data = json_decode($active_lottery->participation_mode);

$mode = $mode."_value";
$message = $participation_data->$mode->data->thanks;	

if ( $message == "" ) $message = __("Thanks for participating but you have not won. Try next time!", "inventive_lottery");

return $message;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function get_lottery_participation_win($lottery_id, $prize_id)
{

global $wpdb;
$message = "";
$mode = $this->get_lottery_participation_mode($lottery_id);
$active_lottery = $this->active_lottery($lottery_id);
$participation_data = json_decode($active_lottery->participation_mode);

$mode = $mode."_value";
$message = $participation_data->$mode->data->win;	

if ( $message == "" ) $message = __("You have won! We sent you a message for confirming your prizes!", "inventive_lottery");

;

$message .= '<div class="ivl_win_details">'.$this->get_prize_details($prize_id).'</div>';

return $message;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function get_prize_details($prize_id)
{

$prize = get_post($prize_id);
if ($prize->post_type != "shop_coupon"):

$result = sprintf(__('You won a %1$s!','inventive_lottery'), get_the_title($prize_id));

else:

$result = __('You won a discount coupon!','inventive_lottery');

endif;

return $result;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function participation_status($lottery_id)
{	
//first we check the participation mode
$mode = "mode_".$this->get_lottery_participation_mode($lottery_id);
return $this->$mode($this->get_lottery_participation_mode_values($mode,$lottery_id),$lottery_id,"");
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function participation_status_message($lottery_id)
{	
//first we check the participation mode
$mode = "mode_".$this->get_lottery_participation_mode($lottery_id);
return $this->$mode($this->get_lottery_participation_mode_values($mode,$lottery_id),$lottery_id,"message");
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/***************************************        MODE REGISTRATION          ***************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function mode_registration($values, $lottery_id, $message)
{
	$current_user = wp_get_current_user();
	if (!$this->issetor($current_user)->user_registered) return $this->get_lottery_participation_instructions($lottery_id);
	
	///date when pass will expire
	$end_date = strtotime($current_user->user_registered." +".$values->access_duration." days");
	//now
	$today = strtotime(current_time( 'mysql' ));
    
	$total_uses = $this->total_pass_uses_logic($lottery_id, $values, 1);
	//if we are on message mode, in this case we show up message which tell us that we can't play more
	if ($message && $total_uses === false) return __("You have reached maximum participation times for this lottery","inventive_lottery");
	
	$times_today = $this->total_pass_uses_today_logic($lottery_id, $values, 1);
	//if we are on message mode, in this case we show up message we have we can't play more for today
	if ($message && $times_today === false) return __("You have reached maximum participation times today","inventive_lottery");
	
	//if we are on message mode
	if ($message) return $this->get_lottery_participation_instructions($lottery_id);
		
	if (($today < $end_date) && ($total_uses === true) && ($times_today === true)):
	return TRUE;
	endif;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/******************************************    TOTAL PASS USES LOGIC    ******************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function total_pass_uses_logic($lottery_id, $values, $active_passes)
{

	$pass_uses = $this->user_pass_uses($lottery_id);
	$times_total = $values->times_total*$active_passes;
	if ($pass_uses->total < $times_total) :
	$total_uses = true;
	else :
	$total_uses = false;
	endif;	
	
	return $total_uses;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function total_pass_uses_today_logic($lottery_id, $values, $active_passes)
{

	$times_today = $this->user_pass_uses_today($lottery_id);
	$times_per_day = $values->times_per_day*$active_passes;

	if ($times_today->total < $times_per_day):
	$times_today = true;
	else:
	$times_today = false;
	endif;
	
	
	return $times_today;
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function user_pass_uses($lottery_id)
{
global $wpdb;
$pass_uses = $wpdb->get_row($wpdb->prepare("SELECT count(calendar.id) as total FROM ".$wpdb->prefix."inventive_lottery_participants as participants
		left join ".$wpdb->prefix."inventive_lottery_calendar as calendar on (participants.lottery = calendar.id) where participants.user_id = %s and calendar.lottery = %s",get_current_user_id(),$lottery_id));	
		
		return $pass_uses;
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function user_pass_uses_today($lottery_id)
{
global $wpdb;

$start_today = date("Y")."-".date("m")."-".date("d")." 00:00:00";
$end_today = date("Y")."-".date("m")."-".date("d")." 23:59:59";

$pass_uses_today = $wpdb->get_row($wpdb->prepare("SELECT count(calendar.id) as total FROM ".$wpdb->prefix."inventive_lottery_participants as participants
		left join ".$wpdb->prefix."inventive_lottery_calendar as calendar on (participants.lottery = calendar.id) where participants.user_id = %s and participants.date > %s and participants.date < %s and calendar.lottery = %s",get_current_user_id(),$start_today,$end_today,$lottery_id));	
			
 return $pass_uses_today;
 	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function ajax_product_search($ivl_title,$how_many,$array_name,$array_id,$array_linked_products,$where=array())
{
global $wpdb;
?>
<div class="ivl-search-container" data-array-name="<?php echo $array_name; ?>" data-how-many = "<?php echo $how_many; ?>">
<label for="ivl-ivl-search-product"><?php echo $ivl_title; ?></label>
<input type="text" class="ivl-ivl-search-product widefat" data-where="<?php echo implode(" ",$where); ?>"  name="ivl-search-product" id="ivl-search-product" value="" autocomplete="off"/>
<div class="contenitore-ricerca-premi">
</div>
<div class="premi-scelti">
<div class="contenitore-premi-scelti">
<?php

if (strpos($array_linked_products, ",")):

$lista_premi = explode( ',', $array_linked_products );

else :

if ($array_linked_products) :
$lista_premi = array($array_linked_products);
else :
$lista_premi = "";
endif;

endif;

if (is_array($lista_premi) && count($lista_premi) >= 1) :

foreach ($lista_premi as $premi) :
 if (in_array("questions",$where)):
 $prodotto = $wpdb->get_row($wpdb->prepare("select id,question from ".$wpdb->prefix."inventive_lottery_questions where id = %s",$premi));
 echo '<span class="prodotto_'.$prodotto->id.'">'.$prodotto->question.'</span>';
 else :
 $prodotto = get_post( $premi );
 echo '<span class="prodotto_'.$prodotto->ID.'">'.get_the_title($prodotto->ID).'</span>';
 endif;
 
 ?>

<?php
endforeach;
endif;

echo '
</div>
</div>';

echo '<input class="ivl-values" type="hidden" name="'.$array_name.'" id="'.$array_id.'" value="'.$array_linked_products.'"/>';

echo '</div>';
	
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function send_email($lottery,$coupon_code,$prize_id)
{

	
$user_info = get_user_by('id',get_current_user_id());

$multiple_to_recipients = array(
    $user_info->user_email, //we sender to user
    $lottery->email_email //we send also to administrator
);

$messaggio = '<p>'.$this->get_lottery_participation_win($lottery->id,$prize_id).'</p>';

$messaggio .= '<span>'.$lottery->email_message.'</span>';
if ($coupon_code) :
$messaggio = str_replace("{COUPON}",'<p>'.__("This is the coupon code:","inventive_lottery").' <strong>'.$coupon_code.'</strong></p>',$messaggio); 
else :
$messaggio = str_replace("{COUPON}",'',$messaggio); 
endif;



add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

$headers = 'From: "'.$lottery->email_sender.'" <'.$lottery->email_email.'>';

wp_mail( $multiple_to_recipients, $lottery->email_subject, nl2br($messaggio) , $headers);

// Reset content-type to avoid conflicts 
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
	
}

public function build_response_message($lottery_id, $response, $prize_id)
{
global $wpdb;

$lottery = $wpdb->get_row($wpdb->prepare("SELECT *
FROM ".$wpdb->prefix."inventive_lottery_calendar as programmazione
left join ".$wpdb->prefix."inventive_lottery_lotteries as lottery on (lottery.id = programmazione.lottery) WHERE programmazione.id = %s",$lottery_id));

echo '<div class="ivl_response_container_show">';
///if we won
if ($response == 1):
///we send thanks but you win message
echo $this->get_lottery_participation_win($lottery->id,$prize_id);

$coupon_code = "";

///we send email to user
$this->send_email($lottery, $coupon_code, $prize_id);

elseif ($response == 2) :

echo __("We are sorry but we assigned all prizes!","inventive_lottery");
	
else :

///we send thanks but you have not win message
echo $this->get_lottery_participation_thanks($lottery->id);
exit();
	
endif;	
echo '</div>';
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/**********************************		LINKED PRIZES EXTRACTION	 	*****************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
public function linked_prizes_extraction($id_lottery_calendar)
{
	global $wpdb;
	$inventive_lottery_extraction_method = new inventive_lottery_extraction_method();
	$stop_cycle = "";
	$already_participated = "";
	$product_id = "";
	
////selezioniamo tutti i premi collegati al lottery che non sono ancora stati assegnati
	$active_lottery = $wpdb->get_results($wpdb->prepare("SELECT *, lottery_calendar.id as id_riga_premio_assegnato FROM ".$wpdb->prefix."inventive_lottery_linked_prizes as lottery_calendar
	left join ".$wpdb->prefix."inventive_lottery_lotteries as lottery on (lottery.id = lottery_calendar.lottery)
	WHERE lottery_calendar.lottery_calendar = %s and lottery_calendar.winner = 0 order by rand()",$id_lottery_calendar), ARRAY_A);
	
	
	////adesso iniziamo un ciclo dei premi non assegnati, se l'utente ne vince almeno 1 interrompiamo il ciclo
	///i dati dell'lottery
	foreach ($active_lottery as $ivl_active_prize) :
	$json_response = ""; //reset json response
	//echo "premio attivo:".$ivl_active_prize['id_riga_premio_assegnato']." - ";
	////se abbiamo già assegnato un premio interrompiamo il loop
	if ($stop_cycle == 1) break;
	
	$dati_lottery = json_decode($ivl_active_prize['extraction']);


if ($this->participation_status(esc_sql($ivl_active_prize['lottery'])) === TRUE) :

/////if lottery is in time extraction mode
	$extraction_mode = $dati_lottery->mode."_extraction";
	$response = $inventive_lottery_extraction_method->$extraction_mode($dati_lottery, $ivl_active_prize);

	//we want to check only 1 time
	if ($response['stop'] == "stop"):
	$stop_cycle = 1;
	endif;
	
	//we won
	if ($response['response'] == 1) :
	//if we won something stop the loop
	$stop_cycle = 1;	
	endif;

endif;

endforeach; /// fine foreach dei premi assegnabili

///we add participation to this lottery
	$wpdb->insert( 
	$wpdb->prefix . "inventive_lottery_participants", 
	array( 
		'lottery' => $id_lottery_calendar, 
		'user_id' => get_current_user_id(),
		'date' => current_time( 'mysql' )
));
	if (!$this->issetor($response)) $json_response = json_encode(array("response"=>"2"));
	else $json_response = json_encode(array("response"=>$response['response'],"product_id"=>$response['product_id']));
	echo $json_response;
	
	

	
}

public function clean($string)
{
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	
}




}//end class



endif; //end if class doesnt exists