<?php

/**
 * Allow core components and dependent plugins to register activity actions.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @uses do_action() To call 'ajan_register_activity_actions' hook.
 */
function ajan_register_custom_activity_actions() {
 
	do_action( 'ajan_theme_set_activity_action' );

}
add_action( 'ajan_init', 'ajan_register_activity_actions', 9 );


/**
 * Register the activity stream actions for updates
 *
 * @since ajency-activity-and-notifications (1.6)
 *
 * @global object $ajan BuddyPress global settings.
 */
function ajan_theme_set_activity_action() {
	global $ajan;

	$theme_activity_actions = apply_filters('ajan_register_theme_activity_actions',array());
 
	foreach($theme_activity_actions as $theme_activity_action){
			ajan_activity_set_action($theme_activity_action['component_id'], 
			$theme_activity_action['type'],
			$theme_activity_action['description'],
			$theme_activity_action['format_callback'] 
		); 
	}

}
add_action( 'ajan_theme_set_activity_action', 'ajan_theme_set_activity_action' );

 