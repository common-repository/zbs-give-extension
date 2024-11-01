<?php 
/*!
 * Zero BS CRM
 * https://zerobscrm.com
 * V3.0+
 *
 * Copyright 2019, Zero BS Software Ltd. & Zero BS CRM.com
 *
 * Date: 29/11/19
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ABSPATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */


add_action('give_update_payment_status', 'zeroBSCRM_updateTransStatus', 200, 3);
function zeroBSCRM_updateTransStatus($id, $status, $old_status){

	global $wpdb;
	$table = $wpdb->prefix . "give_donationmeta";
	//get the payment_key for transaction identification in ZBS

	$sql = $wpdb->prepare("SELECT meta_value FROM $table WHERE donation_id = %d AND meta_key = %s", $id, '_give_payment_purchase_key' );
	$purchase_key = $wpdb->get_var($sql);

	if($status == 'publish'){
		$zbt['status'] = "Completed";
	}else{
		$zbt['status'] = $status;
	}

	$orderID = zeroBSCRM_giveWP_getPostID_byMeta('zbs_givewp_uid', $purchase_key);
	if($orderID){
		$tmeta = get_post_meta($orderID, 'zbs_transaction_meta', true);
		$tmeta['status'] = $zbt['status'];
		update_post_meta( $orderID,'zbs_transaction_meta',$tmeta);
	}	



}




