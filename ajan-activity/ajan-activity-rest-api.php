<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
//json rest api

if(is_plugin_active('json-rest-api/plugin.php')){



	function ajan_api_init() {

			global $ajan_api_mytype;

			$ajan_api_mytype = new AJAN_API_MyType();

			add_filter( 'json_endpoints', array( $ajan_api_mytype, 'register_routes' ) );
	}

	add_action( 'wp_json_server_before_serve', 'ajan_api_init' );

	class AJAN_API_MyType {
		public function register_routes( $routes ) {
		$routes['/ajan/activities'] = array(
			array( array( $this, 'get_activities'), WP_JSON_Server::READABLE ),
			array( array( $this, 'add_activity'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
		);
		$routes['/ajan/activities/personal/(?P<id>\d+)'] = array(
			array( array( $this, 'get_personal_activities'), WP_JSON_Server::READABLE ),	 
		);
		 

		// Add more custom routes here

		return $routes;
	}

	function get_personal_activities($id){

			return array(ajan_get_user_personal_activities($id));
	}

	// ...
}


}


