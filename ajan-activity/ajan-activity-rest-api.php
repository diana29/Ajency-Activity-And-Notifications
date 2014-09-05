<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
//json rest api

if(is_plugin_active('json-rest-api/plugin.php')){


/**
	 * plugin api calls class
	 *
	 * @since ajency-activity-and-notifications (0.1)
	 *
	 * @uses json rest api plugin action hook wp_json_server_before_serve
	 */
	function ajan_api_init() {

			global $ajan_api_mytype;

			$ajan_api_mytype = new AJAN_API_MyType();

			add_filter( 'json_endpoints', array( $ajan_api_mytype, 'register_routes' ) );
	}

	add_action( 'wp_json_server_before_serve', 'ajan_api_init' );

	/**
	 * Extended class defining api cals for activites
	 *
	 * @since ajency-activity-and-notifications (0.1)
	 * 
	 */
	class AJAN_API_MyType {
		public function register_routes( $routes ) {
		$routes['/activity/create'] = array(
			array( array( $this, 'add_activity'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
		);
		$routes['/activities/me'] = array(
			//returns the collection of logged in user activities
			array( array( $this, 'get_logged_in_user_activities'), WP_JSON_Server::READABLE ),	 
		);
		$routes['/activities/user/(?P<id>\d+)'] = array(
			//returns the activities of a user ; user_id should the id of the user whose activites are required
			array( array( $this, 'get_user_activities'), WP_JSON_Server::READABLE ),	 
		);
		 

		// Add more custom routes here

		return $routes;
	}

	function get_user_activities($id){

			return array(ajan_get_user_personal_activities($id));
	}

	function get_logged_in_user_activities(){

			global $user_ID;

			return $this->get_user_activities($user_ID);

	}

	function add_activity(){
 			
 			global $user_ID;

 			$activity = array();

 			$error = array();

 			$status = "";

 			if(isset($_POST["user_id"])){
 				
 				$activity['user_id'] = $_POST["user_id"];
 			}

 			if(isset($_POST["action"])){
 				
 				$activity['action'] = $_POST["action"];
 			}

 			if(isset($_POST["content"])){
 				
 				$activity['content'] = $_POST["content"];
 			}

 			if(isset($_POST["component"])){
 				
 				$activity['component'] = $_POST["component"];
 			}

 			if(isset($_POST["type"])){
 				
 				$activity['type'] = $_POST["type"];
 			}
 
 			if(!isset($_POST["component"]) || !isset($_POST["type"]) || empty($_POST["component"]) || empty($_POST["type"])){
 				
 				 $error[] = "Activity component or type not set.";
 			}   
 			if(count($error)==0){
  
				$response = ajan_activity_add($activity); 

				$status = "1";

 			}else{

 				$response = $error;

 				$status = "0";


 			}
			
			$response = array('status'=>$status,'response' => $response);

			$response = json_encode( $response );

		    header( "Content-Type: application/json" );

		    echo $response;

		    exit;

	}


	// ...
}


}

