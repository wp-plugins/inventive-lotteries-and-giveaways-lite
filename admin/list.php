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