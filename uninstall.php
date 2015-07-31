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

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

/*
$option_name = 'plugin_option_name';
delete_option( $option_name );
delete_site_option( $option_name ); 
*/ 

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}inventive_lottery_lotteries" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}inventive_lottery_participants" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}inventive_lottery_countries" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}inventive_lottery_calendar" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}inventive_lottery_linked_prizes" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}inventive_lottery_calendar" );
?>