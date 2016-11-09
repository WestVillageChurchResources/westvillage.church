<?php
namespace Libsyn;

class Utilities extends \Libsyn{
	
    /**
     * Handles WP callback to send variable to trigger AJAX response.
     * 
     * @param <array> $vars 
     * 
     * @return <array>
     */
	public static function plugin_add_trigger_libsyn_check_ajax($vars) {
		$vars[] = 'libsyn_check_url';
		return $vars;
	}
	
    /**
     * Handles WP callback to save ajax settings
     * 
     * @param <array> $vars 
     * 
     * @return <array>
     */
	public static function plugin_add_trigger_libsyn_oauth_settings($vars) {
		$vars[] = 'libsyn_oauth_settings';
		return $vars;
	}
	
    /**
     * Handles WP callback to clear outh settings
     * 
     * @param <array> $vars 
     * 
     * @return <array>
     */
	public static function plugin_add_trigger_libsyn_update_oauth_settings($vars) {
		$vars[] = 'libsyn_update_oauth_settings';
		return $vars;
	}
	
    /**
     * Renders a simple ajax page to check against and test the ajax urls
     * 
     * 
     * @return <type>
     */
	public static function checkAjax() {
		if(intval(get_query_var('libsyn_check_url')) === 1) {
			$error = false;
			$json = true; //TODO: may need to do a check here later.
			//set output
			header('Content-Type: application/json');
			if(!$error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
    /**
     * Saves Settings form oauth settings for dialog
     * 
     * 
     * @return <type>
     */
	public static function saveOauthSettings() {
		if(intval(get_query_var('libsyn_oauth_settings')) === 1) {
			$error = false;
			$json = true; //TODO: may need to do a check here later.
			$sanitize = new \Libsyn\Service\Sanitize();		
			
			if(isset($_POST['clientId'])&&isset($_POST['clientSecret'])) { 
				update_option('libsyn-podcasting-client', array('id' => $sanitize->clientId($_POST['clientId']), 'secret' => $sanitize->clientSecret($_POST['clientSecret']))); 
				$clientId = $_POST['clientId']; 
				$clientSecret = $_POST['clientSecret'];
			}
			if(!empty($clientId)) $json = json_encode(array('client_id' => $clientId, 'client_secret' => $clientSecret));
				else $error = true;
			
			//set output
			header('Content-Type: application/json');
			if(!$error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
    /**
     * Saves Settings form oauth settings for dialog
     * 
     * 
     * @return <type>
     */
	public static function updateOauthSettings() {
		if(intval(get_query_var('libsyn_update_oauth_settings')) === 1) {
			$error = false;
			$json = true;
			$sanitize = new \Libsyn\Service\Sanitize();
			$json = 'true'; //set generic response to true
			
			if(isset($_GET['client_id']) && isset($_GET['client_secret'])) {
				update_option('libsyn-podcasting-client', array('id' => $sanitize->clientId($_GET['client_id']), 'secret' =>$sanitize->clientSecret($_GET['client_secret']))); 
			} else {
				$error=true;
				$json ='false';
			}
			
			//set output
			header('Content-Type: application/json');
			if(!$error) echo json_encode($json);
				else echo json_encode(array());
			exit;
		}
	}
	
    /**
     * Clears Settings and deletes table for uninstall
     * 
     * 
     * @return <type>
     */
	public static function uninstallSettings() {
		global $wpdb;
		$option_name = 'libsyn-podcasting-client';
		$service = new \Libsyn\Service();
		 
		delete_option( $option_name );
		// For site options in Multisite
		delete_site_option( $option_name );

		//drop libsyn db table
		$wpdb->query( "DROP TABLE IF EXISTS ".$service->api_table_name ); //currently used without prefix
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}".$service-api_table_name ); //not really needed in current build
	}
	
    /**
     * Clears Settings and deletes table for uninstall
     * 
     * 
     * @return <type>
     */
	public static function deactivateSettings() {
		global $wpdb;
		$option_name = 'libsyn-podcasting-client';
		$service = new \Libsyn\Service();
		 
		delete_option( $option_name );
		// For site options in Multisite
		delete_site_option( $option_name );

		//drop libsyn db table
		$wpdb->query( "DROP TABLE IF EXISTS ".$service->api_table_name ); //old without prefix
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}".$service->api_table_name );		
	}
}

?>