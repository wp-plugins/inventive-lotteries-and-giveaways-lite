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

///se stiamo modificando un lottery
if (isset($_GET['id']))
{
$lottery_calendar = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_calendar WHERE id = %s",intval($_GET['id'])));
}
else
{
$lottery_calendar = (object) array(
		'lottery' => '', 
		'winner' => '',
		'start_date' => date("m-d-y"),
		'end_date' => '',
		'extraction' => ''
);	

}
?>

<div class="wrap">
<h2><?php _e("Inventive lottery","inventive_lottery"); ?></h2>

<h3><?php _e("Insert lottery in calendar","inventive_lottery"); ?></h3>

<form method="post" action="admin.php?page=inventive-lottery-calendar">


<table class="form-table">

<tr valign="top">

<th scope="row"><?php _e("Lottery","inventive_lottery"); ?></th>

<td>

<select name="id_lottery">
<?php
/*
*/
$lotteries = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries",ARRAY_A);
foreach($lotteries as $row)
{
if ($row['id'] == $lottery_calendar->lottery) $sel = "SELECTED";
else $sel = ""; 
?>
<option value="<?php echo $row['id']; ?>" <?php echo $sel; ?>><?php echo $row['title']; ?></option>
<?php
}
?>
</select>


</td>

</tr>
<tr>
<th scope="row"><?php _e("Start date","inventive_lottery"); ?></th>

<td>
<?

?>
<input type="text" name="start_date" id="start_date" value="<?php echo $lottery_calendar->start_date; ?>" />
</td>

</tr>

<tr>
<th scope="row"><?php _e("End date","inventive_lottery"); ?></th>

<td>
<?

?>
<input type="text" name="end_date" id="end_date" value="<?php echo $lottery_calendar->end_date; ?>" />
</td>

</tr>

</table>
<?php if (isset($_GET['id'])) : ?>
<input type="hidden" name="modifica" value="<?php echo $_GET['id']; ?>" />
<?php endif; ?>
<?php submit_button(); ?>



<div class="ivl_winners">
<table class="widefat">
<thead>
<tr>
<th> <?php _e("Winner","inventive_lottery"); ?></th>
<th> <?php _e("Prize","inventive_lottery"); ?> </th>
<th> <?php _e("Date","inventive_lottery"); ?> </th>
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
if (isset($_GET['id'])):
///tiriamo fuori tutti i vincitori di questa programmazione lottery
$select_winners = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_linked_prizes as winners
left join ".$wpdb->prefix."posts as prize on (prize.ID = winners.prizes_id and prize.post_status = 'publish')
	WHERE winners.winner != 0 and lottery_calendar = %s",intval($_GET['id'])));

	foreach ($select_winners as $vincitori):
	
	$user_info = get_user_by('id',$vincitori->winner);
	echo '<tr>';
	echo '<td><a href="user-edit.php?user_id='.$vincitori->winner.'">'.$user_info->user_login.'</a></td>';
	echo '<td>'.$vincitori->post_title.'</td>';
	echo '<td>'.$vincitori->date.'</td>';
	echo '</tr>';
	///in futuro mettere anche quale premio ha vinto
	endforeach;
//
?>

</tbody>
</table>
<input type="submit" class="button" name="ivl_download_winners" id="ivl_download_winners" value="<?php _e("Download winners result","inventive_lottery"); ?>"/>
<?php
endif;
?>
</div>
</div>

</form>