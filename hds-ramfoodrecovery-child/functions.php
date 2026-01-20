<?php
/*** 
IMPORTANT NOTES:  DO NOT ENABLE WP-ROCKET, COMBINE, CSS UNTIL YOU RESOLVE VISUAL ISSUES


If you upgrade the parent theme, hds, then you will need to add the following code, as it will get overwritten

@ /hds/extendvc/extend-vc.php, comment out this line 35:

//vc_remove_element("vc_btn");

***/

/***  Temporarily disable page caching, as per http://docs.wp-rocket.me/article/61-disable-page-caching ***/
//add_filter( 'do_rocket_generate_caching_files', '__return_false' );


function do_not_cache_feeds(&$feed) {
   $feed->enable_cache(false);
}
add_action( 'wp_feed_options', 'do_not_cache_feeds' );


/* Use jQuery CDN 
function modify_jquery() {
	if (!is_admin()) {
		// comment out the next two lines to load the local copy of jQuery
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js', false, '3.6.4');
		wp_enqueue_script('jquery');
	}
}
add_action('init', 'modify_jquery');*/


/***  Enqueue Elated child theme stylesheet ***/
function elated_child_enqueue_style() {
		
	/* Add Proxima Nova CSS */
	//wp_enqueue_style('elated_child_enqueue_style', '//static.colostate.edu/fonts/proxima-nova/proxima.css');
	
	/* Add child style */
	wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css'  );
	wp_enqueue_style( 'childstyle' );
}
add_action( 'wp_enqueue_scripts', 'elated_child_enqueue_style', 11);



/***  Change login appearance ***/
function my_login_stylesheet() {    
	wp_enqueue_style( 'custom-login', '//housing.colostate.edu/shared/css/style-login.css' );
	//wp_enqueue_script( 'custom-login', '//hdsstaff.colostate.edu/shared/js/style-login.js', array( 'jquery' ), '1.0', false );
    
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );



/***  Support Google translate ***/
function add_google_translate() {
	wp_register_script('google_translate', '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit');
	wp_enqueue_script('google_translate');
}
add_action( 'wp_enqueue_scripts', 'add_google_translate' );  


/***  CSU logo ***/
function add_responsive_logo() {
	wp_register_script('responsive_logo', '//static.colostate.edu/logo/reslogo/logo.min.js');
	wp_enqueue_script('responsive_logo', array( 'jquery' ), '1.0', false );
}
add_action( 'wp_enqueue_scripts', 'add_responsive_logo', 100 );  


