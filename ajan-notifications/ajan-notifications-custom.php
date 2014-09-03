<?php

/**
 * Allow core components and dependent plugins to register activity actions.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @uses do_action() To call 'ajan_register_notification_actions' hook.
 */
function ajan_register_notification_actions() { 

	do_action( 'ajan_theme_set_notification_action' );
 
}
add_action( 'ajan_init', 'ajan_register_notification_actions', 11 );

/**
 * Register the notification actions for updates
 *
 * @since ajency-activity-and-notifications (1.6)
 *
 * @global object $ajan BuddyPress global settings.
 */
function ajan_theme_set_notification_action() {
	global $ajan;
	$components = array();
	$components[] = array(	'component_id'		=>	'activity', 
						'format_callback'	=>	'ajan_activity_format_notifications'
					 ); 

	$theme_notification_actions = apply_filters('ajan_register_theme_notification_actions',$components);
 
	foreach($theme_notification_actions as $theme_notification_action){
			ajan_notification_set_action($theme_notification_action['component_id'], 
			$theme_notification_action['format_callback'] 
		); 
	}

}
add_action( 'ajan_theme_set_notification_action', 'ajan_theme_set_notification_action' );



function ajan_notification_set_action($component,$callback){
 
	$ajan = activitynotifications(); 

	$ajan->{'notifications'}->notification_components[$component] =  $callback ;
 
	return true;
}

 