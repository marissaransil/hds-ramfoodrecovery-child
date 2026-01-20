<?php
/*
 * Shaun Geisert - 04/05/2023.  PHP code to authenticate against/hit our bulk texting endpoint to send a custom text to all subscribers of the RFR program
*/

/*************************
            CONSTANTS FOR TEXTING
*************************/
define('PROGRAM_SID', '6ymXUTPfyNG6pBjd');
define('PROGRAM_KEY', 'vUfprLLcKJ6MFBFuCntvLCsHB53xusCp');
define('API_URL', 'https://wsprod.colostate.edu/cwis463/smshub/api');     // PROD
//define('API_URL', 'https://wsprod.colostate.edu/cwis199/assessments/api');  // TEST
define('TWILIO_PUBLIC_KEY', 'ACc02306268a2709354ec64dfac78cd7e2');
define('PROGRAM_ID', '1');

function authorize_api_request() {
    $auth_tkn = '';
    // Keep attempting authorization until successful
    for ($i = 0; $i < 20; $i++) {
        try {
            // Build request
            $auth_url = API_URL . '/Login';
            $auth_body = array('ProgramSID' => PROGRAM_SID, 'ProgramKey' => PROGRAM_KEY);
            // Send request and get response
            $auth_response = wp_remote_post($auth_url, array('content-type' => 'application/json', 'method' => 'POST', 'body' => $auth_body, 'timeout' => 10));
            // If no error
            if (!is_wp_error($auth_response)) {
                // Set auth token (and strip wrapping quotes)
                $auth_tkn = trim($auth_response['body'], '"');
                break; // Exit loop if successful
                
            } else {
                // If error
                $error_code = $auth_response->get_error_code();
                if ($error_code === 'http_request_failed') {
                    // Log error
                    log_sms('Event Submission - API authorization request timed out (code 824)', true);
                } else {
                    // Log error
                    log_sms('Event Submission - API authorization request errored but did not time out (code 835)', true);
                }
            }
        }
        catch(Exception $e) {
            // Log error
            log_sms('Event Submission - Exception caught while trying to authorize API request (code 848): ' . $e->getMessage(), true);
        }
    }
    return $auth_tkn;
}

/*** Helper Function to Write To Custom SMS Log and Send Email ***/
function log_sms($msg, $send_email = false) {
    $log_file = get_stylesheet_directory() . '/sms-logs.txt';
    date_default_timezone_set('America/Denver');

    $log = fopen($log_file, 'a') or die ('fopen failed');
    $log_string = "\n" . date("Y-m-d H:i:s") . " :: " . $msg;
    fwrite($log, $log_string) or die ('fwrite failed');
    fclose($log);

    if ($send_email) {
        $to = 'shaun.geisert@colostate.edu';
        $subject = 'Error in Bulk Texting Application';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $msg, $headers);
    }
}
