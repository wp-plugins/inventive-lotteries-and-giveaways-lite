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
class inventive_lottery_extraction_method {
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/**********************************	    	RANDOM EXTRACTION	         *****************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/	
	public function random_extraction($dati_lottery, $ivl_active_prize)
	{
		
		global $wpdb;
		$winning_number = 100;
		$winning_chances = $dati_lottery->data;
		$rand_extraction = rand($winning_chances,100);

		if (($rand_extraction == $winning_number) && ($ivl_active_prize['winner'] == 0)) $result = true;
		else $result = false;

		return $this->extraction_response($result, $ivl_active_prize, "stop");
		
	}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/**********************************	    	EXTRACTION RESPONSE	         *****************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/	
	public function extraction_response($result, $ivl_active_prize, $stop)
	{
	global $wpdb;
		
		if ( ( $result === true) )
		{
		 ///we win 
	 	 $response = "1";
		 
		 ///update prize table
	$wpdb->update( 
	$wpdb->prefix . "inventive_lottery_linked_prizes", 
	array( 
		'winner' => get_current_user_id(),
		'date' => current_time( 'mysql' )
	),
	array( 
	'id' => $ivl_active_prize['id_riga_premio_assegnato']
	 )); 
		}
		else 
		{
		 ///we lose
		 $response =  "0";
		}
		return array("response"=>$response,"product_id"=>$ivl_active_prize['prizes_id'],"stop"=>$stop);	
		
	}
	
	
}
?>