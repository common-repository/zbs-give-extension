<?php 
/*!
 * Zero BS CRM: Parent Checks for ZBS Core 
 * https://zerobscrm.com
 * V3.0
 *
 * Copyright 2019 - ZeroBSCRM.com
 *
 * Date: 13/06/2019
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
	if ( ! defined( 'ABSPATH' ) ) exit();
/* ======================================================
  / Breaking Checks
   ====================================================== */


/* ======================================================
  	This generic includes checks for a core ZBS Install, 
  	and provides generic killswitches for version requirements.
   ====================================================== */

	// wrapped in check to avoid multi-inclusion, across ext.
	if (!function_exists('zeroBSCRM_generic_parentsPresent')){

	/* ======================================================
	  	General Core Presence Check
	   ====================================================== */

		// generic check, if not already inc.
		function zeroBSCRM_generic_parentsPresent($basefile=false){

		    if (is_admin() && current_user_can( 'activate_plugins' )) {

				#} Check for parent plugin
				$requirementsMet = true;
				if (!defined('ZBSCRMCORELOADED')) { 
					
					// can't see that the CORE is present, so kill.
					$requirementsMet = false; 
					add_action( 'admin_notices', 'zeroBSCRM_generic_ZBSMissingNotice' ); 

				}

				// deactivate
		        if (!$requirementsMet) zeroBSCRM_generic_deactivateThisPlugin($basefile);
			   
		    }

		}

		function zeroBSCRM_generic_ZBSMissingNotice(){
		    ?><div class="error">
		    <p><?php _e('This Zero BS CRM Extension requires','zero-bs-crm'); ?> <a href="https://wordpress.org/plugins/zero-bs-crm/" target="_blank">ZBS CRM v3.0</a> <?php _e('WordPress Plugin. Please install and activate Zero BS CRM first.','zero-bs-crm'); ?></p>		  
		    <?php #} Needs installing or activating? - not perfect
		    $pluginDir = 'zero-bs-crm/ZeroBSCRM.php';
		    if(file_exists(WP_PLUGIN_DIR.'/'.$pluginDir)){
		    		?><p><?php _e('Zero BS CRM seems to beinstalled. Please activate the plugin.','zero-bs-crm'); ?></p></div><?php
		    } else {
		    	?><p><a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=zero-bs-crm'); ?>" class="button"><?php _e('Please Click Here','zero-bs-crm'); ?></a> <?php _e('and then click install in the bottom left.','zero-bs-crm'); ?></p></div><?php
		    }
		}
	/* ======================================================
	  / General Core Presence Check
	   ====================================================== */

	/* ======================================================
	  	Required Version Checks
	   ====================================================== */

	   	// checks for (minimum) parent ver
		function zeroBSCRM_generic_coreVerCheck($ver="2.53", $basefile=false){

		    if (!empty($ver) && is_admin() && current_user_can( 'activate_plugins' )) {

				$requirementsMet = true;

				// ver switch
				global $zbs;

				if (isset($zbs->version) && version_compare($zbs->version,  $ver, '<')){

					// <Version - can't run :)
					$requirementsMet = false; 
					global $zbsFailedOnVer; $zbsFailedOnVer = $ver; // lazy passthrough
					add_action( 'admin_notices', 'zeroBSCRM_generic_ZBSCOREVERNotice' ); 	

				}

				// deactivate
		        if (!$requirementsMet) zeroBSCRM_generic_deactivateThisPlugin($basefile);
			   
		    }
		}

		function zeroBSCRM_generic_ZBSCOREVERNotice(){

			// catch if already fired
			if (!defined('ZBS_MISSING_VERSION')){

				$ver = '3.0'; global $zbsFailedOnVer; if (isset($zbsFailedOnVer)) $ver = $zbsFailedOnVer;
			    ?><div class="error">
			    	<p><?php _e('This Zero BS CRM Extension requires','zero-bs-crm'); ?> <a href="https://wordpress.org/plugins/zero-bs-crm/" target="_blank">ZBS CRM v<?php echo $ver; ?></a> <?php _e('WordPress Plugin. Please install and activate Zero BS CRM','zero-bs-crm'); echo ' v'.$ver.' ';  _e('before activating this extension.','zero-bs-crm'); ?></p>
			    </div><?php

			    define('ZBS_MISSING_VERSION',1);

			}
		}

	   	// checks for (minimum) parent DAL ver
		function zeroBSCRM_generic_dalVerCheck($ver="2.53", $basefile=false){

		    if (!empty($ver) && is_admin() && current_user_can( 'activate_plugins' )) {

				$requirementsMet = true;

				// ver switch
				global $zbs;

				if (isset($zbs->dal_version) && version_compare($zbs->dal_version,  $ver, '<')){

					// <Version - can't run :)
					$requirementsMet = false; 
					global $zbsFailedOnVer; $zbsFailedOnVer = $ver; // lazy passthrough
					add_action( 'admin_notices', 'zeroBSCRM_generic_ZBSDALVERNotice' ); 	

				}

				// deactivate
		        if (!$requirementsMet) zeroBSCRM_generic_deactivateThisPlugin($basefile);
			   
		    }
		}

		function zeroBSCRM_generic_ZBSDALVERNotice(){

			// catch if already fired
			if (!defined('ZBS_MISSING_VERSION')){
				
				$ver = '3.0'; global $zbsFailedOnVer; if (isset($zbsFailedOnVer)) $ver = $zbsFailedOnVer;
			    ?><div class="error">
			    	<p><?php _e('This Zero BS CRM Extension requires','zero-bs-crm'); ?> <a href="https://wordpress.org/plugins/zero-bs-crm/" target="_blank">ZBS CRM v<?php echo $ver; ?></a> <?php _e('WordPress Plugin. Please install and activate Zero BS CRM, and make sure your database has been fully migrated to ','zero-bs-crm'); echo ' v'.$ver.' ';  _e('before activating this extension.','zero-bs-crm'); ?></p>
			    </div><?php

			    define('ZBS_MISSING_VERSION',1);

			}
		}
		
	/* ======================================================
	  / Required Version Checks
	   ====================================================== */

	/* ======================================================
	  	Helper Funcs
	   ====================================================== */

		function zeroBSCRM_generic_deactivateThisPlugin($basefile=false){

			if ($basefile !== false){
		    	#} Nope.
		    	deactivate_plugins( plugin_basename( $basefile ) ); 
		        if ( isset( $_GET['activate'] ) ) {
		            unset( $_GET['activate'] );
		        }
		    }

		}
		
	/* ======================================================
	  / Helper Funcs
	   ====================================================== */


} // / if func exists