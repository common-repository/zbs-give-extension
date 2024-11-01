<?php 
/*!
 * Zero BS CRM
 * http://zerobscrm.com
 * V2.0
 *
 * Copyright 2017, ZeroBSCRM.com, Epic Plugins, StormGate Ltd.
 *
 * Date: 06/07/17
 */


#} Initial Vars
global 	$zeroBSCRM_GiveWPdb_version,$zeroBSCRM_GiveWPversion,$zeroBSCRM_extensions;
		$zeroBSCRM_GiveWPdb_version 			= "1.0";
		$zeroBSCRM_GiveWPversion 				= "1.0";
		$zeroBSCRM_GiveWPconfigkey 				= 'givewp';
		//$zeroBSCRM_extensions[] = 				$zeroBSCRM_GiveWPconfigkey;

#} Check core (ZBS Extension Tie-in) #CORELOADORDER
zeroBSCRM_GiveWPCheckCore();

#} #coreintegration - Name pass
global $zeroBSCRM_extensionsInstalledList; 
if (!is_array($zeroBSCRM_extensionsInstalledList)) $zeroBSCRM_extensionsInstalledList = array();
$zeroBSCRM_extensionsInstalledList[] = 'givewp'; function zeroBSCRM_extension_name_givewp(){ return 'GiveWP'; }


#} #CORELOADORDER
// check core plugin installed
function zeroBSCRM_GiveWPCheckCore(){


	#} If no core found, deactivate this plugin!
	if (!defined('ZBSCRMCORELOADED')){

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

	    deactivate_plugins(  __FILE__  );  

	    #} Stop plugin running
	    define('ZBSCRMCORELOADFAILURE',true);

	}

}


#} If legit...
if (!defined('ZBSCRMCORELOADFAILURE')){


	#} Settings Model. req. > v1.1 

		#} Include our defaults file - #settingsInclude
		if(!defined('ZBSCRM_INC_EXT_GIVEWP_CONFINIT')) require_once(ZEROBSCRM_GIVE_PATH . 'includes/ZeroBSCRM_GiveWP.Config.Init.php');

		#} Include the config lib if not existing (should always exist for our extensions, as will be loaded from CORE)
		if(!class_exists('WHWPConfigLib')) require_once(ZEROBSCRM_GIVE_PATH . 'includes/wh.config.lib.php');

		#} Init settings model using your defaults set in the file above
		#} Note "zeroBSCRM_extension_extensionName_defaults" var below must match your var name in the config.
		
			global $zeroBSCRM_GiveWPSettings, $zeroBSCRM_extension_extensionName_defaults;
			$zeroBSCRM_GiveWPSettings = new WHWPConfigExtensionsLib($zeroBSCRM_GiveWPconfigkey,$zeroBSCRM_extension_extensionName_defaults);
		

}



