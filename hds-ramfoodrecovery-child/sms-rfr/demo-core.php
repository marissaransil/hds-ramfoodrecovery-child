<?php

/***********************************
        RFR CLASS (SINGLETON)
***********************************/
class RamFoodRecovery {

  // Hold the class instance
	private static $instance = null;

	// Private constructor to prevent initiation with outer code
	private function __construct() {}

	// The object is created only from within the class itself and if class has no instance
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Domain vars
	private static function isProd() {
		return ($_SERVER['HTTP_HOST'] == 'ramfoodrecovery.colostate.edu') ? true : false;
	}
	
	// API vars
	public static $programSID = '6ymXUTPfyNG6pBjd';
	public static $programKey = 'vUfprLLcKJ6MFBFuCntvLCsHB53xusCp';
	public static function getApiUrl() {
        return (self::isProd()) ? 'https://wsnetdev2.colostate.edu/cwis463/api' : 'https://wsnetdev2.colostate.edu/cwis332/api'; // cwis463 on prod, cwis332 on dev
    }
	
	// GF vars
	public static function getEventFormId() {
		return (self::isProd()) ? '20' : '4';
	}
	public static function getSubFormId() {
		return (self::isProd()) ? '19' : '3';
	}
	public static function getTrackingFormId() {
		return (self::isProd()) ? '21' : '5';
	}
	public static function getEventSubmissionHook() {
		return (self::isProd()) ? 'gform_after_submission_20' : 'gform_after_submission_4';
	}
	public static function getSubSubmissionHook() {
		return (self::isProd()) ? 'gform_after_submission_19' : 'gform_after_submission_3';
	}

	// API Auth
	function getAuthTkn() {

		// Base vars
		$auth_tkn = '';
		$auth_success = false;
		$fail_count = 0;
	
		// Keep attempting until authorization is successful
		while( !$auth_success && $fail_count < 50) {

			try {

				// Build request
				$auth_url = self::getApiUrl() . '/Login';
				$auth_body = array(
					'programSID' => self::$programSID,
					'ProgramKey' => self::$programKey
					);
				// Send request and get response
				$auth_response = wp_remote_post( $auth_url, array(
					'method' => 'POST',
					'body' => $auth_body,
					'timeout' => 3
					)
				);
	
				// If no error
				if( !is_wp_error( $auth_response ) ) {
					$auth_tkn = trim($auth_response['body'], '"'); // Set auth token (and strip wrapping quotes)
					$auth_success = true; // Flag as successful to exit while loop
				}
				// If error
				else {
					// If error was timeout
					if( $auth_response->get_error_code() == 'http_request_failed' ) {
						demo_log_sms('API authorization request timed out (code 824)');
					}
					// If error was anything else
					else {
						demo_log_sms('API authorization request errored but did was not timed out (code 835)');
					}	
				}
	
			} catch( Exception $e ) {

				demo_log_sms('Exception caught while trying to authorize API request (code 848)');

			}

			$fail_count++;

		} // End while

		// Return token
		return $auth_tkn;

	} // End getAuthTkn()

	// Subscribe (un-blacklist) Function
	public static function subscribe( $number ) {

		// Get auth_tkn
		$auth_tkn = self::getAuthTkn();

		try {

			// Request params
			$sub_url = self::getApiUrl() . '/Subscribers';
			$sub_headers = array(
				'Content-type' => 'application/json',
				'Authorization' => $auth_tkn
			);
			$sub_body = array(
				'PhoneNumber' => $number,
				'DoNotText' => false
			);
	
			// Request post
			$sub_response = wp_remote_post( $sub_url, array(
				'method' => 'POST',
				'data-format' => 'body',
				'headers' => $sub_headers,
				'body' => json_encode($sub_body)
				)
			);
	
		} catch( Exception $e ) {
	
			// Log error
			demo_log_sms('SMS Subscription - Exception caught while trying to mark as subscriber (code 1081)');
	
		}

	}

    // Send SMS Function
    public static function sendSMS( $numbers, $msg ) {

		// Get auth_tkn
		$auth_tkn = self::getAuthTkn();

		try {

			// Request params
			$sms_url = self::getApiUrl() . '/TextMessages';
			$sms_headers = array(
				'Content-type' => 'application/json',
				'Authorization' => $auth_tkn
			);
			// Request body
			$sms_body = array(
				'PhoneNumber' => $numbers,
				'Message' => $msg,
				'DateSent' => current_time('mysql')
			);
			// Request post
			$sms_response = wp_remote_post( $sms_url, array(
				'method' => 'POST',
				'data-format' => 'body',
				'headers' => $sms_headers,
				'body' => json_encode($sms_body)
				)
			);

		} catch( Exception $e ) {

			demo_log_sms('SMS Subscription - Exception caught while trying to send SMS (code 1119)');

		}

	} // End SendSMS()

}