#} Let's get to it..
add_filter( 'give_insert_payment_args', 'zeroBSCRM_storeGiveWPDonar', 100, 2 );
function zeroBSCRM_storeGiveWPDonar( $args, $payment_data ) {

	#} Defaults
	$isZBSAddition = true;
	$fieldIndexes = array(
		'name' => -1,
		'email' => -1,
	);
	$custom_fields_array = array();

	#} Retrieve User Info
	$posted_data = $payment_data['user_info'];
	#} Check them :) / Correct if users have in diff order
	if (is_array($posted_data)){

		foreach ($posted_data as $fieldID => $field){

			#} check type
			switch ($fieldID){

				case 'first_name':

					#} Setting this presumes there's only one :D
					$fieldIndexes['name'] = $fieldID;

					break;

				case 'email':

					#} Setting this presumes there's only one :D
					$fieldIndexes['email'] = $fieldID;

					break;

				#} All other types, e.g. hidden etc. will be ignored for now
				default:

				break;

			}

			#} Custom Field support
			if (substr($fieldID,0,2) == "cf"){

				$cf_indexname = 'zbsc_' . $fieldID;
				$custom_fields_array[$cf_indexname] =  $field;

			}


		}

	}
	
	#} Is a "ZBS to add", and also has name + email fields somewhere :)
	if ($isZBSAddition && $fieldIndexes['name'] !== -1 && $fieldIndexes['email'] !== -1){

		#} Check email
		$customerEmail = ''; if (isset($posted_data[$fieldIndexes['email']]) && zeroBSCRM_validateEmail($posted_data[$fieldIndexes['email']])) $customerEmail = $posted_data[$fieldIndexes['email']];

		#} Using Integration function, add customer, (if email present!)
		if (!empty($customerEmail)){

			$zbscust = array(
		        'zbsc_prefix' 				=> '',
		        'zbsc_fname' 				=> '',
		        'zbsc_lname' 				=> '',  
		        'zbsc_suffix' 				=> '', 
		        'zbsc_hometel' 				=> '',  
		        'zbsc_country' 				=> '',
		        'zbsc_mobtel'				=> '',
		        'zbsc_worktel'				=> '',

		        'zbsc_addr1' 				=> '',
		        'zbsc_addr2' 				=> '',
		        'zbsc_city' 				=> '',  
		        'zbsc_county' 				=> '', 
		        'zbsc_postcode' 			=> '',  
		        'zbsc_country' 				=> '',

		        'zbsc_secaddr_addr1' 		=> '',
		        'zbsc_secaddr_addr2' 		=> '',
		        'zbsc_secaddr_city' 		=> '',  
		        'zbsc_secaddr_county' 		=> '', 
		        'zbsc_secaddr_postcode' 	=> '',  
		        'zbsc_secaddr_country' 		=> '',

		        'zbsc_notes' 		=> ''
			);

			#} if any, add
			if(array_key_exists('prefix', $posted_data)){$zbscust['zbsc_prefix'] = $posted_data['prefix']; } # else { $zbscust['zbsc_prefix'] = ''; }
			if(array_key_exists('first_name', $posted_data)){$zbscust['zbsc_fname']   = $posted_data['first_name']; } # else { $zbscust['zbsc_fname'] = ''; }
			if(array_key_exists('last_name', $posted_data)){$zbscust['zbsc_lname']   = $posted_data['last_name']; } # else { $zbscust['zbsc_lname'] = ''; }
			if(array_key_exists('suffix', $posted_data)){$zbscust['zbsc_suffix']   = $posted_data['suffix']; } # else { $zbscust['zbsc_suffix'] = ''; }
			if(array_key_exists('hometel', $posted_data)){$zbscust['zbsc_hometel']   = $posted_data['hometel']; } # else { $zbscust['zbsc_hometel'] = ''; }
			if(array_key_exists('worktel', $posted_data)){$zbscust['zbsc_worktel']   = $posted_data['worktel']; } # else { $zbscust['zbsc_worktel'] = ''; }
			if(array_key_exists('mobtel', $posted_data)){$zbscust['zbsc_mobtel']   = $posted_data['mobtel']; } # else { $zbscust['zbsc_mobtel'] = ''; }

			if(array_key_exists('addr1', $posted_data)){$zbscust['zbsc_addr1']   = $posted_data['addr1']; } # else { $zbscust['zbsc_addr1'] = ''; }
			if(array_key_exists('addr2', $posted_data)){$zbscust['zbsc_addr2']   = $posted_data['addr2']; } # else { $zbscust['zbsc_addr2'] = ''; }
			if(array_key_exists('city', $posted_data)){$zbscust['zbsc_city']   = $posted_data['city']; } # else { $zbscust['zbsc_city'] = ''; }
			if(array_key_exists('county', $posted_data)){$zbscust['zbsc_county']   = $posted_data['county']; } # else { $zbscust['zbsc_county'] = ''; }
			if(array_key_exists('postcode', $posted_data)){$zbscust['zbsc_postcode']   = $posted_data['postcode']; } # else { $zbscust['zbsc_postcode'] = ''; }
			if(array_key_exists('country', $posted_data)){$zbscust['zbsc_country']   = $posted_data['country']; } # else { $zbscust['zbsc_country'] = ''; }

			if(array_key_exists('saddr1', $posted_data)){$zbscust['zbsc_secaddr_addr1']   = $posted_data['saddr1']; } # else { $zbscust['zbsc_secaddr_addr1'] = ''; }
			if(array_key_exists('saddr2', $posted_data)){$zbscust['zbsc_secaddr_addr2']   = $posted_data['saddr2']; } # else { $zbscust['zbsc_secaddr_addr2'] = ''; }
			if(array_key_exists('scity', $posted_data)){$zbscust['zbsc_secaddr_city']   = $posted_data['scity']; } # else { $zbscust['zbsc_secaddr_city'] = ''; }
			if(array_key_exists('scounty', $posted_data)){$zbscust['zbsc_secaddr_county']   = $posted_data['scounty']; } # else { $zbscust['zbsc_secaddr_county'] = ''; }
			if(array_key_exists('spostcode', $posted_data)){$zbscust['zbsc_secaddr_postcode']   = $posted_data['spostcode']; } # else { $zbscust['zbsc_secaddr_postcode'] = ''; }
			if(array_key_exists('scountry', $posted_data)){$zbscust['zbsc_secaddr_country']   = $posted_data['scountry']; } # else { $zbscust['zbsc_secaddr_country'] = ''; }
															
			if(array_key_exists('notes', $posted_data)){$zbscust['zbsc_notes']   = $posted_data['notes']; } # else { $zbscust['zbsc_notes'] = ''; }
			
			#} Reformat inc email/status etc.
			$customer_array = array(

		    	'zbsc_status' 				=> __('Donor','zerobscrm'),
		    	'zbsc_prefix' 				=> $zbscust['zbsc_prefix'],
		    	'zbsc_fname' 				=> $zbscust['zbsc_fname'], 			        
		    	'zbsc_lname' 				=> $zbscust['zbsc_lname'], 			        
		    	'zbsc_suffix' 				=> $zbscust['zbsc_suffix'],    		        
		    	'zbsc_email' 				=> $customerEmail,												        
			    'zbsc_hometel' 				=> $zbscust['zbsc_hometel'],  	
		        'zbsc_worktel' 				=> $zbscust['zbsc_worktel'],  
		        'zbsc_mobtel' 				=> $zbscust['zbsc_mobtel'],   

		        'zbsc_addr1' 				=> $zbscust['zbsc_addr1'], 
		        'zbsc_addr2' 				=> $zbscust['zbsc_addr2'], 
		        'zbsc_city' 				=> $zbscust['zbsc_city'], 
		        'zbsc_county' 				=> $zbscust['zbsc_county'], 
		        'zbsc_postcode' 			=> $zbscust['zbsc_postcode'], 
		        'zbsc_country' 				=> $zbscust['zbsc_country'], 

		        'zbsc_secaddr_addr1' 		=> $zbscust['zbsc_secaddr_addr1'], 
		        'zbsc_secaddr_addr2' 		=> $zbscust['zbsc_secaddr_addr2'], 
		        'zbsc_secaddr_city' 		=> $zbscust['zbsc_secaddr_city'], 
		        'zbsc_secaddr_county' 		=> $zbscust['zbsc_secaddr_county'], 
		        'zbsc_secaddr_postcode' 	=> $zbscust['zbsc_secaddr_postcode'], 
		        'zbsc_secaddr_country' 		=> $zbscust['zbsc_secaddr_country'], 

		        'zbsc_notes'				=> $zbscust['zbsc_notes']

	        );

			#} merge in any custom fields..
	        $update_args = array_merge($customer_array, $custom_fields_array); 

	        #} Fire addOrUpdate
	    	$custID = zeroBS_integrations_addOrUpdateCustomer('gwp',$customerEmail,$update_args);

	    	#} Add tag
			wp_set_post_terms( $custID, array('GiveWP'), 'zerobscrm_customertag' );

			#} ... then attach the donation transaction

				#} Build out vars
				$trans_title = __('Donation from ' . $payment_data['form_title'] . ' by ' . $payment_data['user_info']['first_name'], 'zerobscrm');
				$zbo = array();
				$zbo['orderid'] 	= $payment_data['purchase_key']; 
				$zbo['date'] 		= $payment_data['date'];
				$zbo['currency'] 	= $payment_data['currency'];
				$zbo['item'] 		= $trans_title;
				$zbo['total'] 		= $payment_data['price'];
				$zbo['customer'] 	= $custID;


				$zbsStatusStr = zeroBSCRM_getTransactionsStatuses();
				$zbsStatusArr = explode(",", $zbsStatusStr);

				//use the GIVE status here. Presume this updates when PayFast updates.
				$zbo['status'] 		= $payment_data['status'];
			
				$zbs_transaction = array(
							 'post_title' => $trans_title,
	                         'post_status' => 'publish',
	                         'post_type' => 'zerobs_transaction',
	                         'post_date' => $payment_data['date']
	                          );
				#} Log
	
			#} Fire if not already assigned
			$orderID = zeroBSCRM_giveWP_getPostID_byMeta('zbs_givewp_uid', $payment_data['purchase_key']);
			if(!$orderID && isset($custID) && !empty($custID)){
				$transactionID = wp_insert_post( $zbs_transaction );
				update_post_meta( $transactionID,'zbs_transaction_meta',$zbo);
				update_post_meta( $transactionID,'zbs_parent_cust', $custID);
				update_post_meta( $transactionID,'zbs_givewp_uid', $zbo['orderid']);
				wp_set_post_terms( $transactionID, array('GiveWP'), 'zerobscrm_transactiontag' );
			}

		} 

	}

	return $args;

}

function zeroBSCRM_giveWP_getPostID_byMeta($key, $value) {
	global $wpdb;
	$q = $wpdb->prepare("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='%s' AND meta_value='%s'",$key,$value);
	$meta = $wpdb->get_results($q);

	if (is_array($meta) && !empty($meta) && isset($meta[0])) {
		$meta = $meta[0];
	}		
	if (is_object($meta)) {
		return $meta->post_id;
	}
	else {
		return false;
	}
}