/**
	 * Function that includes theme's core styles
	 */
	function eltd_styles() {
		global $eltd_options;
		global $eltd_toolbar;
        global $eltd_landing;
		global $eltdIconCollections;

		//init variables
		$responsiveness = 'yes';
		$vertical_area 	= "no";
		$vertical_area_hidden = '';

		wp_register_style("eltd_blog", ELTD_ROOT . "/css/blog.min.css");

		//include theme's core styles
		wp_enqueue_style("eltd_default_style", ELTD_ROOT . "/style.css");		
		wp_enqueue_style("eltd_stylesheet", ELTD_ROOT . "/css/stylesheet.min.css");

		if(eltd_load_blog_assets()) {
			wp_enqueue_style('eltd_blog');
		}
		
		//define files afer which style dynamic needs to be included. It should be included last so it can override other files
		$style_dynamic_deps_array = array();
		if(eltd_load_woo_assets()) {
			$style_dynamic_deps_array = array('eltd_woocommerce', 'eltd_woocommerce_responsive');
		}

		if (file_exists(dirname(__FILE__) ."/css/style_dynamic.css") && eltd_is_css_folder_writable() && !is_multisite()) {
			wp_enqueue_style("eltd_style_dynamic", ELTD_ROOT . "/css/style_dynamic.css", $style_dynamic_deps_array, filemtime(dirname(__FILE__) ."/css/style_dynamic.css")); //it must be included after woocommerce styles so it can override it
		} else {
			//wp_enqueue_style("eltd_style_dynamic", ELTD_ROOT . "/css/style_dynamic.php", $style_dynamic_deps_array); //it must be included after woocommerce styles so it can override it
			wp_enqueue_style("eltd_style_dynamic", get_stylesheet_directory_uri() . "/css/style_dynamic.css", $style_dynamic_deps_array, filemtime(dirname(__FILE__) ."/css/style_dynamic.css"));
		}

		//include icon collections styles
		if(is_array($eltdIconCollections->iconCollections) && count($eltdIconCollections->iconCollections)) {
			foreach ($eltdIconCollections->iconCollections as $collection_key => $collection_obj) {
				wp_enqueue_style('eltd_'.$collection_key, $collection_obj->styleUrl);
			}
		}

		//does responsive option exists?
		if (isset($eltd_options['responsiveness'])) {

			$responsiveness = $eltd_options['responsiveness'];
		}

		//is responsive option turned on?
		if ($responsiveness != "no") {
			//include proper styles
			wp_enqueue_style("eltd_responsive", ELTD_ROOT . "/css/responsive.min.css");

            if (file_exists(dirname(__FILE__) ."/css/style_dynamic_responsive.css") && eltd_is_css_folder_writable() && !is_multisite()){
                wp_enqueue_style("eltd_style_dynamic_responsive", ELTD_ROOT . "/css/style_dynamic_responsive.css", array(), filemtime(dirname(__FILE__) ."/css/style_dynamic_responsive.css"));
            } else {
                //wp_enqueue_style("eltd_style_dynamic_responsive", ELTD_ROOT . "/css/style_dynamic_responsive.php");
				wp_enqueue_style("eltd_style_dynamic_responsive", get_stylesheet_directory_uri() . "/css/style_dynamic_responsive.css", array(), filemtime(dirname(__FILE__) ."/css/style_dynamic_responsive.css"));
            }
		}

		//does left menu option exists?
		if (isset($eltd_options['vertical_area'])){
			$vertical_area = $eltd_options['vertical_area'];
		}
		
		//is hidden menu enabled?
		if (isset($eltd_options['vertical_area_type'])){
			$vertical_area_hidden = $eltd_options['vertical_area_type'];
		}

		//is left menu activated and is responsive turned on?
		if($vertical_area == "yes" && $responsiveness != "no" && $vertical_area_hidden!='hidden'){
			//wp_enqueue_style("eltd_vertical_responsive", ELTD_ROOT . "/css/vertical_responsive.min.css");
		}

        //is landing turned on?
        if (isset($eltd_landing)) {
            //include toolbar specific styles
            wp_enqueue_style("eltd_landing_fancybox", get_home_url() . "/demo-files/landing/css/jquery.fancybox.css");
            wp_enqueue_style("eltd_landing", get_home_url() . "/demo-files/landing/css/landing_stylesheet.css");

        }

		//include Visual Composer styles
		if (class_exists('WPBakeryVisualComposerAbstract')) {
			//wp_enqueue_style( 'js_composer_front' );
		}

        if (file_exists(dirname(__FILE__) ."/css/custom_css.css") && eltd_is_css_folder_writable() && !is_multisite()){
            wp_enqueue_style("eltd_custom_css", ELTD_ROOT . "/css/custom_css.css", array(), filemtime(dirname(__FILE__) ."/css/custom_css.css"));
        } else {
            //wp_enqueue_style("eltd_custom_css", ELTD_ROOT . "/css/custom_css.php");
        }
	}

	add_action('wp_enqueue_scripts', 'eltd_styles');
	
	
	/**
	 * Function that includes all necessary scripts
	 */
	function eltd_scripts() {
		global $eltd_options;
		global $eltd_toolbar;
        global $eltd_landing;
		global $wp_scripts;

		//init variables
		$smooth_scroll 	= true;
		$has_ajax 		= false;
		$eltd_animation = "";

		//is smooth scroll option turned on?
		if(isset($eltd_options['smooth_scroll']) && $eltd_options['smooth_scroll'] == "no"){
			$smooth_scroll = false;
		}

		//init theme core scripts
		wp_enqueue_script("jquery");
		wp_enqueue_script("eltd_plugins", ELTD_ROOT."/js/plugins.js",array(),false,true);
		//wp_enqueue_script("carouFredSel", ELTD_ROOT."/js/jquery.carouFredSel-6.2.1.js",array(),false,true);
		//wp_enqueue_script("one_page_scroll", ELTD_ROOT."/js/jquery.fullPage.min.js",array(),false,true);
		wp_enqueue_script("lemmonSlider", ELTD_ROOT."/js/lemmon-slider.js",array(),false,true);
		//wp_enqueue_script("mousewheel", ELTD_ROOT."/js/jquery.mousewheel.min.js",array(),false,true);
		wp_enqueue_script("touchSwipe", ELTD_ROOT."/js/jquery.touchSwipe.min.js",array(),false,true);
		//wp_enqueue_script("isotope", ELTD_ROOT."/js/jquery.isotope.min.js",array(),false,true);

	   //include google map api script
        if (isset($eltd_options['google_maps_api_key']) && ($eltd_options['google_maps_api_key'] != "")) {
            $google_maps_api_key = $eltd_options['google_maps_api_key'];
            //wp_enqueue_script("google_map_api", "https://maps.googleapis.com/maps/api/js?key=" . $google_maps_api_key,array(),false,true);
        }
        else {
            //wp_enqueue_script("google_map_api", "https://maps.googleapis.com/maps/api/js", array(), false, true);
        }

        if (file_exists(dirname(__FILE__) ."/js/default_dynamic.js") && eltd_is_js_folder_writable() && !is_multisite()) {
            wp_enqueue_script("eltd_default_dynamic", ELTD_ROOT."/js/default_dynamic.js",array(), filemtime(dirname(__FILE__) ."/js/default_dynamic.js"),true);
        } else {
            //wp_enqueue_script("eltd_default_dynamic", ELTD_ROOT."/js/default_dynamic.php", array(), false, true);
			// load other js from static js and not PHP, since for some reason this is terribly slow
			wp_enqueue_script("defaultdynamic", get_stylesheet_directory_uri() . "/js/default_dynamic.js", array(), false, true );
        }

        wp_enqueue_script("eltd_default", ELTD_ROOT."/js/default.min.js", array(), false, true);

		if(eltd_load_blog_assets()) {
			//wp_enqueue_script('eltd_blog', ELTD_ROOT."/js/blog.min.js", array(), false, true);
		}

        if (file_exists(dirname(__FILE__) ."/js/custom_js.js") && eltd_is_js_folder_writable() && !is_multisite()) {
            wp_enqueue_script("eltd_custom_js", ELTD_ROOT."/js/custom_js.js",array(), filemtime(dirname(__FILE__) ."/js/custom_js.js"),true);
        } else {
            //wp_enqueue_script("eltd_custom_js", ELTD_ROOT."/js/custom_js.php", array(), false, true);
        }

        //is smooth scroll enabled enabled and not Mac device?
        $mac_os = strpos($_SERVER['HTTP_USER_AGENT'], "Macintosh; Intel Mac OS X");
        if($smooth_scroll && $mac_os == false){
            wp_enqueue_script("TweenLite", ELTD_ROOT."/js/TweenLite.min.js",array(),false,true);
            wp_enqueue_script("ScrollToPlugin", ELTD_ROOT."/js/ScrollToPlugin.min.js",array(),false,true);
            wp_enqueue_script("smoothPageScroll", ELTD_ROOT."/js/smoothPageScroll.js",array(),false,true);
        }

		//include comment reply script
		$wp_scripts->add_data('comment-reply', 'group', 1 );
		if (is_singular()) {
			wp_enqueue_script( "comment-reply");
		}

		//is ajax set in session?
		if (isset($_SESSION['eltd_borderland_page_transitions'])) {
			$eltd_animation = $_SESSION['eltd_borderland_page_transitions'];
		}
		if (($eltd_options['page_transitions'] != "0") && (empty($eltd_animation) || ($eltd_animation != "no"))) {
			$has_ajax = true;
		} elseif (!empty($eltd_animation) && ($eltd_animation != "no"))
			$has_ajax = true;

		if ($has_ajax) {
			wp_enqueue_script("ajax", ELTD_ROOT."/js/ajax.min.js",array(),false,true);
		}

		//include Visual Composer script
		if (class_exists('WPBakeryVisualComposerAbstract')) {
			//wp_enqueue_script( 'wpb_composer_front_js' );
		}

        //is landing enabled?
        if(isset($eltd_landing)) {
            wp_enqueue_script("eltd_landing_fancybox", get_home_url() . "/demo-files/landing/js/jquery.fancybox.js",array(),false,true);
			wp_enqueue_script("eltd_mixitup", get_home_url() . "/demo-files/landing/js/jquery.mixitup.min.js",array(),false,true);
            wp_enqueue_script("eltd_landing", get_home_url() . "/demo-files/landing/js/landing_default.js",array(),false,true);
        }

	}

	add_action('wp_enqueue_scripts', 'eltd_scripts');
	

