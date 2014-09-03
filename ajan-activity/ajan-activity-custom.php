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


/**
 * return the activity collections called on ajan_has_activities filter hook
 *
 * @since ajency-activity-and-notifications (0.1.0)
 * @return $activities_template the activity collection array or false if no activities are found 
 */
function ajan_has_activities_return($has_activities, $activities_template, $template_args){
 	
 	
 	if($has_activities){
		return $activities_template->activities;
 	}else{
 		return $has_activities; //if activities are not present return false
 	}
	
}


/**
 * get user specific activities
 *
 * @since ajency-activity-and-notifications (0.1.0)
 * @uses ajan_has_activities() to get activities.
 * @uses ajan_has_activities filter hook to return the as it is
 * @param $user_id the users whose activities need to be returned, 
 * if not passed the logged in users activites are returned
 * @param $page which page /offset to return
 * @param $per_page no of activites per page
 * if either  $page or $per_page activites are not paginated
 */

function ajan_get_user_personal_activities($user_id=0,$page='',$per_page=''){

	//if no user_id is passed then get the current logged in user id and return his activities
	if($user_id==0){

		global $user_ID;

		$user_id = $user_ID;

	}
	$args = array( 
		// Filtering
		'user_id'           => $user_id,     // user_id to filter on
		'page'              => $page,        // which page to load
		'per_page'          => $per_page,    // number of items per page
		 
	);

	add_filter('ajan_has_activities','ajan_has_activities_return',10,3);

    return ajan_has_activities($args) ;

 }


 /**
 * get activities where the user has been mentioned,
 *
 * @since ajency-activity-and-notifications (0.1.0)
 * @uses ajan_has_activities() to get activities.
 * @uses ajan_has_activities filter hook to return the as it is
 * @param $user_id the suer id of the user who is mentioned in activites, 
 * if not passed the logged in users activites are returned
 * @param $page which page /offset to return
 * @param $per_page no of activites per page
 * if either  $page or $per_page activites are not paginated
 */

function ajan_get_user_mentions_activities($user_id=0,$page='',$per_page=''){

	//if no user_id is passed then get the current logged in user id and return his activities
	if($user_id==0){

		global $user_ID;

		$user_id = $user_ID;

	}
	$args = array( 
		// Filtering
		'user_id'           => $user_id,     // user_id to filter on
		'page'              => $page,        // which page to load
		'per_page'          => $per_page,    // number of items per page
		'scope'             => 'mentions',     // user_id to filter on
		 
	);

	add_filter('ajan_has_activities','ajan_has_activities_return',10,3);

    return ajan_has_activities($args) ;

 }