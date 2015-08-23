<?php
/*
Plugin Name: Inventive lotteries and giveaways lite
Description: Inventive lotteries and giveaways is the perfect tool to boost your sales and customer fidelity in your online blog, website or shop.
Version: 1.01
Author: 1nventive (Francesco Puglisi)
Author URI: http://www.1nventive.it
License: GPL2

Copyright 2015 Inventive3d (info@inventive3d.com)

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

if ( !class_exists( 'inventive_lottery' ) ) :

class inventive_lottery {
	
	public function __construct() {
		
		load_plugin_textdomain( 'inventive_lottery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_action('init',array($this,'main_functions'));
		
		register_activation_hook( __FILE__, array($this,'inventiveLottery_install') );
		register_activation_hook( __FILE__, array($this,'inventiveLottery_install_data') );	
		
		include_once('includes/classes.php');
		include_once('includes/extraction-methods.php');
		
		} //end construct
		
		
		///creiamo la tabella in db
public function inventiveLottery_install() {


global $wpdb;
$inventiveLottery_db_version = "1.0";

////creiamo la tabella lotteries
   $table_name = $wpdb->prefix . "inventive_lottery_lotteries";
   
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description text NOT NULL,
  image VARCHAR(255) DEFAULT '' NOT NULL,
  lottery_mode varchar(200) NOT NULL,
  email_sender varchar (100) NOT NULL,
  email_email varchar (100) NOT NULL,
  email_message text NOT NULL,
  email_subject varchar(155) NOT NULL,
  linked_prizes text NOT NULL,
  working_days varchar (255) NOT NULL,
  move_unassigned_prizes mediumint(1) NOT NULL,
  participation_mode text NOT NULL,
  generate_order tinyint(1) NOT NULL,
  allowed_countries text not null,
  age_restriction tinyint(3) not null,
  conditions text not null,
  prizes_template tinyint(1) not null,
  show_date tinyint(1) not null,
  animated tinyint(1) not null,
  convert_role varchar(100) not null,
  UNIQUE KEY id (id)
    );";
	 
   $wpdb->query( $sql );
   
   ///creiamo la tabella dei partecipanti ai lotteries
    $table_name = $wpdb->prefix . "inventive_lottery_participants";
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  user_id mediumint(9) NOT NULL,
  lottery mediumint(9) NOT NULL,
  questions text NOT NULL,
  date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  UNIQUE KEY id (id)
    );";
	 
  $wpdb->query( $sql );
  
 
   
   ///creiamo la tabella della lotteries_calendar dei lotteries
   $table_name = $wpdb->prefix . "inventive_lottery_calendar";
      
  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  lottery mediumint(9) NOT NULL,
  start_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  end_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  UNIQUE KEY id (id)
    );";
	
	
	 
  $wpdb->query( $sql );
  
  

 $table_name = $wpdb->prefix . "inventive_lottery_linked_prizes";
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  prizes_id VARCHAR(255) DEFAULT '' NOT NULL,
  lottery mediumint(5) NOT NULL,
  lottery_calendar int(11) NOT NULL,
  extraction text NOT NULL,
  insert_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  winner int(11) NOT NULL,
  date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  UNIQUE KEY id (id)
    );";
	 
   $wpdb->query( $sql );
 
   add_option( "inventiveLottery_db_version", $inventiveLottery_db_version );
}

///default data
public function inventiveLottery_install_data() {
   global $wpdb;
  
}
		public function main_functions()
		{
		//all the actions and filters
		add_action('admin_enqueue_scripts', array($this,'inventiveLottery_script'));
		add_action('admin_enqueue_scripts', array($this,'inventiveLottery_calendar_script'));
		
		add_action( 'wp_ajax_nopriv_ivl-myajax-submit', array($this,'inventive_lottery_ajax_submit') );
		add_action( 'wp_ajax_ivl-myajax-submit', array($this,'inventive_lottery_ajax_submit') );
		
		add_action( 'admin_menu', array($this,'ivl_menu_create_menu') );
		
		if (!is_admin()) add_action('wp_enqueue_scripts', array($this,'inventiveLottery_frontend_style'));
	
		add_filter('the_content',array($this,'lottery_in_content'));
		add_action('wp_footer', array($this,'footer_content'));
		add_action( 'register_form', array($this,'add_registration_message') );
		
		
		add_action('init',array($this,'download_winners_csv'));
		
		
		}
		
		public function lottery_in_content($content)
{
global $post;

if ((get_post_meta( $post->ID, 'inventive_lottery_id', true ) != 0)):
$lottery_content = new inventive_lottery_content();
$atts = array("id" => get_post_meta($post->ID, 'inventive_lottery_id', true) );
ob_start();
$lottery_content->main_lottery($atts);
$result = ob_get_contents();
ob_end_clean();
$content .= $result;
endif;
	
return $content;
	
}
		
		public function footer_content()
		{
		$lang = explode("-",get_bloginfo('language'));
		echo '
		<script>
		var ivl_lang = "'.$lang[0].'";
		var ivl_loading ="'.__("...Loading...","inventive_lottery").'";
		</script>
		<div class="overlay-inventive-lottery">
		<div id="chiudi-inventive_lottery">'.__("X Close","inventive_lottery").'</div>
		<div id="inventive_lottery-caricamento"></div>
		<div id="overlay-inventive_lottery-contenitore"></div>
		</div>	';
			
		}

public function download_winners_csv()
{

$helpers = new inventive_lottery_helpers();

  if ($helpers->issetor($_POST['ivl_download_winners'])) {
	  
	global $wpdb;
	
    header("Content-type: application/x-msdownload",true,200);
    header("Content-Disposition: attachment; filename=lottery-calendar-".sanitize_text_field($_POST['modifica'])."-winners.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
	
	$select_winners = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."inventive_lottery_linked_prizes as winners
	left join ".$wpdb->prefix."posts as prize on (prize.ID = winners.prizes_id and prize.post_status = 'publish')
	WHERE winners.winner != 0 and lottery_calendar = %s",sanitize_text_field($_POST['modifica'])));

	foreach ($select_winners as $vincitori):
	
	$user_info = get_user_by('id',$vincitori->winner);
	echo $user_info->user_login.';';
	echo $vincitori->post_title.';';
	echo $vincitori->date.'';
	echo "\r\n";

	endforeach;

    exit();
}

}
		


public function inventiveLottery_calendar_script() {
	 if (isset($_GET['page']) && (($_GET['page'] == 'inventive-lottery-insert-lottery')||($_GET['page'] == 'inventive-lottery-insert-lottery-calendar')||($_GET['page'] == 'inventive-lottery-insert-questions')||($_GET['page'] == 'inventive-lottery-list-questions'))) :
	    wp_enqueue_media();
		wp_enqueue_style( 'inventiveLottery-jquery-ui-style', plugins_url( 'css/jquery-ui.min.css', __FILE__ ));
		wp_enqueue_style( 'inventiveLottery-jquery-ui-style-2', plugins_url( 'css/jquery-ui.structure.min.css', __FILE__ ) );
		wp_enqueue_style( 'inventiveLottery-jquery-ui-style-3', plugins_url( 'css/jquery-ui.theme.min.css', __FILE__ ) );

	  endif;
}


public function add_registration_message() {

///we add custom messages if one of our active lotteries is in registration mode
$helpers = new inventive_lottery_helpers();
$active_lotteries = $helpers->get_active_lotteries("");

foreach ($active_lotteries as $lottery):

if ($helpers->get_lottery_participation_mode($lottery->id) == "registration"):

if ($helpers->issetor($helpers->get_lottery_participation_mode_values("registration",$lottery->id)->registration_message)):
echo '<div class="ivl-register-message">';
echo $helpers->get_lottery_participation_mode_values("registration",$lottery->id)->registration_message;
echo '</div>';
endif;
endif;

endforeach;

  
}


public function inventiveLottery_script() {
	 
	    wp_enqueue_media();
		wp_enqueue_style( 'inventiveLottery-style', plugins_url( 'css/style.css', __FILE__ ));
		wp_enqueue_style( 'inventiveLottery-timepicker-style', plugins_url( 'css/timepicker.css', __FILE__ ));
		
		wp_register_script('inventiveLottery_timepicker', plugins_url( 'scripts/timepicker.js', __FILE__ ));
		wp_enqueue_script('inventiveLottery_timepicker');
        wp_register_script('inventiveLottery_script', plugins_url( 'scripts/scripts.js', __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-datepicker'));
        wp_enqueue_script('inventiveLottery_script');
		
		wp_enqueue_script( 'ivlAjax', plugins_url('/includes/ajax.php', __FILE__) );
		wp_register_script('ivlAjax', plugins_url('/includes/ajax.php',  __FILE__ ));
		wp_localize_script( 'ivlAjax', 'ivlAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); 

}

////CARICHIAMO LO STILE FRONT END

public function inventiveLottery_frontend_style() {
	
	    wp_enqueue_script( 'ivl-ajax-request', plugins_url( '/includes/ajax.php' , __FILE__ ), array( 'jquery' ) );
		wp_localize_script( 'ivl-ajax-request', 'ivlAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); 	
			
	    wp_enqueue_style( 'inventiveLottery-frontend-style', plugins_url( 'css/frontend-style.css', __FILE__ ) );
		wp_register_script('inventiveLottery-frontend-scripts', plugins_url( 'scripts/frontend-scripts.js', __FILE__ ));
        wp_enqueue_script('inventiveLottery-frontend-scripts');
		wp_enqueue_style( 'inventiveLottery-countdown-style', plugins_url( 'scripts/jquery-countdown/jquery.countdown.css', __FILE__ ));
		
        wp_register_script('inventiveLottery-jquery-plugin-scripts', plugins_url( 'scripts/jquery-countdown/jquery.plugin.min.js', __FILE__ ));
	    wp_enqueue_script('inventiveLottery-jquery-plugin-scripts');
		wp_register_script('inventiveLottery-jquery-countdown-scripts', plugins_url( 'scripts/jquery-countdown/jquery.countdown.min.js', __FILE__ ));
	    wp_enqueue_script('inventiveLottery-jquery-countdown-scripts');
		$lang = explode("-",get_bloginfo('language'));		
		if ($lang[0] != "en") :
		wp_register_script('inventiveLottery-jquery-countdown-lang-scripts', plugins_url( 'scripts/jquery-countdown/jquery.countdown-'.$lang[0].'.js', __FILE__ ));
	    wp_enqueue_script('inventiveLottery-jquery-countdown-lang-scripts');
		endif;
	

} 



////creiamo il menu inventiveLottery
public function inventiveLottery_menu(){
    	add_menu_page('Inventive lottery', 
		'Inventive lottery', 
		'manage_options', 
		'inventive-lottery-settings', 
		'inventiveLottery_options');
		

}

public function ivl_menu_create_menu() {
//create custom top-level menu
add_menu_page( 'inventiveLottery',__("Inventive Lottery","inventive_lottery"),'manage_options','inventive-lottery-settings',array($this,'click_inventiveLottery'));
//create submenu items
add_submenu_page( 'inventive-lottery-settings', __("Lotteries listing","inventive_lottery"), __("Lotteries listing","inventive_lottery"), 'manage_options',
'inventive-lottery-list-lotteries', array($this,'click_list') );

add_submenu_page( 'inventive-lottery-settings', __("Insert lottery","inventive_lottery"), __("Insert lottery","inventive_lottery"), 'manage_options',
'inventive-lottery-insert-lottery', array($this,'click_insert') );

add_submenu_page( 'inventive-lottery-settings', __("Lotteries calendar","inventive_lottery"), __("Lotteries calendar","inventive_lottery"), 'manage_options',
'inventive-lottery-calendar', array($this,'click_lotteries_calendar') );

add_submenu_page( 'inventive-lottery-settings', __("Insert lottery in calendar","inventive_lottery"), __("Insert lottery in calendar","inventive_lottery"), 'manage_options',
'inventive-lottery-insert-lottery-calendar', array($this,'click_insert_lotteries_calendar') );

}

///diciamo quale pagina aprire al click sul menu instant win
public function click_inventiveLottery(){
   	include('admin/index.php');
}
public function click_list(){
   	include('admin/list.php');
}

public function click_lotteries_calendar(){
   	include('admin/lotteries-calendar.php');
}

public function click_insert(){
   	include('admin/insert.php');
}

public function click_insert_lotteries_calendar(){
   	include('admin/insert-lotteries-calendar.php');
}


////////////RICHIESTA AJAX
 
public function inventive_lottery_ajax_submit() {
	
    // generate the response
    $response = json_encode( array( 'success' => true ) );
   include('includes/ajax.php');
 
    exit;
}

	
} //end lottery class

new inventive_lottery();
if (!is_admin()) include_once('includes/lottery-content.php');
include('admin/metabox.php');	

endif; //end if class doesn't exists