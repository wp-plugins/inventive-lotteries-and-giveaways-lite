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
function inventive_lottery_metaboxes()
{
	
$where = array("product","page","post");

foreach ($where as $w):	
add_meta_box( '1nventive-lottery-metaboxes', __( 'Lottery'), 'inventive_lottery_options', $w, 'side', 'high' );	
endforeach;

}

function inventive_lottery_options($post) {
	global $wpdb;

	$lottery_id = get_post_meta( $post->ID, 'inventive_lottery_id', true );

	$lotteries = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries order by title ASC", ARRAY_A);
	
	echo '<p><select name="inventive_lottery_id" id="inventive_lottery_id">';
	
	echo '<option value="0">'.__("None","inventive_lottery").'</option>';
	
	foreach ($lotteries as $lottery) :
	
	if (esc_attr( $lottery_id ) == $lottery['id']) $selected = "SELECTED";
	else $selected = "";
	
	echo '<option value="'.$lottery['id'].'" '.$selected.'>'.$lottery['title'].'</option>';
	
	endforeach;
	
	echo '</select></p>';

}

add_action( 'add_meta_boxes', 'inventive_lottery_metaboxes' );

add_action( 'save_post', 'inventive_lottery_save_meta', 10, 2 );


function inventive_lottery_save_meta($post_id, $post)
{
	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );
	
/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;
		
	///verifichiamo se lo spectrum analyzer è attivato


	
///se lo spectrum analyzer è attivato salviamo il file mp3 se non ce l'abbiamo ancora
if (isset($_POST['inventive_lottery_id'])):


	update_post_meta( $post_id, 'inventive_lottery_id', intval($_POST['inventive_lottery_id']) );


endif;
}