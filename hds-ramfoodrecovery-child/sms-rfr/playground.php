<?php
/*** Add Playground Page for Gravity Forms | Text Alerts ***/
add_action( 'admin_menu', 'addGFPlayground' );

function addGFPlayground() {
	add_menu_page( 'Gravity Forms Playground', 'GF Playground', 'manage_options', 'gf_playground.php', 'render_gf_playground', 'dashicons-lightbulb', 90 );
}

function render_gf_playground() {
	echo '<h1>Gravity Forms Playground</h1>';

	// Get subscribers entries
	echo '<h2>Subscribers</h2>';
	$form_id			= '16';
	$search_criteria	= array();
	$sorting			= array();
	$paging				= array( 'offset' => 0, 'page_size' => 500 );
	$total_count		= 0;
	$subs = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging, $total_count );

	echo '<h3>var_dump()</h3>';
	var_dump( $subs );

	var_dump($subsArr);
	echo '<h3>Parsed</h3>';
	echo '<table><thead><tr><th>First Name</th><th>Last Name</th><th>CSU ID</th><th>Phone Number</th><th>Email</th></tr></thead><tbody>';
	foreach($subs as $sub) {
		echo '<tr><td>' . $sub[1] . '</td><td>' . $sub[3] . '</td><td>' . $sub[4] . '</td><td>' . $sub[7] . '</td><td>' . $sub[6] . '</td></tr>';
	}
	echo '</tbody></table>';


	echo '<br><br><hr><br>';


	// Get events
	echo '<h2>Events</h2>';
	$form_id			= '17';
	$search_criteria	= array();
	$sorting			= array();
	$paging				= array( 'offset' => 0, 'page_size' => 500 );
	$total_count		= 0;
	$events = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging, $total_count );

	echo '<h3>var_dump()</h3>';
	var_dump( $events );

	echo '<h3>Parsed</h3>';
	echo '<table><thead><tr><th>Date</th><th>Time</th><th>Location</th><th>Additional Info</th><th>Full Message</th></tr></thead><tbody>';
	foreach($events as $event) {
		echo '<tr><td>' . $event[3] . '</td><td>' . $event[4] . '</td><td>' . $event[9] . '</td><td>' . $event[8] . '</td><td>' . $event[18] . '</td></tr>';
	}
	echo '</tbody></table>';


	echo '<br><br><hr><br>';


	// All Together Now
	echo '<h2>All Together Now</h2>';
	$token = '34LWIBS2FAKETOKEN310JQ2XTE6';
	foreach($events as $event) {
		echo '<h3>Event ID: ' . $event['id'] . '</h3>';
		foreach($subs as $sub) {
			echo '{ "phone": "' . preg_replace('/\D+/', '', $sub[7]) . '", "message": "' . $event[18] . '", "token": "' . $token . '" }';
		}
	}


	echo '<br><br><br><hr><br>';
}