/*** dequeue google maps other unused styles and scripts ***/
function remove_unused()
{
	// scripts
	//wp_dequeue_script('google_map_api');
	wp_dequeue_script('jquery');
	
	
	// styles
    wp_dequeue_style("eltd_google_fonts_styles");
	wp_deregister_style("eltd_google_fonts_styles");
	
	wp_dequeue_style("eltd_woocommerce");
	wp_deregister_style("eltd_woocommerce");
	
	wp_dequeue_style("eltd_woocommerce_responsive");
	wp_deregister_style("eltd_woocommerce_responsive");
	
	wp_dequeue_style("eltd_woocommerce_responsive");
	wp_deregister_style("eltd_woocommerce_responsive");
	
	// js composer pretty
	wp_dequeue_script('prettyphoto');
  	wp_deregister_script('prettyphoto');
  	wp_dequeue_style('prettyphoto');
  	wp_deregister_style('prettyphoto');

	//wp_dequeue_script("eltd_default_dynamic");
	//wp_deregister_script("eltd_default_dynamic");
}
add_action('wp_enqueue_scripts', 'remove_unused', 9999);





/*** dequeue parent default.js, enqueue our override js file ***/
/*add_action('wp_enqueue_scripts', 'override_parent_script', 100);
function override_parent_script()
{
    wp_dequeue_script('eltd_default');
    wp_enqueue_script('child_theme_script_handle', get_stylesheet_directory_uri().'/js/default.js', array('jquery'));
}*/

