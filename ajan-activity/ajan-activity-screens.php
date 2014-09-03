<?php

/**
 * BuddyPress Activity Screens.
 *
 * The functions in this file detect, with each page load, whether an Activity
 * component page is being requested. If so, it parses any necessary data from
 * the URL, and tells BuddyPress to load the appropriate template.
 *
 * @package BuddyPress
 * @subpackage ActivityScreens
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Load the Activity directory.
 *
 * @since ajency-activity-and-notifications (1.5)
 *
 * @uses ajan_displayed_user_id()
 * @uses ajan_is_activity_component()
 * @uses ajan_current_action()
 * @uses ajan_update_is_directory()
 * @uses do_action() To call the 'ajan_activity_screen_index' hook.
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_screen_index' hook.
 */
function ajan_activity_screen_index() {
	if ( ajan_is_activity_directory() ) {
		ajan_update_is_directory( true, 'activity' );

		do_action( 'ajan_activity_screen_index' );

		ajan_core_load_template( apply_filters( 'ajan_activity_screen_index', 'activity/index' ) );
	}
}
add_action( 'ajan_screens', 'ajan_activity_screen_index' );

/**
 * Load the 'My Activity' page.
 *
 * @since ajency-activity-and-notifications (1.0)
 *
 * @uses do_action() To call the 'ajan_activity_screen_my_activity' hook.
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_template_my_activity' hook.
 */
function ajan_activity_screen_my_activity() {
	do_action( 'ajan_activity_screen_my_activity' );
	ajan_core_load_template( apply_filters( 'ajan_activity_template_my_activity', 'members/single/home' ) );
}

/**
 * Load the 'My Friends' activity page.
 *
 * @since ajency-activity-and-notifications (1.0)
 *
 * @uses ajan_is_active()
 * @uses ajan_update_is_item_admin()
 * @uses ajan_current_user_can()
 * @uses do_action() To call the 'ajan_activity_screen_friends' hook.
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_template_friends_activity' hook.
 */
function ajan_activity_screen_friends() {
	if ( !ajan_is_active( 'friends' ) )
		return false;

	ajan_update_is_item_admin( ajan_current_user_can( 'ajan_moderate' ), 'activity' );
	do_action( 'ajan_activity_screen_friends' );
	ajan_core_load_template( apply_filters( 'ajan_activity_template_friends_activity', 'members/single/home' ) );
}

/**
 * Load the 'My Groups' activity page.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @uses ajan_is_active()
 * @uses ajan_update_is_item_admin()
 * @uses ajan_current_user_can()
 * @uses do_action() To call the 'ajan_activity_screen_groups' hook
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_template_groups_activity' hook
 */
function ajan_activity_screen_groups() {
	if ( !ajan_is_active( 'groups' ) )
		return false;

	ajan_update_is_item_admin( ajan_current_user_can( 'ajan_moderate' ), 'activity' );
	do_action( 'ajan_activity_screen_groups' );
	ajan_core_load_template( apply_filters( 'ajan_activity_template_groups_activity', 'members/single/home' ) );
}

/**
 * Load the 'Favorites' activity page.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @uses ajan_update_is_item_admin()
 * @uses ajan_current_user_can()
 * @uses do_action() To call the 'ajan_activity_screen_favorites' hook
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_template_favorite_activity' hook
 */
function ajan_activity_screen_favorites() {
	ajan_update_is_item_admin( ajan_current_user_can( 'ajan_moderate' ), 'activity' );
	do_action( 'ajan_activity_screen_favorites' );
	ajan_core_load_template( apply_filters( 'ajan_activity_template_favorite_activity', 'members/single/home' ) );
}

/**
 * Load the 'Mentions' activity page.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @uses ajan_update_is_item_admin()
 * @uses ajan_current_user_can()
 * @uses do_action() To call the 'ajan_activity_screen_mentions' hook
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_template_mention_activity' hook
 */
function ajan_activity_screen_mentions() {
	ajan_update_is_item_admin( ajan_current_user_can( 'ajan_moderate' ), 'activity' );
	do_action( 'ajan_activity_screen_mentions' );
	ajan_core_load_template( apply_filters( 'ajan_activity_template_mention_activity', 'members/single/home' ) );
}

/**
 * Reset the logged-in user's new mentions data when he visits his mentions screen.
 *
 * @since ajency-activity-and-notifications (1.5)
 *
 * @uses ajan_is_my_profile()
 * @uses ajan_activity_clear_new_mentions()
 * @uses ajan_loggedin_user_id()
 */
function ajan_activity_reset_my_new_mentions() {
	if ( ajan_is_my_profile() )
		ajan_activity_clear_new_mentions( ajan_loggedin_user_id() );
}
add_action( 'ajan_activity_screen_mentions', 'ajan_activity_reset_my_new_mentions' );

