<?php 
/**
	* Template Name: Upcoming Ram Food Events
	*/

global $wp_query, $eltd_options;
$id = $wp_query->get_queried_object_id();
$sidebar = get_post_meta($id, "eltd_show-sidebar", true);  

$enable_page_comments = false;
if(get_post_meta($id, "eltd_enable-page-comments", true) == 'yes') {
	$enable_page_comments = true;
}

if(get_post_meta($id, "eltd_page_background_color", true) != ""){
	$background_color = 'background-color: '.esc_attr(get_post_meta($id, "eltd_page_background_color", true));
}else{
	$background_color = "";
}

$content_style = "";
if(get_post_meta($id, "eltd_content-top-padding", true) != ""){
	if(get_post_meta($id, "eltd_content-top-padding-mobile", true) == 'yes'){
		$content_style = "padding-top:".esc_attr(get_post_meta($id, "eltd_content-top-padding", true))."px !important";
	}else{
		$content_style = "padding-top:".esc_attr(get_post_meta($id, "eltd_content-top-padding", true))."px";
	}
}

$pagination_classes = '';
if( isset($eltd_options['pagination_type']) && $eltd_options['pagination_type'] == 'standard' ) {
	if( isset($eltd_options['pagination_standard_position']) && $eltd_options['pagination_standard_position'] !== '' ) {
		$pagination_classes .= "standard_".esc_attr($eltd_options['pagination_standard_position']);
	}
}
elseif ( isset($eltd_options['pagination_type']) && $eltd_options['pagination_type'] == 'arrows_on_sides' ) {
	$pagination_classes .= "arrows_on_sides";
}

if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
else { $paged = 1; }

?>
	<?php get_header(); ?>
		<?php if(get_post_meta($id, "eltd_page_scroll_amount_for_sticky", true)) { ?>
			<script>
			var page_scroll_amount_for_sticky = <?php echo esc_attr(get_post_meta($id, "eltd_page_scroll_amount_for_sticky", true)); ?>;
			</script>
		<?php } ?>

		<?php get_template_part( 'title' ); ?>
		<?php get_template_part('slider'); ?>

		<div class="container"<?php eltd_inline_style($background_color); ?>>
        <?php if($eltd_options['overlapping_content'] == 'yes') {?>
            <div class="overlapping_content"><div class="overlapping_content_inner">
        <?php } ?>

                <div class="container_inner default_template_holder clearfix" <?php eltd_inline_style($content_style); ?>>
				
					<?php
						$form_id = '17';
						$entries = GFAPI::get_entries( $form_id );
						
						// var_dump( $entries );

						foreach($entries as $entry) {

							$eventDate = date( $entry[3] );
							$todayDate = date('Y-m-d');
							$eventDatePretty = date( 'F j, Y', strtotime($entry[3]) );

							if($eventDate >= $todayDate) {
								echo '<p style="margin:0; padding:0; font-size:1.2em;"><strong>' . $eventDatePretty . '</strong> <span style="font-size:.9em;">' . ltrim($entry[4], '0') . ' - ' . ltrim($entry[22], '0') . '</span></p>';
								echo '<p style="margin:0; padding:0"><strong>Location:</strong> ' . $entry[9] . '</p>';
								echo '<p style="margin:0; padding:0"><strong>Description:</strong> ' . $entry[18] . '</p>';
								echo '<hr>';
							}
							
						}
					?>
				
		    	</div>
            <?php if($eltd_options['overlapping_content'] == 'yes') {?>
                </div></div>
            <?php } ?>
	    </div>
	<?php get_footer(); ?>