/***  disable change e-mail notification to end user ***/
add_filter( 'send_email_change_email', '__return_false' );




/***  reference our custom.js file ***/
function theme_js() {
    wp_enqueue_script( 'theme_js', get_stylesheet_directory_uri() . '/custom.js', array( 'jquery' ), '1.0', false );
	
	// for /interactive-green-room that doesn't work.  TODO:  Shouldn't have to do this - don't get it.
	if ( is_page(8877) ) {
	  wp_enqueue_script('tooltipster', '/wp-content/plugins/vc-extensions-bundle/profilecard/js/jquery.tooltipster.min.js', array( 'jquery' ) );  
	  wp_enqueue_style( 'tooltipsterCSS', '/wp-content/plugins/vc-extensions-bundle/profilecard/css/tooltipster.css' );
	}
}
add_action('wp_enqueue_scripts', 'theme_js');

// Enable accessibility scripts file
function customJS_enqueue_scripts() {
	wp_enqueue_script('customJS_script', get_stylesheet_directory_uri() .'/accessibility.js', array(), '1.0', true);
	$script  = '!function(e){"use strict";var t,n=[],r=e.document,o=e.MutationObserver||e.WebKitMutationObserver;function c(){for(var e,t,o=0,c=n.length;o<c;o++){e=n[o];for(var l,u=0,i=(t=r.querySelectorAll(e.selector)).length;u<i;u++)(l=t[u]).enterDom||(l.enterDom=!0,e.fn.call(l,l))}}e.enterDom=function(e,l){n.push({selector:e,fn:l}),t||(t=new o(c)).observe(r.documentElement,{childList:!0,subtree:!0}),c()}}(window);';
	wp_add_inline_script('customJS_script', $script, 'before');
}
add_action('wp_enqueue_scripts', 'customJS_enqueue_scripts');



// stop wp removing div and br tags, as per https://ikreativ.com/stop-wordpress-removing-html/
function tinymce_fix( $init )
{
    // html elements being stripped
    $init['extended_valid_elements'] = 'div[*],article[*]';

    // don't remove line breaks
    $init['remove_linebreaks'] = false;

    // convert newline characters to BR
    $init['convert_newlines_to_brs'] = true;

    // don't remove redundant BR
    $init['remove_redundant_brs'] = false;

    // pass back to wordpress
    return $init;
}
add_filter('tiny_mce_before_init', 'tinymce_fix');


