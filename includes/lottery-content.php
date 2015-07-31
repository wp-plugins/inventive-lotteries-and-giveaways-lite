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
class inventive_lottery_content {
	
	public function main_lottery($atts)
	{
	global $wpdb;
	$inventive_lottery = new inventive_lottery();
	$helpers = new inventive_lottery_helpers();

$active_lottery = $helpers->get_active_lotteries($helpers->issetor($atts['id']));

$file = dirname(__FILE__) . '/inventive-lottery.php';
$plugin_url = str_replace("includes/","",plugin_dir_path($file));

?>

<?php 
////lottery title
if (!isset($active_lottery[0]->title)) echo '<h1 class="title-lottery">'.__("There is no lottery active right now","inventive_lottery").'</h1>';

if (isset($active_lottery[0]->title)) 
{
	////lottery start/end date
if ($active_lottery[0]->show_date) :
echo '<div class="ivl_lottery_show_date">'.
sprintf(
__(
'Starts %1$s / Ends %2$s','inventive_lottery'
),
date_i18n( get_option( 'date_format' ), strtotime($active_lottery[0]->start_date)),
date_i18n( get_option( 'date_format' ), strtotime($active_lottery[0]->end_date))
).'</div>';
endif;


///lottery title
echo '<div class="title-lottery">'.$active_lottery[0]->title.'</div>';


////lottery image
if ($active_lottery[0]->image) echo '<img class="image-lottery" src="'.$active_lottery[0]->image.'">';

////lottery description
echo '<div class="description-lottery">'.nl2br($active_lottery[0]->description).'</div>';

?>
	
<?php
///COUNTDOWN
echo '<div class="ivl-count-container">';
echo __("Just","inventive_lottery");
echo '<div id="ivl-count"></div>';
echo '<div class="ivl_at_the_end_of_lottery">'.__("at the end of the lottery","inventive_lottery").'</div>';
echo '</div>';
?>

<?php
echo '<div class="ivl-participate-now">';
echo __("Participate now","inventive_lottery");;
echo '</div>';

echo '<div class="ivl-participate-now-step-2">';
	///if we can access this lottery

	if ($helpers->participation_status($active_lottery[0]->id) === TRUE):


		include($plugin_url."templates/access-success.php");

		else: //we show up messages about why not we can access this lottery
		include($plugin_url."/templates/welcome.php");	
		
		if (!get_current_user_id()) wp_login_form(array("remember"=>false)); 
		
		endif;
		
		 

echo '<input type="hidden" name="ivl_calendar_id" id="ivl_calendar_id" value="'.$active_lottery[0]->id_calendar.'" \>';
echo '<input type="hidden" name="ivl_lottery_id" id="ivl_lottery_id" value="'.$active_lottery[0]->id.'" \>';
echo '<input type="hidden" name="ivl_lottery_end_date" id="ivl_lottery_end_date" value="'.$active_lottery[0]->end_date.'" \>';
}///end active lottery
?>
</div>
<div class="ivl_clear"></div>
<?php
} //function end




} //class end
?>