/**
 * Load the page for a single activity item.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @global object $ajan BuddyPress global settings
 * @uses ajan_is_activity_component()
 * @uses ajan_activity_get_specific()
 * @uses ajan_current_action()
 * @uses ajan_action_variables()
 * @uses ajan_do_404()
 * @uses ajan_is_active()
 * @uses groups_get_group()
 * @uses groups_is_user_member()
 * @uses apply_filters_ref_array() To call the 'ajan_activity_permalink_access' hook
 * @uses do_action() To call the 'ajan_activity_screen_single_activity_permalink' hook
 * @uses ajan_core_add_message()
 * @uses is_user_logged_in()
 * @uses ajan_core_redirect()
 * @uses site_url()
 * @uses esc_url()
 * @uses ajan_get_root_domain()
 * @uses ajan_get_activity_root_slug()
 * @uses ajan_core_load_template()
 * @uses apply_filters() To call the 'ajan_activity_template_profile_activity_permalink' hook
 */
function ajan_activity_screen_single_activity_permalink() {
	global $ajan;

	// No displayed user or not viewing activity component
	if ( !ajan_is_activity_component() )
		return false;

	if ( ! ajan_current_action() || !is_numeric( ajan_current_action() ) )
		return false;

	// Get the activity details
	$activity = ajan_activity_get_specific( array( 'activity_ids' => ajan_current_action(), 'show_hidden' => true, 'spam' => 'ham_only', ) );

	// 404 if activity does not exist
	if ( empty( $activity['activities'][0] ) || ajan_action_variables() ) {
		ajan_do_404();
		return;

	} else {
		$activity = $activity['activities'][0];
	}

	// Default access is true
	$has_access = true;

	// If activity is from a group, do an extra cap check
	if ( isset( $ajan->groups->id ) && $activity->component == $ajan->groups->id ) {

		// Activity is from a group, but groups is currently disabled
		if ( !ajan_is_active( 'groups') ) {
			ajan_do_404();
			return;
		}

		// Check to see if the group is not public, if so, check the
		// user has access to see this activity
		if ( $group = groups_get_group( array( 'group_id' => $activity->item_id ) ) ) {

			// Group is not public
			if ( 'public' != $group->status ) {

				// User is not a member of group
				if ( !groups_is_user_member( ajan_loggedin_user_id(), $group->id ) ) {
					$has_access = false;
				}
			}
		}
	}

	// Allow access to be filtered
	$has_access = apply_filters_ref_array( 'ajan_activity_permalink_access', array( $has_access, &$activity ) );

	// Allow additional code execution
	do_action( 'ajan_activity_screen_single_activity_permalink', $activity, $has_access );

	// Access is specifically disallowed
	if ( false === $has_access ) {

		// User feedback
		ajan_core_add_message( __( 'You do not have access to this activity.', 'ajency-activity-and-notifications' ), 'error' );

		// Redirect based on logged in status
		is_user_logged_in() ?
			ajan_core_redirect( ajan_loggedin_user_domain() ) :
			ajan_core_redirect( site_url( 'wp-login.php?redirect_to=' . esc_url( ajan_get_root_domain() . '/' . ajan_get_activity_root_slug() . '/p/' . ajan_current_action() . '/' ) ) );
	}

	ajan_core_load_template( apply_filters( 'ajan_activity_template_profile_activity_permalink', 'members/single/activity/permalink' ) );
}
add_action( 'ajan_screens', 'ajan_activity_screen_single_activity_permalink' );

/**
 * Add activity notifications settings to the notifications settings page.
 *
 * @since ajency-activity-and-notifications (1.2)
 *
 * @uses ajan_get_user_meta()
 * @uses ajan_core_get_username()
 * @uses do_action() To call the 'ajan_activity_screen_notification_settings' hook.
 */
function ajan_activity_screen_notification_settings() {

	if ( ajan_activity_do_mentions() ) {
		if ( ! $mention = ajan_get_user_meta( ajan_displayed_user_id(), 'notification_activity_new_mention', true ) ) {
			$mention = 'yes';
		}
	}

	if ( ! $reply = ajan_get_user_meta( ajan_displayed_user_id(), 'notification_activity_new_reply', true ) ) {
		$reply = 'yes';
	}

	?>

	<table class="notification-settings" id="activity-notification-settings">
		<thead>
			<tr>
				<th class="icon">&nbsp;</th>
				<th class="title"><?php _e( 'Activity', 'ajency-activity-and-notifications' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'ajency-activity-and-notifications' ) ?></th>
				<th class="no"><?php _e( 'No', 'ajency-activity-and-notifications' )?></th>
			</tr>
		</thead>

		<tbody>
			<?php if ( ajan_activity_do_mentions() ) : ?>
				<tr id="activity-notification-settings-mentions">
					<td>&nbsp;</td>
					<td><?php printf( __( 'A member mentions you in an update using "@%s"', 'ajency-activity-and-notifications' ), ajan_core_get_username( ajan_displayed_user_id() ) ) ?></td>
					<td class="yes"><input type="radio" name="notifications[notification_activity_new_mention]" value="yes" <?php checked( $mention, 'yes', true ) ?>/></td>
					<td class="no"><input type="radio" name="notifications[notification_activity_new_mention]" value="no" <?php checked( $mention, 'no', true ) ?>/></td>
				</tr>
			<?php endif; ?>

			<tr id="activity-notification-settings-replies">
				<td>&nbsp;</td>
				<td><?php _e( "A member replies to an update or comment you've posted", 'ajency-activity-and-notifications' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_activity_new_reply]" value="yes" <?php checked( $reply, 'yes', true ) ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_activity_new_reply]" value="no" <?php checked( $reply, 'no', true ) ?>/></td>
			</tr>

			<?php do_action( 'ajan_activity_screen_notification_settings' ) ?>
		</tbody>
	</table>

<?php
}
add_action( 'ajan_notification_settings', 'ajan_activity_screen_notification_settings', 1 );

