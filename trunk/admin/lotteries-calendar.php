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
$helpers = new inventive_lottery_helpers();
////SALVATAGGIO DATI
if (isset($_POST['id_lottery']))
{
	
	
if (isset($_POST['modifica']))
{
	////verifichiamo la modalità di lottery del lottery
	$lottery_lottery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries WHERE id = %s",intval($_POST['id_lottery'])));

	$date_iniziale = sanitize_text_field($_POST['start_date']);
	$date_finale = sanitize_text_field($_POST['end_date']);
		
	$_POST['start_date'] = date( 'Y-m-d H:i:s', strtotime( $date_iniziale) );
	$_POST['end_date'] = date( 'Y-m-d H:i:s', strtotime( $date_finale)  );

	$wpdb->update( 
	$wpdb->prefix . "inventive_lottery_calendar", 
	array( 
		'lottery' => intval($_POST['id_lottery']), 
		'start_date' => sanitize_text_field($_POST['start_date']),
		'end_date' => sanitize_text_field($_POST['end_date'])
	),
	array( 'ID' => intval($_POST['modifica']) )
);

}
else ///se stiamo inserendo nuove programmazioni di lotteries
{

	$date_iniziale = sanitize_text_field($_POST['start_date']);
	$date_finale = sanitize_text_field($_POST['end_date']);
	$_POST['start_date'] = date( 'Y-m-d H:i:s', strtotime( $date_iniziale) );
	$_POST['end_date'] = date( 'Y-m-d H:i:s', strtotime( $date_finale)  );

////verifichiamo la modalità di lottery del lottery
	$lottery_lottery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries WHERE id = %s",intval($_POST['id_lottery'])));
    $lottery_mode = json_decode($lottery_lottery->lottery_mode);


$wpdb->insert( 
	$wpdb->prefix . "inventive_lottery_calendar", 
	array( 
		'lottery' => intval($_POST['id_lottery']), 
		'start_date' => sanitize_text_field($_POST['start_date']),
		'end_date' => sanitize_text_field($_POST['end_date'])
		//'lottery' => $lottery_lottery->lottery_mode.'|'.$lottery
));

$id_programmazione_lottery = $wpdb->insert_id;

		////se l'lottery è in base all'orario, selezioniamo dal lottery i premi assegnati ed inseriamoli in db
$premi_assegnati = explode(",",$lottery_lottery->linked_prizes);
		///al momento è possibile assegnare solo products, in futuro sarà possibile assegnare i coupon o altro
		foreach ($premi_assegnati as $premi) :

		if ($lottery_mode->mode == "random"):
	
	$extraction = json_encode(array("mode"=>'random',"data"=>$lottery_mode->chance_to_win));
	
	endif;
	
	////inseriamo nella tabella premi_collegati i dati
	$wpdb->insert( 
	$wpdb->prefix . "inventive_lottery_linked_prizes", 
	array( 
		'prizes_id' => $premi, 
		'lottery' => intval($_POST['id_lottery']),
		'lottery_calendar' => $id_programmazione_lottery,
		'extraction' => $extraction,
		'insert_date' => current_time( 'mysql' )
));
	
	endforeach; //fine del ciclo dei products assegnati



}


}


///se stiamo cancellando
if (isset($_GET['c']))
{
$wpdb->delete( $wpdb->prefix."inventive_lottery_calendar", array( 'ID' => intval($_GET['c']) ) );		
}


///selezioniamo tutti i lotteries presenti
$lotteries = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."inventive_lottery_calendar order by start_date DESC",ARRAY_A);
?>
<div class="wrap">
<h2><?php _e("Inventive lottery","inventive_lottery"); ?></h2>

<h3><?php _e("Lotteries calendar","inventive_lottery"); ?></h3>

<table class="widefat">
<thead>
<tr>
<th> <?php _e("Id","inventive_lottery"); ?></th>
<th> <?php _e("Lottery","inventive_lottery"); ?> </th>
<th> <?php _e("Participants","inventive_lottery"); ?> </th>
<th> <?php _e("Winners","inventive_lottery"); ?> </th>
<th> <?php _e("Start","inventive_lottery"); ?> </th>
<th> <?php _e("End","inventive_lottery"); ?> </th>
<th> <?php _e("Actions","inventive_lottery"); ?> </th>
</tr>
</thead>
<tfoot>
<tr>
<th> </th>
<th>  </th>
<th>  </th>
<th>  </th>
<th>  </th>
<th>  </th>
</tr>
</tfoot>
<tbody>

<?php
foreach($lotteries as $row)
{
	$adesso = current_time( 'mysql' );
	///il giorno della settimana
	$condizione_attivita = "and lottery.working_days like '%".date('N').":1,%'";
	
	$active_lottery = $wpdb->get_row($wpdb->prepare("SELECT *, lottery_calendar.id as calendar_id FROM ".$wpdb->prefix."inventive_lottery_calendar as lottery_calendar
	left join ".$wpdb->prefix."inventive_lottery_lotteries as lottery on (lottery.id = lottery_calendar.lottery)
	WHERE lottery_calendar.start_date <= '".$adesso."' ".$condizione_attivita." and lottery_calendar.end_date >= '".$adesso."' and lottery_calendar.id = %s",$row['id']));
	
	if (isset($active_lottery->id)) $c_at = ' class="lottery_attivo" ';
	else $c_at = "";
	?>
	<tr <?php echo $c_at; ?>>
    <td>
    <?php echo $row['id']; ?>
    </td>
    <td>
<?php

///tiriamo fuori il lottery collegato
$lottery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_lotteries WHERE id = %s",$row['lottery']));
echo $lottery->title;
?>
</td>
<td>
<?php
//selezioniamo i partecipanti totali

$partecipanti_totali = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM ".$wpdb->prefix."inventive_lottery_participants WHERE lottery = %s",$row['id']) );
echo $partecipanti_totali;
?>
</td>
<td>
<?php

///tiriamo fuori tutti i vincitori di questa programmazione lottery
$totale_vincitori = $wpdb->get_results($wpdb->prepare("SELECT count(id) as total_winners FROM ".$wpdb->prefix."inventive_lottery_linked_prizes as winners
	WHERE winners.winner != 0 and lottery_calendar = %s",$row['id']));

	echo $totale_vincitori[0]->total_winners;
//

?>
</td>
<td>
<?php
	echo $row['start_date'];
	?>
</td>
<td>
<?php
	echo $row['end_date'];
	?>
</td>
<td> 
<a href='?page=inventive-lottery-insert-lottery-calendar&id=<?php echo $row['id']; ?>' class='button-primary'> <?php _e("Edit","inventive_lottery"); ?> </a>
<a href='?page=inventive-lottery-calendar&c=<?php echo $row['id']; ?>' class='button-primary'> <?php _e("Delete","inventive_lottery"); ?> </a>
</td>
</tr>

    <?php

}
?>

</tbody>
</table>

</div>