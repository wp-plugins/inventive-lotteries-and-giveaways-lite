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
global $wpdb;
include_once('classes.php');

$inventive_lottery_helpers = new inventive_lottery_helpers();

if (!$inventive_lottery_helpers->issetor($_POST['operation'])) $_POST['operation'] = "";

if ($_POST['operation'] == "ivl-search-product")
{
	$str = $_POST['stringa_ricerca'];
	
	
    $mypostids = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where post_title LIKE %s and post_status = 'publish' limit 0,10",'%'.$wpdb->esc_like($str).'%'));

    $args = array(
        'post__in'=> $mypostids,
        'post_type'=> explode(",",sanitize_text_field($_POST['where'])),
        'orderby'=>'title',
        'order'=>'asc'
    );
    $res = new WP_Query($args);

    while( $res->have_posts() ) : $res->the_post();
    echo '<span class="prodotto_'.get_the_ID().'">'.get_the_title().'</span>';
    endwhile;
	
	exit();

}

if ($_POST['operation'] == "lottery-mechanism")
{
$lottery_id = intval($_POST['id_lottery']);
///if our access si active
if ($inventive_lottery_helpers->participation_status(esc_sql($lottery_id)) === TRUE):

$result = array("no_questions"=>true);

//our participation status isn't active...
else :
$result = array("questions"=>$inventive_lottery_helpers->participation_status_message($lottery_id));
endif;//se non abbiamo ancora partecipato
echo json_encode($result);
exit();
}


if ($_POST['operation'] == "win-mechanism")
{
	///we exit if we aren't with an active participation status
   if ($inventive_lottery_helpers->participation_status(intval($_POST['id_lottery'])) !== TRUE) return false;
	
	$inventive_lottery_helpers->linked_prizes_extraction(intval($_POST['id_programmazione_lottery']));
	exit();
} //fine richiesta meccanismo vincite



////screen ivl_response PARTECIPAZIONE lottery
if ($_POST['operation'] == "screen-ivl_response")
{
	///we check response id and return correct message
	$inventive_lottery_helpers->build_response_message(intval($_POST['id_lottery']),sanitize_text_field($_POST['response_id']),$inventive_lottery_helpers->issetor(intval($_POST['product_id'])));

	exit();
	
	
}

if ($_POST['operation'] == "finish") :

///we add participation to this lottery
	$wpdb->insert( 
	$wpdb->prefix . "inventive_lottery_participants", 
	array( 
		'lottery' => intval($_POST['id_programmazione_lottery']), 
		'user_id' => get_current_user_id(),
		'date' => current_time( 'mysql' )
));

exit();

endif;
?>