// don't want certain plugins enabled
function deactivate_plugin_conditional() {
    $cornerstone = 'cornerstone/cornerstone.php';
    deactivate_plugins($cornerstone, false, true);
	
	$elegantbuilder = 'elegantbuilder/et-layout-builder.php';
    deactivate_plugins($elegantbuilder, false, true);
	
	$elegantthemesupdater = 'elegant-themes-updater/elegant-themes-updater.php';
    deactivate_plugins($elegantthemesupdater, false, true);
	
	$elegantthemesupdater = 'elegant-themes-updater/elegant-themes-updater.php';
    deactivate_plugins($elegantthemesupdater, false, true);
	
	$revslider = 'revslider/revslider.php';
    deactivate_plugins($revslider, false, true);
	
	$siteorigin = 'siteorigin-panels/siteorigin-panels.php';
    deactivate_plugins($siteorigin, false, true);
	
	$siteoriginwidgets = 'so-widgets-bundle/so-widgets-bundle.php';
    deactivate_plugins($siteoriginwidgets, false, true);
	
	//$wprocket = 'wp-rocket/wp-rocket.php';
    //deactivate_plugins($wprocket, false, true);
	
}
add_action( 'init', 'deactivate_plugin_conditional' );

/* https://www.relevanssi.com/knowledge-base/excluding-protected-posts/ */
add_filter('relevanssi_do_not_index', 'rlv_exclude_protected', 10, 2);
function rlv_exclude_protected($exclude, $post_id) {
	$post = get_post($post_id);
	if (!empty($post->post_password)) $exclude = true;
	return $exclude;
}


/*
add_filter( 'vc_grid_item_shortcodes', 'my_module_add_grid_shortcodes' );
function my_module_add_grid_shortcodes( $shortcodes ) {
   $shortcodes['vc_post_id'] = array(
     'name' => __( 'Featured Image Or Text', 'my-text-domain' ),
     'base' => 'vc_post_id',
     'category' => __( 'Content', 'my-text-domain' ),
     'description' => __( 'Show current post id', 'my-text-domain' ),
     'post_type' => Vc_Grid_Item_Editor::postType(),
  );
 
   return $shortcodes;
}
 
add_shortcode( 'vc_post_id', 'vc_post_id_render' );
function vc_post_id_render() {
   return '<h2>{{ post_data:ID }} </h2>'; // usage of template variable post_data with argument "ID"
}*/


