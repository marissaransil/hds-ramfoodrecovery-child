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
define('SEND_SUBSCRIBER_TEXT_FORM_ID', '16');

/*************************
            FUNCTIONS
*************************/
/*** Intercept Gravity Forms Add Subscriber | Text Alerts ***/
add_action('gform_after_submission_' . SEND_SUBSCRIBER_TEXT_FORM_ID, 'post_subscriber_msg_to_text_service', $priority = 10, $accepted_args = 2);
function post_subscriber_msg_to_text_service($entry, $form) {
    // Authorization
    $auth_tkn = authorize_api_request();
    $phone = '1' . preg_replace( '/\D+/', '', rgar($entry, '13') ); // Strip phone number formatting, prepend "1"
    $msg = 'Thank you for subscribing to Ram Food Recovery text notifications. Reply STOP at any time to unsubscribe.';
    
    log_sms('Event token ' . $auth_tkn);
    send_subscriber_msg_to_text_service($auth_tkn, $phone, $msg);
}

function send_subscriber_msg_to_text_service($auth_tkn, $phone, $msg) {
    if (empty($auth_tkn)) return false;
    //log_sms('Provided token ' . $auth_tkn);
    try {
        $url = API_URL . '/TextMessages';
        $headers = array('Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $auth_tkn);
        log_sms('headers ' . serialize($headers));
        $body = array('Message' => $msg, 'PhoneNumber' => $phone, 'ProgramId' => PROGRAM_ID);
        log_sms('body ' . serialize($body));
        $args = array('headers' => $headers, 'body' => wp_json_encode($body), 'method' => 'POST', 'timeout' => 3);
        do {
            $response = wp_remote_post($url, $args);
            //log_sms('response ' . serialize($response));
            if (is_wp_error($response)) {
                // Log error
                log_sms('Add Subscriber - Error while trying to make API send request (code 848): ' . $response->get_error_message());
                return false;
            }
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code == 200) {
                // Log success
                log_sms('Add Subscriber  - The following message was successfully passed along to API: ' . $msg );
                return true;
            }
            // Log error
            log_sms('Add Subscriber  - API returned a non-200 status code: ' . $status_code . ' when trying send the following message: ' . $msg . '. Retrying in 4 seconds.');
            $num_tries++;
            sleep(4);
        } while ($num_tries <= 10);
        // Log error
        log_sms('Add Subscriber  - API failed to return a 200 status code after 10 retries.');
        return false;
    }
    catch(Exception $e) {
        // Log error
        log_sms('Add Subscriber  - Exception caught while trying to make API send request (code 848)');
        return false;
    }
}