/***********************************
          EVENT SUBMISSION
***********************************/
add_action( RamFoodRecovery::getEventSubmissionHook(), 'demo_post_to_text_service', $priority = 10, $accepted_args = 2 );
function demo_post_to_text_service( $entry, $form ) {

	// Get subscribers
	$form_id			= RamFoodRecovery::getSubFormId();
	$search_criteria	= array();
	$sorting			= array();
	$paging				= array( 'offset' => 0, 'page_size' => 10000 );
	$total_count		= 0;
	$subs = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging, $total_count );

	// Var to store array of numbers
	$numbersArr = [];
 
	// Loop through subscribers
	foreach( $subs as $sub ) {

		// If entry NOT in trash
		if( $sub['status'] != 'trash' ) {
		
			// (Un)Format phone number
			$phone = '1' . preg_replace('/\D+/', '', $sub[7]);

			// Append to array
			array_push( $numbersArr, $phone );

		}

	}

	// If there were any numbers found
	if( $numbersArr ) {

		// Send SMS
		RamFoodRecovery::sendSMS( implode(',', $numbersArr), rgar($entry, '18') );

	}

}



/***********************************
      SUBSCRIPTION SUBMISSION
***********************************/
add_action( RamFoodRecovery::getSubSubmissionHook(), 'demo_post_to_text_subscribe_service', $priority = 10, $accepted_args = 2 );
function demo_post_to_text_subscribe_service( $entry, $form ) {

  // (Un)Format phone number
	$phone = '1' . preg_replace( '/\D+/', '', rgar($entry, '7') ); // Strip phone number formatting, prepend "1"

  // Remove any potential duplicates already in the system
	try {

		// Get duplicates (based on [formatted] phone number)
		$form_id			= RamFoodRecovery::getSubFormId();
		$search_criteria['field_filters'][] = array( 'key' => '7', 'value' => rgar($entry, '7'));
		$sorting			= array();
		$paging				= array( 'offset' => 0, 'page_size' => 10000 );
		$total_count		= 0;
		$dups 				= GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging, $total_count );

		// Loop through duplicates
		foreach( $dups as $dup ) {

			// If not current entry
			if($dup['id'] != $entry['id']) {
 
				// Delete the duplicate entry
				GFAPI::delete_entry($dup['id']);

			}
		}

	} catch( Exception $e ) {

		// Log error
		demo_log_sms('SMS Subscription - Exception caught while trying to remove duplicates (code 1045)');

	}

  // Mark as subscriber
	RamFoodRecovery::subscribe( $phone );

	// Send SMS
	RamFoodRecovery::sendSMS( $phone, 'Thank you for subscribing for Ram Food Recovery text notifications. Reply STOP at any time to unsubscribe.' );
	
}



/***********************************
         MODIFY FORM OPTIONS
***********************************/
// Add all needed filters
add_filter( 'gform_pre_render_'. RamFoodRecovery::getTrackingFormId() , 'demo_populate_posts' );
add_filter( 'gform_pre_validation_'. RamFoodRecovery::getTrackingFormId() , 'demo_populate_posts' );
add_filter( 'gform_pre_submission_filter_'. RamFoodRecovery::getTrackingFormId() , 'demo_populate_posts' );
add_filter( 'gform_admin_pre_render_'. RamFoodRecovery::getTrackingFormId() , 'demo_populate_posts' );

// Populate field with RFR events
function demo_populate_posts( $form ) {
 
    foreach ( $form['fields'] as &$field ) {
 
        if ( $field->type != 'select' || strpos( $field->cssClass, 'populate-posts' ) === false ) {
            continue;
        }
 
        // Get entries of events
        $events = GFAPI::get_entries( RamFoodRecovery::getEventFormId() );
 
        $choices = array();
 
        foreach ( $events as $event ) {
            $choices[] = array( 'text' => $event[23], 'value' => $event[23] );
        }
 
        // Update 'Select a Post'
        $field->placeholder = 'Select an Event';
        $field->choices = $choices;
 
    }
 
	return $form;
	
}



/***********************************
		   HELPER: LOGGING
***********************************/
function demo_log_sms( $msg ) {

	$log_file = get_stylesheet_directory() . '/zoinks.txt';
	date_default_timezone_set('America/Denver');

	$log = fopen( $log_file, 'a' ) or die ('fopen failed');
	$log_string = "\n" . date("Y-m-d H:i:s") . " :: " . $msg;
	fwrite( $log, $log_string ) or die ('fwrite failed');
	fclose( $log );

}