// provide gravity forms hook to populate buildings in forms that need this, as per https://www.gravityhelp.com/documentation/article/dynamically-populating-drop-down-fields/
	add_filter( 'gform_pre_render_2', 'populate_allhallsapts' );
	add_filter( 'gform_pre_validation_2', 'populate_allhallsapts' );
	add_filter( 'gform_pre_submission_filter_2', 'populate_allhallsapts' );
	add_filter( 'gform_admin_pre_render_2', 'populate_allhallsapts' );
	function populate_allhallsapts( $form ) {
	
		foreach ( $form['fields'] as &$field ) {
	
			if ( $field->type != 'select' || strpos( $field->cssClass, 'populatehallsapts' ) === false ) {
				continue;
			}
	
			// manually define buildings
			$choices = array();
			$choices[] = array("text" => "Academic Village - Aspen", "value" => "Academic Village - Aspen");
			$choices[] = array("text" => "Academic Village - Engineering", "value" => "Academic Village - Engineering");
			$choices[] = array("text" => "Academic Village - Honors", "value" => "Academic Village - Honors");
			$choices[] = array("text" => "Aggie North – Cottonwood", "value" => "Aggie North – Cottonwood");
			$choices[] = array("text" => "Aggie North – Lodgepole", "value" => "Aggie North – Lodgepole");
			$choices[] = array("text" => "Aggie North – Walnut", "value" => "Aggie North – Walnut");
			$choices[] = array("text" => "Allison", "value" => "Allison");
			$choices[] = array("text" => "Braiden", "value" => "Braiden");
			$choices[] = array("text" => "Corbett", "value" => "Corbett");
			//$choices[] = array("text" => "Durrell", "value" => "Durrell");
			$choices[] = array("text" => "Durward", "value" => "Durward");
			$choices[] = array("text" => "Edwards", "value" => "Edwards");
			$choices[] = array("text" => "Ingersoll", "value" => "Ingersoll");
			$choices[] = array("text" => "International House", "value" => "International House");
			$choices[] = array("text" => "Laurel Village - Alpine", "value" => "Laurel Village - Alpine");
			$choices[] = array("text" => "Laurel Village - Piñon", "value" => "Laurel Village - Piñon");
			$choices[] = array("text" => "Newsom", "value" => "Newsom");
			$choices[] = array("text" => "Parmelee", "value" => "Parmelee");
			$choices[] = array("text" => "South Aggie Village", "value" => "South Aggie Village");
			$choices[] = array("text" => "Summit", "value" => "Summit");
			$choices[] = array("text" => "University Village 1700", "value" => "University Village 1700");
			$choices[] = array("text" => "Westfall", "value" => "Westfall");
			
			// update 'Select a Building' to whatever you'd like the instructive option to be
			$field->placeholder = 'Select a Building';
			$field->choices = $choices;
	
		}
	
		return $form;
	}
	
	// provide gravity forms hook to populate buildings in forms that need this, as per https://www.gravityhelp.com/documentation/article/dynamically-populating-drop-down-fields/
	add_filter( 'gform_pre_render_9', 'populate_allhalls' );
	add_filter( 'gform_pre_validation_9', 'populate_allhalls' );
	add_filter( 'gform_pre_submission_filter_9', 'populate_allhalls' );
	add_filter( 'gform_admin_pre_render_9', 'populate_allhalls' );
	function populate_allhalls( $form ) {
	
		foreach ( $form['fields'] as &$field ) {
	
			if ( $field->type != 'select' || strpos( $field->cssClass, 'populatehalls' ) === false ) {
				continue;
			}
	
			// manually define buildings
			$choices = array();
			$choices[] = array("text" => "Academic Village - Aspen", "value" => "Academic Village - Aspen");
			$choices[] = array("text" => "Academic Village - Engineering", "value" => "Academic Village - Engineering");
			$choices[] = array("text" => "Academic Village - Honors", "value" => "Academic Village - Honors");
			$choices[] = array("text" => "Allison", "value" => "Allison");
			$choices[] = array("text" => "Braiden", "value" => "Braiden");
			$choices[] = array("text" => "Corbett", "value" => "Corbett");
			//$choices[] = array("text" => "Durrell", "value" => "Durrell");
			$choices[] = array("text" => "Durward", "value" => "Durward");
			$choices[] = array("text" => "Edwards", "value" => "Edwards");
			$choices[] = array("text" => "Ingersoll", "value" => "Ingersoll");
			$choices[] = array("text" => "Laurel Village - Alpine", "value" => "Laurel Village - Alpine");
			$choices[] = array("text" => "Laurel Village - Piñon", "value" => "Laurel Village - Piñon");
			$choices[] = array("text" => "Newsom", "value" => "Newsom");
			$choices[] = array("text" => "Parmelee", "value" => "Parmelee");
			$choices[] = array("text" => "Summit", "value" => "Summit");
			$choices[] = array("text" => "Westfall", "value" => "Westfall");
			
			// update 'Select a Building' to whatever you'd like the instructive option to be
			$field->placeholder = 'Select a Building';
			$field->choices = $choices;
	
		}
	
		return $form;
	}





// begin new gravity forms auto-fill code

// Populate fields in form using data already in WordPress
add_filter('gform_field_value_user_firstname', function() { $value = populate_usermeta('first_name'); return $value; } );
add_filter('gform_field_value_user_lastname', function() { $value = populate_usermeta('last_name'); return $value; } );
add_filter('gform_field_value_user_netid', function() { $value = populate_usermeta('user_login'); return $value; } );
add_filter('gform_field_value_user_email', function() { $value = populate_usermeta('user_email'); return $value; } );
add_filter('gform_field_value_user_name', function() { $value = populate_usermeta('display_name'); return $value; } );

// this function returns the requested user meta of the current user
function populate_usermeta($meta_key){
    global $current_user;
    return $current_user->__get($meta_key);
}

