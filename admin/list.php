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

///if we are duplicating
if (isset($_GET['duplicate']))
{

$wpdb->query( $wpdb->prepare("INSERT into ".$wpdb->prefix."inventive_lottery_lotteries (title, description, image, linked_product, lottery_mode, email_sender, email_email, email_message, email_subject, linked_prizes, working_days, move_unassigned_prizes, participation_mode, generate_order, prizes_template, allowed_countries, age_restriction, conditions) SELECT title, description, image, linked_product, lottery_mode, email_sender, email_email, email_message, email_subject, linked_prizes, working_days, move_unassigned_prizes, participation_mode, generate_order, prizes_template, allowed_countries, age_restriction, conditions FROM ".$wpdb->prefix."inventive_lottery_lotteries WHERE id= %s",intval($_GET['id'])) );	

}

///se stiamo cancellando
if (isset($_GET['c']))
{
$wpdb->delete( $wpdb->prefix."inventive_lottery_lotteries", array( 'ID' => intval($_GET['c']) ) );		
}

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


///selezioniamo tutti i lotteries presenti
$lotteries = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries",ARRAY_A);
?>
<div class="wrap">
<h2><?php _e("Inventive lottery","inventive_lottery"); ?></h2>

<h3><?php _e("Lotteries listing","inventive_lottery"); ?></h3>

<table class="widefat">
<thead>
<tr>
<th> <?php _e("Id","inventive_lottery"); ?> </th>
<th> <?php _e("Title","inventive_lottery"); ?> </th>
<th> <?php _e("Actions","inventive_lottery"); ?> </th>
</tr>
</thead>
<tfoot>
<tr>
<th> </th>
<th>  </th>
<th>  </th>
</tr>
</tfoot>
<tbody>

<?php
foreach($lotteries as $row)
{
	?>
	<tr>
    <td>
<?php
	echo $row['id'];
	?>
</td>
<td>
<?php
	echo $row['title'];
	?>
</td>
<td> 
<a href='?page=inventive-lottery-list-lotteries&id=<?php echo $row['id']; ?>&duplicate=1' class='button-primary'> <?php _e("Duplicate","inventive_lottery"); ?> </a>
<a href='?page=inventive-lottery-insert-lottery&id=<?php echo $row['id']; ?>' class='button-primary'> <?php _e("Edit","inventive_lottery"); ?> </a>
<a href='?page=inventive-lottery-list-lotteries&c=<?php echo $row['id']; ?>' class='button-primary'> <?php _e("Delete","inventive_lottery"); ?> </a>
</td>
</tr>

    <?php

}
?>

</tbody>
</table>

</div>