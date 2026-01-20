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
define('SEND_CUSTOM_TEXT_FORM_ID', '22');

/*************************
            FUNCTIONS
*************************/
/*** Intercept Gravity Forms Custom Message Submission | Text Alerts ***/
add_action('gform_after_submission_' . SEND_CUSTOM_TEXT_FORM_ID, 'post_custom_msg_to_text_service', $priority = 10, $accepted_args = 2);
function post_custom_msg_to_text_service($entry, $form) {
    // Authorization
    $auth_tkn = authorize_api_request();
    $msg = rgar($entry, '2');
    //log_sms('My token ' . $auth_tkn);
    send_custom_msg_to_text_service($auth_tkn, $msg);
}

function send_custom_msg_to_text_service($auth_tkn, $msg) {
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
                log_sms('Custom Message Submission - Error while trying to make API send request (code 848): ' . $response->get_error_message(), true);
                return false;
            }
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code == 200) {
                // Log success
                log_sms('Custom Message Submission - The following message was successfully passed along to API: ' . $msg );
                return true;
            }
            // Log error
            log_sms('Custom Message Submission - API returned a non-200 status code: ' . $status_code . ' when trying send the following message: ' . $msg . '. Retrying in 4 seconds.', true);
            $num_tries++;
            sleep(4);
        } while ($num_tries <= 10);
        // Log error
        log_sms('Custom Message Submission - API failed to return a 200 status code after 10 retries.');
        return false;
    }
    catch(Exception $e) {
        // Log error
        log_sms('Custom Message Submission - Exception caught while trying to make API send request (code 848)', true);
        return false;
    }
}

function send_custom_msg_to_text_service_curl($auth_tkn, $msg) {
    if (empty($auth_tkn)) return false;
    //log_sms('Provided token ' . $auth_tkn);
    try {
        $url = API_URL . '/TextAll';
        $headers = array('Content-Type: application/json', 'TwilioPublicKey: ' . TWILIO_PUBLIC_KEY, 'Authorization: Bearer ' . $auth_tkn);
        log_sms('headers ' . serialize($headers));
        $body = array('Message' => $msg, 'PhoneNumber' => '0', 'ProgramId' => PROGRAM_ID);
        log_sms('body ' . serialize($body));
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10
        );
        $num_tries = 1;
        do {
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            curl_close($ch);
            log_sms('response ' . $response);
            if (!$response) {
                // Log error
                log_sms('Custom Message Submission - Error while trying to make API send request (code 848): ' . curl_error($ch));
                return false;
            }
            $status_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if ($status_code == 200) {
                // Log success
                log_sms('Custom Message Submission - The following message was successfully passed along to API: ' . $msg );
                return true;
            }
            // Log error
            log_sms('Custom Message Submission - API returned a non-200 status code: ' . $status_code . ' when trying send the following message: ' . $msg . '. Retrying in 4 seconds.');
            $num_tries++;
            sleep(4);
        } while ($num_tries <= 20);
        // Log error
        log_sms('Custom Message Submission - API failed to return a 200 status code after 20 retries.');
        return false;
    }
    catch(Exception $e) {
        // Log error
        log_sms('Custom Message Submission - Exception caught while trying to make API send request (code 848)', true);
        return false;
    }
}