// Populate User Phone in form using parameter user_csuid via an API call 
add_filter('gform_field_value_user_phone', function() { $value = populate_from_api('user_login','PHONE'); if (IsNullOrEmptyString($value)) $value = populate_from_api('user_login','EMPLOYEE_PHONE'); return $value; } );

// Populate CSU ID in form using parameter user_csuid 
add_filter('gform_field_value_user_csuid', function() { $value = populate_from_api('user_login','CSU_ID'); return $value; } );

// Populate User Major in form using parameter user_major 
add_filter('gform_field_value_user_major', function() { $value = populate_from_api('user_login','STUDENT_MAJOR'); return $value; } );

/* Shaun Geisert - added token support on 3/10/2023 - pre-populating eID affiliate info from API */
function populate_from_api($meta_key, $column){
    global $current_user;

    // get username of current user
    $username = $current_user->__get($meta_key);
    
    if (IsNullOrEmptyString($username))
        return;
    
    // assemble web api url
    $webapi = 'https://wsnet2.colostate.edu/cwis199/lookup/api/weidperson/' . $username;
    
    // set up cURL
    $ch = curl_init($webapi);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer 1n8FgrbMZI0GvN5RneyV3FV1ogAgCm2SgJ4s3a0Hh56ohso4ai' 
    ));
    
    // call web api
    $response = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($response);
    
    if (!is_array($json) || count($json) < 1 || IsNullOrEmptyString($json[0]->$column))
        return;
    
    return $json[0]->$column;
}

// populate form field(s) using eName against web api - RMS
add_filter('gform_field_value_user_building', function() { $value = populate_from_rms('user_login','Assign_Building'); return $value; });
add_filter('gform_field_value_user_roomnumber', function() { $value = populate_from_rms('user_login','Assign_RoomNumber'); return $value; });

// the following function can no longer work because RMS Assignments has been taken down
function populate_from_rms($meta_key, $column){
	
    global $current_user;
	
	// get username of current user
    $username = $current_user->__get($meta_key);
	
	if (IsNullOrEmptyString($username))
		return;
		
	// for testing 
	//$username = 'testename';
	
	// assemble web api url
	$webapi = 'https://wsnet2.colostate.edu/cwis199/lookup/api/rmsassignmentsinfo/' . $username;
	
	// call web api
	$response = file_get_contents($webapi);
	
	//$response = new SimpleXMLElement($response);
	$json = json_decode($response);
	
	//var_dump($json[0]);
	//var_dump($json[0]->ENAME);
	
	if (IsNullOrEmptyString($json))
		return;
	
	return $json[0]->$column;
		
}

// Function for basic field validation (present and neither empty nor only white space
function IsNullOrEmptyString($var){
	try {
		if(is_array($var)) {
			return array_map('safe', $var);
		}
		return (!isset($var) || trim($var)==='');
	} catch (Exception $e) {
    	return true;
	}
}
//End new Gravity Forms auto-fill code

// modeled after https://www.gravityhelp.com/documentation/article/using-the-gravity-forms-gform-validation-hook/
add_filter('gform_validation_2', 'mac_validation');
	function mac_validation($validation_result){
	
		$form = $validation_result["form"];
		
		foreach( $form['fields'] as &$field ) {
			if ( strpos( $field->cssClass, 'validateMAC' ) === false ) {
    			continue;
			}
			
			$field_value = rgpost( "input_{$field['id']}" );
			
			 if($field_value != filter_var($field_value, FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=> "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/" )))){
                $validation_result["is_valid"] = false;
				
				$field->failed_validation = true;
				$field->validation_message = 'Oops - A MAC address should be composed of 12 characters (as six groups of 2), using only the characters 0-9 and A-F, and colons (:) or hyphens (-) as separators (eg: 33:01:5B:64:06:94 )';
				
			 }
		}
		
		$validation_result['form'] = $form;
		return $validation_result;

	}
	
// Upon form submission, jump user to form confirmation/validation anchor
add_filter( 'gform_confirmation_anchor', '__return_true' );
	
//Anand's Changes

// Removes the shortcodes in search results
add_filter('relevanssi_pre_excerpt_content', 'rlv_trim_vc_shortcodes');
function rlv_trim_vc_shortcodes($content) {
    $content = preg_replace('/\[\/?vc.*?\]/', '', $content);
    $content = preg_replace('/\[\/?mk.*?\]/', '', $content);
    return $content;
}
	
