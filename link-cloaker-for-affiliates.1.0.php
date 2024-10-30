<?php
/**
 * Plugin Name: Link Cloaker for Affiliates
 * Plugin URI: http://www.affiliatedefense.com/wp-affiliate-link-cloaker
 * Description: Easily cloak affiliate links by displaying a new, user friendly URL instead.
 * Version: 1.0
 * Author: AffiliateDefense
 * Author URI: http://www.affiliatedefense.com
 * License: Commercial
 */
 
//Installation
global $alc_db_version;
$alc_db_version = "1.0";

//Install database table
function alc_install() {
   global $wpdb;
   global $alc_db_version;

   $table_name = $wpdb->prefix . "affiliate_link_cloaker";
      
    $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  api_key text NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "alc_db_version", $alc_db_version );
}

//Setup new user
function alc_install_data() {
	global $wpdb;
	$apiKey = time().rand(0,999999);
	$table_name = $wpdb->prefix . "affiliate_link_cloaker";
	$rows_affected = $wpdb->insert( $table_name, array( 'api_key' => $apiKey, 'external_api_key' => 0 ) );
	wp_remote_get( 'http://www.affiliatedefense.com/api/v1/users/post/'.$apiKey );
}


//Include linkcloaker_import_admin.php for settings page
function link_cloaker_admin() {  
    include('linkcloaker_import_admin.php');  
}  

//Add setting menu
function link_cloaker_admin_actions() {  
	add_menu_page("Link Cloaking", "Link Cloaking", 1, "Link_Cloaking", "link_cloaker_admin", plugin_dir_url( __FILE__ ) . 'images/icon.png');
}  

//Link cloaking script
function link_cloaker_script() {  
	global $wpdb;	
	$table_name = $wpdb->prefix . "affiliate_link_cloaker";
	$myrow = $wpdb->get_row( "SELECT * FROM $table_name" );
	$apiKey = $myrow->api_key; 
	$response = wp_remote_get('http://www.affiliatedefense.com/api/v1/cloaks/script/'.$apiKey);
	$body = wp_remote_retrieve_body($response);
	echo $body;
}  

//Truncate database table on deactivation
function link_cloaker_deactivate() {
    global $wpdb;
	$table_name = $wpdb->prefix . "affiliate_link_cloaker";
	$sql = "TRUNCATE TABLE ". $table_name;
	$wpdb->query($sql);	
}

register_activation_hook( __FILE__, 'alc_install' );
register_activation_hook( __FILE__, 'alc_install_data' );
register_deactivation_hook( __FILE__, 'link_cloaker_deactivate' );

//Add setting menu
add_action('admin_menu', 'link_cloaker_admin_actions');  

//Add link cloaking script to footer
add_action('wp_footer', 'link_cloaker_script');
