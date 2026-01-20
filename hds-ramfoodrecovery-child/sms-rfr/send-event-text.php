<?php
/*
 * Shaun Geisert - 04/05/2023.  PHP code to authenticate against/hit our bulk texting endpoint to send a custom text to all subscribers of the RFR program
*/
if (!defined('ABSPATH')) { // prevent direct access
    exit;
}
/*************************
            CONSTANTS
*************************/
define('SEND_EVENT_TEXT_FORM_ID', '17');

/*************************
            FUNCTIONS
*************************/
/*** Intercept Gravity Forms Event Submission | Text Alerts ***/
add_action('gform_after_submission_' . SEND_EVENT_TEXT_FORM_ID, 'post_event_msg_to_text_service', $priority = 10, $accepted_args = 2);
function post_event_msg_to_text_service($entry, $form) {
    // Authorization
    $auth_tkn = authorize_api_request();
    $msg = rgar( $entry, '18' );
    
    //log_sms('Event token ' . $auth_tkn);
    send_event_msg_to_text_service($auth_tkn, $msg);
}
function send_event_msg_to_text_service($auth_tkn, $msg) {
    if (empty($auth_tkn)) return false;
    //log_sms('Provided token ' . $auth_tkn);
    try {
        $url = API_URL . '/TextAll';
        $headers = array('Content-Type' => 'application/json', 'TwilioPublicKey' => TWILIO_PUBLIC_KEY, 'Authorization' => 'Bearer ' . $auth_tkn);
        //log_sms('headers ' . serialize($headers));
        $body = array('Message' => $msg, 'PhoneNumber' => '0', 'ProgramId' => PROGRAM_ID);
        //log_sms('body ' . serialize($body));
        $args = array('headers' => $headers, 'body' => wp_json_encode($body), 'method' => 'POST', 'timeout' => 3);
        $num_tries = 1;
        do {
            $response = wp_remote_post($url, $args);
            //log_sms('response ' . serialize($response));
            if (is_wp_error($response)) {
                // Log error
                log_sms('Event Submission - Error while trying to make API send request (code 848): ' . $response->get_error_message());
                return false;
            }
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code == 200) {
                // Log success
                log_sms('Event Submission - The following message was successfully passed along to API: ' . $msg );
                return true;
            }
            // Log error
            log_sms('Event Submission - API returned a non-200 status code: ' . $status_code . ' when trying send the following message: ' . $msg . '. Retrying in 4 seconds.');
            $num_tries++;
            sleep(4);
        } while ($num_tries <= 10);
        // Log error
        log_sms('Event Submission - API failed to return a 200 status code after 10 retries.');
        return false;
    }
    catch(Exception $e) {
        // Log error
        log_sms('Event Submission - Exception caught while trying to make API send request (code 848)');
        return false;
    }
}