/** Theme Compatability *******************************************************/

/**
 * The main theme compat class for BuddyPress Activity.
 *
 * This class sets up the necessary theme compatability actions to safely output
 * activity template parts to the_title and the_content areas of a theme.
 *
 * @since ajency-activity-and-notifications (1.7)
 */
class AJAN_Activity_Theme_Compat {

	/**
	 * Set up the activity component theme compatibility.
	 *
	 * @since ajency-activity-and-notifications (1.7)
	 */
	public function __construct() {
		add_action( 'ajan_setup_theme_compat', array( $this, 'is_activity' ) );
	}

	/**
	 * Set up the theme compatibility hooks, if we're looking at an activity page.
	 *
	 * @since ajency-activity-and-notifications (1.7)
	 */
	public function is_activity() {

		// Bail if not looking at a group
		if ( ! ajan_is_activity_component() )
			return;

		// Activity Directory
		if ( ! ajan_displayed_user_id() && ! ajan_current_action() ) {
			ajan_update_is_directory( true, 'activity' );

			do_action( 'ajan_activity_screen_index' );

			add_filter( 'ajan_get_buddypress_template',                array( $this, 'directory_template_hierarchy' ) );
			add_action( 'ajan_template_include_reset_dummy_post_data', array( $this, 'directory_dummy_post' ) );
			add_filter( 'ajan_replace_the_content',                    array( $this, 'directory_content'    ) );

		// Single activity
		} elseif ( ajan_is_single_activity() ) {
			add_filter( 'ajan_get_buddypress_template',                array( $this, 'single_template_hierarchy' ) );
			add_action( 'ajan_template_include_reset_dummy_post_data', array( $this, 'single_dummy_post' ) );
			add_filter( 'ajan_replace_the_content',                    array( $this, 'single_dummy_content'    ) );
		}
	}

	/** Directory *************************************************************/

	/**
	 * Add template hierarchy to theme compat for the activity directory page.
	 *
	 * This is to mirror how WordPress has {@link https://codex.wordpress.org/Template_Hierarchy template hierarchy}.
	 *
	 * @since ajency-activity-and-notifications (1.8)
	 *
	 * @param string $templates The templates from ajan_get_theme_compat_templates().
	 * @return array $templates Array of custom templates to look for.
	 */
	public function directory_template_hierarchy( $templates ) {
		// Setup our templates based on priority
		$new_templates = apply_filters( 'ajan_template_hierarchy_activity_directory', array(
			'activity/index-directory.php'
		) );

		// Merge new templates with existing stack
		// @see ajan_get_theme_compat_templates()
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with directory data.
	 *
	 * @since ajency-activity-and-notifications (1.7)
	 */
	public function directory_dummy_post() {
		ajan_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => ajan_get_directory_title( 'activity' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => 'ajan_activity',
			'post_status'    => 'publish',
			'is_page'        => true,
			'comment_status' => 'closed'
		) );
	}

	/**
	 * Filter the_content with the groups index template part.
	 *
	 * @since ajency-activity-and-notifications (1.7)
	 */
	public function directory_content() {
		return ajan_buffer_template_part( 'activity/index', null, false );
	}

	/** Single ****************************************************************/

	/**
	 * Add custom template hierarchy to theme compat for activity permalink pages.
	 *
	 * This is to mirror how WordPress has {@link https://codex.wordpress.org/Template_Hierarchy template hierarchy}.
	 *
	 * @since ajency-activity-and-notifications (1.8)
	 *
	 * @param string $templates The templates from ajan_get_theme_compat_templates().
	 * @return array $templates Array of custom templates to look for.
	 */
	public function single_template_hierarchy( $templates ) {
		// Setup our templates based on priority
		$new_templates = apply_filters( 'ajan_template_hierarchy_activity_single_item', array(
			'activity/single/index.php'
		) );

		// Merge new templates with existing stack
		// @see ajan_get_theme_compat_templates()
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with the displayed user's data.
	 *
	 * @since ajency-activity-and-notifications (1.7)
	 */
	public function single_dummy_post() {
		ajan_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => __( 'Activity', 'ajency-activity-and-notifications' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => 'ajan_activity',
			'post_status'    => 'publish',
			'is_page'        => true,
			'comment_status' => 'closed'
		) );
	}

	/**
	 * Filter the_content with the members' activity permalink template part.
	 *
	 * @since ajency-activity-and-notifications (1.7)
	 */
	public function single_dummy_content() {
		return ajan_buffer_template_part( 'activity/single/home', null, false );
	}
}
new AJAN_Activity_Theme_Compat();
