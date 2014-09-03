<?php
/**
 * ActivityNotifications Moderation Functions.
 *
 * @package ActivityNotifications
 * @subpackage Core
 * @since ActivityNotifications (1.6.0)
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Moderation ****************************************************************/

/**
 * Check for flooding.
 *
 * Check to make sure that a user is not making too many posts in a short amount
 * of time.
 *
 * @since ActivityNotifications (1.6.0)
 *
 * @uses current_user_can() To check if the current user can throttle.
 * @uses ajan_get_option() To get the throttle time.
 * @uses get_transient() To get the last posted transient of the ip.
 * @uses get_user_meta() To get the last posted meta of the user.
 *
 * @param int $user_id User id to check for flood.
 * @return bool True if there is no flooding, false if there is.
 */
function ajan_core_check_for_flood( $user_id = 0 ) {

	// Option disabled. No flood checks.
	if ( !$throttle_time = ajan_get_option( '_ajan_throttle_time' ) )
		return true;

	// Bail if no user ID passed
	if ( empty( $user_id ) )
		return false;

	$last_posted = get_user_meta( $user_id, '_ajan_last_posted', true );
	if ( isset( $last_posted ) && ( time() < ( $last_posted + $throttle_time ) ) && !current_user_can( 'throttle' ) )
		return false;

	return true;
}

/**
 * Check for moderation keys and too many links.
 *
 * @since ActivityNotifications (1.6.0)
 *
 * @uses ajan_current_author_ip() To get current user IP address.
 * @uses ajan_current_author_ua() To get current user agent.
 * @uses ajan_current_user_can() Allow super admins to bypass blacklist.
 *
 * @param int $user_id Topic or reply author ID.
 * @param string $title The title of the content.
 * @param string $content The content being posted.
 * @return bool True if test is passed, false if fail.
 */
function ajan_core_check_for_moderation( $user_id = 0, $title = '', $content = '' ) {

	// Bail if super admin is author
	if ( is_super_admin( $user_id ) )
		return true;

	// Define local variable(s)
	$post      = array();
	$match_out = '';

	/** Blacklist *************************************************************/

	// Get the moderation keys
	$blacklist = trim( get_option( 'moderation_keys' ) );

	// Bail if blacklist is empty
	if ( empty( $blacklist ) )
		return true;

	/** User Data *************************************************************/

	if ( !empty( $user_id ) ) {

		// Get author data
		$user = get_userdata( $user_id );

		// If data exists, map it
		if ( !empty( $user ) ) {
			$post['author'] = $user->display_name;
			$post['email']  = $user->user_email;
			$post['url']    = $user->user_url;
		}
	}

	// Current user IP and user agent
	$post['user_ip'] = ajan_core_current_user_ip();
	$post['user_ua'] = ajan_core_current_user_ua();

	// Post title and content
	$post['title']   = $title;
	$post['content'] = $content;

	/** Max Links *************************************************************/

	$max_links = get_option( 'comment_max_links' );
	if ( !empty( $max_links ) ) {

		// How many links?
		$num_links = preg_match_all( '/<a [^>]*href/i', $content, $match_out );

		// Allow for bumping the max to include the user's URL
		$num_links = apply_filters( 'comment_max_links_url', $num_links, $post['url'] );

		// Das ist zu viele links!
		if ( $num_links >= $max_links ) {
			return false;
		}
	}

	/** Words *****************************************************************/

	// Get words separated by new lines
	$words = explode( "\n", $blacklist );

	// Loop through words
	foreach ( (array) $words as $word ) {

		// Trim the whitespace from the word
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word    = preg_quote( $word, '#' );
		$pattern = "#$word#i";

		// Loop through post data
		foreach( $post as $post_data ) {

			// Check each user data for current word
			if ( preg_match( $pattern, $post_data ) ) {

				// Post does not pass
				return false;
			}
		}
	}

	// Check passed successfully
	return true;
}

/**
 * Check for blocked keys.
 *
 * @since ActivityNotifications (1.6.0)
 *
 * @uses ajan_current_author_ip() To get current user IP address.
 * @uses ajan_current_author_ua() To get current user agent.
 * @uses ajan_current_user_can() Allow super admins to bypass blacklist.
 *
 * @param int $user_id Topic or reply author ID.
 * @param string $title The title of the content.
 * @param string $content The content being posted.
 * @return bool True if test is passed, false if fail.
 */
function ajan_core_check_for_blacklist( $user_id = 0, $title = '', $content = '' ) {

	// Bail if super admin is author
	if ( is_super_admin( $user_id ) )
		return true;

	// Define local variable
	$post = array();

	/** Blacklist *************************************************************/

	// Get the moderation keys
	$blacklist = trim( get_option( 'blacklist_keys' ) );

	// Bail if blacklist is empty
	if ( empty( $blacklist ) )
		return true;

	/** User Data *************************************************************/

	// Map current user data
	if ( !empty( $user_id ) ) {

		// Get author data
		$user = get_userdata( $user_id );

		// If data exists, map it
		if ( !empty( $user ) ) {
			$post['author'] = $user->display_name;
			$post['email']  = $user->user_email;
			$post['url']    = $user->user_url;
		}
	}

	// Current user IP and user agent
	$post['user_ip'] = ajan_core_current_user_ip();
	$post['user_ua'] = ajan_core_current_user_ua();

	// Post title and content
	$post['title']   = $title;
	$post['content'] = $content;

	/** Words *****************************************************************/

	// Get words separated by new lines
	$words = explode( "\n", $blacklist );

	// Loop through words
	foreach ( (array) $words as $word ) {

		// Trim the whitespace from the word
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) ) { continue; }

		// Do some escaping magic so that '#' chars in the spam words don't break things:
		$word    = preg_quote( $word, '#' );
		$pattern = "#$word#i";

		// Loop through post data
		foreach( $post as $post_data ) {

			// Check each user data for current word
			if ( preg_match( $pattern, $post_data ) ) {

				// Post does not pass
				return false;
			}
		}
	}

	// Check passed successfully
	return true;
}

/**
 * Get the current user's IP address.
 *
 * @since ActivityNotifications (1.6.0)
 *
 * @return string IP address.
 */
function ajan_core_current_user_ip() {
	$retval = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );

	return apply_filters( 'ajan_core_current_user_ip', $retval );
}

/**
 * Get the current user's user-agent.
 *
 * @since ActivityNotifications (1.6.0)
 *
 * @return string User agent string.
 */
function ajan_core_current_user_ua() {

	// Sanity check the user agent
	if ( !empty( $_SERVER['HTTP_USER_AGENT'] ) )
		$retval = substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 );
	else
		$retval = '';

	return apply_filters( 'ajan_core_current_user_ua', $retval );
}