/*** Change Admin Area appearance ***/
function my_admin_theme_style() {
    wp_enqueue_style('my-admin-style', '//studentaffairs.colostate.edu/shared/css/csuadmin.css');
}
add_action('admin_enqueue_scripts', 'my_admin_theme_style');	
add_filter( 'send_email_change_email', '__return_false' );


/*** Change Gravity Forms Save & Continue Expiration Length ***/
add_filter( 'gform_incomplete_submissions_expiration_days', 'gwp_days', 1, 10 );
function gwp_days( $expiration_days ) {
    // change this value
    $expiration_days = 180;
    return $expiration_days;
}



/*** For Debugging purposes, only include a file if Shaun is logged in ***/
add_action('admin_init', 'wp_check_username');
function wp_check_username()
{
    $user = wp_get_current_user();

    if($user && isset($user->user_login) && 'sgeisert' == $user->user_login) {
        //include_once( get_stylesheet_directory() . '/PageTextTest.php' );
    }
}



/*** Populate Event Title Dropdown for Attendance Tracking Form ***/
add_filter( 'gform_pre_render_18', 'populate_posts' ); // 18 on prod, 5 on dev
add_filter( 'gform_pre_validation_18', 'populate_posts' );
add_filter( 'gform_pre_submission_filter_18', 'populate_posts' );
add_filter( 'gform_admin_pre_render_18', 'populate_posts' );
function populate_posts( $form ) {
 
    foreach ( $form['fields'] as &$field ) {
 
        if ( $field->type != 'select' || strpos( $field->cssClass, 'populate-posts' ) === false ) {
            continue;
        }
 
        // Get entries of events
        $events = GFAPI::get_entries(17); // 17 on prod, 4 on dev
 
        $choices = array();
 
        foreach ( $events as $event ) {
            $choices[] = array( 'text' => $event[23], 'value' => $event[23] );
        }
 
        // Update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select an Event';
        $field->choices = $choices;
 
    }
 
    return $form;
}



function in_page_login_function($atts) {
    // Get current URL
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));

    // Set sign-in link
    $sign_in_link = '/eid-login/?redirect_to=' . $current_url;

    $html = '';
    $html .= '<a href=' . $sign_in_link . ' class="qbutton" target="_self" style="color: rgb(247, 247, 247); background-color: rgb(30, 77, 43);" data-hover-background-color="#104221" data-hover-color="#ffffff">';
    $html .= 'Sign in with Microsoft 365';
    $html .= '</a>';

    // Output needs to be return
    return $html;
}
add_shortcode('in_page_login', 'in_page_login_function');



/* Include DEMO SMS stuff */
// require_once( get_stylesheet_directory() . '/sms-rfr/demo-core.php' );

// Adding additional functionality (by Shaun G)
include_once( get_stylesheet_directory() . '/sms-rfr/shared-sms-logic.php');
include_once( get_stylesheet_directory() . '/sms-rfr/send-custom-text.php');
include_once( get_stylesheet_directory() . '/sms-rfr/send-event-text.php');
include_once( get_stylesheet_directory() . '/sms-rfr/add-subscriber.php');


/** Enables the HTTP Strict Transport Security (HSTS) header */
function tg_enable_strict_transport_security_hsts_header_wordpress() {
	header( 'Strict-Transport-Security: max-age=31536000' );
  }
add_action( 'send_headers', 'tg_enable_strict_transport_security_hsts_header_wordpress' );


// Change default email notification address for new Gravity Forms -MR

add_action( 'gform_after_save_form', 'set_default_notification_to', 10, 2 );
function set_default_notification_to( $form, $is_new ) {
    if ( $is_new ) {
        foreach ( $form['notifications'] as &$notification ) {
            $notification['to'] = 'slice_reception@Mail.Colostate.edu';
        }
 
        $form['is_active'] = '1';
 
        GFAPI::update_form( $form );
    }
}

/* Get nginx helper purge cache permission for admins back */
function admin_purge_cache() {
    // get the the role 
    $role = get_role('administrator');

    // add capability to this role 
    $role->add_cap('Nginx Helper | Purge cache', true);
}
add_action('init', 'admin_purge_cache', 11);