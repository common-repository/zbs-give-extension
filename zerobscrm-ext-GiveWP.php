<?php
/*
Plugin Name: Give to ZBS CRM Connector
Plugin URI: http://zerobscrm.com
Description: Captures GiveWP Donars and Donations into your Zero BS CRM.
Version: 1.2.1
Author: https://zerobscrm.com
*/

	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;

	// Define WC_PLUGIN_FILE.
	if ( ! defined( 'ZBS_GIVEWP_ROOTFILE' ) ) {
		#} Define Paths
		define( 'ZBS_GIVEWP_ROOTFILE', __FILE__ );		
		define( 'ZEROBSCRM_GIVE_PATH', plugin_dir_path(__FILE__) );
	}

	/* ======================================================
	   Parent + Version Checks
	   ====================================================== */
	   if (!function_exists('zeroBSCRM_generic_parentsPresent')) require_once(plugin_dir_path(__FILE__) . 'includes/ZeroBSCRM_parentCheck.php');
	   function zeroBSCRM_give_parentCheck(){

	   		// check for parent
	   		zeroBSCRM_generic_parentsPresent(__FILE__);

	   } add_action( 'admin_init', 'zeroBSCRM_give_parentCheck', 1);
	/* ======================================================
	   / Parent + Version Checks
	   ====================================================== */


	/* ======================================================
	   init 
	   ====================================================== */
	function zeroBSCRM_giveWP_postInit(){
	
		global $zbs, $zeroBSCRM_extensionsInstalledList;
		if (!is_array($zeroBSCRM_extensionsInstalledList)) $zeroBSCRM_extensionsInstalledList = array();
		$zeroBSCRM_extensionsInstalledList[] = 'givewp';


        // DAL3 switch
        if (isset($zbs->dal_version) && version_compare($zbs->dal_version,  "2.53", '<=') > 0){
            
            // < DAL3 Version
            include_once(  plugin_dir_path( __FILE__ ) . 'includes/ZeroBSCRM_GiveWP.DAL2.php');

        } else {
            
            // DAL3+ Version:
            include_once(  plugin_dir_path( __FILE__ ) . '/includes/ZeroBSCRM_GiveWP.DAL3.php');
        }

   	} add_action('after_zerobscrm_settings_preinit', 	'zeroBSCRM_giveWP_postInit',1);

	/* ======================================================
	   / init 
	   ====================================================== */
	

	function zeroBSCRM_extension_name_givewp(){ return 'Give to ZBS CRM Connector'; }
	function zeroBSCRM_extension_file_givewp(){ return  __FILE__ ; }