#} SETTINGS
function zeroBSCRM_extensionhtml_settings_givewp(){
    	
    global $zeroBSCRM_GiveWPSettings;

	$confirmAct = false;
	$settings = $zeroBSCRM_GiveWPSettings->getAll();
		

	#} Act on any edits!
	if (isset($_POST['editwplf'])){

		#} Retrieve
		$updatedSettings = array();
		#$updatedSettings['setting1'] = ''; if (isset($_POST['wpzbscrm_givewp_setting1']) && !empty($_POST['wpzbscrm_givewp_setting1'])) $updatedSettings['setting1'] = zeroBSCRM_textProcess($_POST['wpzbscrm_givewp_setting1']);


		#} Brutal update
		foreach ($updatedSettings as $k => $v) $zeroBSCRM_GiveWPSettings->update($k,$v);

		#} $msg out!
		$sbupdated = true;

		#} Reload
		$settings = $zeroBSCRM_GiveWPSettings->getAll();
			
	}

	#} catch resets.
	if (isset($_GET['resetsettings'])) if ($_GET['resetsettings']==1){


		if (!isset($_GET['imsure'])){

				#} Needs to confirm!	
				$confirmAct = true;
				$actionStr 				= 'resetsettings';
				$actionButtonStr 		= __w('Reset Settings to Defaults?','zerobscrm');
				$confirmActStr 			= __w('Reset All Give WP Settings?','zerobscrm');
				$confirmActStrShort 	= __w('Are you sure you want to reset these settings to the defaults?','zerobscrm');
				$confirmActStrLong 		= __w('Once you reset these settings you cannot retrieve your previous settings.','zerobscrm');

			} else {


				if (wp_verify_nonce( $_GET['_wpnonce'], 'resetclearzerobscrm' ) ){

						#} Reset
						$zeroBSCRM_GiveWPSettings->resetToDefaults();

						#} Reload
						$settings = $zeroBSCRM_GiveWPSettings->getAll();

						#} Msg out!
						$sbreset = true;

				}

			}

	} 


	if (!$confirmAct){

	?>
    
        <p id="sbDesc"><?php _we('Below you can choose global settings for the Zero BS CRM to Give WP Connector','zerobscrm'); ?></p>

        <?php if (isset($sbupdated)) if ($sbupdated) { echo '<div style="width:500px; margin-left:20px;" class="wmsgfullwidth">'; zeroBSCRM_html_msg(0,__w('Settings Updated','zerobscrm')); echo '</div>'; } ?>
        <?php if (isset($sbreset)) if ($sbreset) { echo '<div style="width:500px; margin-left:20px;" class="wmsgfullwidth">'; zeroBSCRM_html_msg(0,__w('Settings Reset','zerobscrm')); echo '</div>'; } ?>
       
        <div id="sbA">

        		<form method="post">
        			<input type="hidden" name="editwplf" id="editwplf" value="1" />
        			 <table class="table table-bordered table-striped wtab" style="width:780px;margin:10px;">
	               
	                   <thead>
	                    
	                        <tr>
	                            <th colspan="2" class="wmid"><?php _we('GiveWP Settings','zerobscrm'); ?>:</th>
	                        </tr>

	                    </thead>
	                    
	                    <tbody>
	                    	                    
	                    	<!-- <tr>
	                    		<td class="wfieldname" style="width:300px"><label for="wpzbscrm_givewp_setting1"><?php _we('Setting1'); ?>:</label></td>
	                    		<td><input type="text" class="winput form-control" name="wpzbscrm_givewp_setting1" id="wpzbscrm_givewp_setting1" value="<?php if (isset($settings['setting1']) && !empty($settings['setting1'])) echo $settings['setting1']; ?>" placeholder="e.g. Mike's Widgets" /></td>
	                    	</tr> -->
	                    	        
			
	                    </tbody>

	                </table>
					

	                <table class="table table-bordered table-striped wtab" style="width:780px;margin:10px;">
	               		<tbody>

	                    	<tr>
	                    		<td colspan="2" class="wmid"><button type="submit" class="button button-primary button-large"><?php _we('Save Settings','zerobscrm'); ?></button></td>
	                    	</tr>

	                    </tbody>
	                </table>

	            </form>


	            <?php /*<table class="table table-bordered table-striped wtab" style="width:780px;margin:10px;margin-top:40px;">
	               
	                   <thead>
	                        <tr>
	                            <th class="wmid">Zero BS CRM Mail Campaigns: <?php _we('Extra Tools','zerobscrm'); ?></th>
	                        </tr>
	                    </thead>
	                    
	                    <tbody>
	                    	<tr>
	                    		<td>
	                    			<p style="padding: 10px;text-align:center;">
		                    			<button type="button" class="button button-primary button-large" onclick="javascript:window.location='?page=<?php echo $zeroBSCRM_MailCampaignsslugs['settings']; ?>&resetsettings=1';"><?php _we('Restore default settings','zerobscrm'); ?></button>
		                    		</p>
		                    	</td>
	                    	</tr>
	                    </tbody>
	            </table>*/ ?>

	            <script type="text/javascript">

	            	jQuery(document).ready(function(){


	            	});


	            </script>
	            
   		</div><?php 
   		
   		} else {

   				?><div id="clpSubPage" class="whclpActionMsg six">
        		<p><strong><?php echo $confirmActStr; ?></strong></p>
            	<h3><?php echo $confirmActStrShort; ?></h3>
            	<?php echo $confirmActStrLong; ?><br /><br />
            	<button type="button" class="button button-primary button-large" onclick="javascript:window.location='<?php echo wp_nonce_url('?page='.$zeroBSCRM_MailCampaignsslugs['settings'].'&'.$actionStr.'=1&imsure=1','resetclearzerobscrm'); ?>';"><?php echo $actionButtonStr; ?></button>
            	<button type="button" class="button button-large" onclick="javascript:window.location='?page=<?php echo $zeroBSCRM_MailCampaignsslugs['settings']; ?>';"><?php _we("Cancel",'zerobscrm'); ?></button>
            	<br />
				</div><?php 
   		} 

}
