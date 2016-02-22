<?php
/*
Plugin Name: Front-end signup and log in forms
Description: Add sign up and log in forms in the front end of your WordPress with shortcodes.
Version: 0.1
Author: montera34
Author URI: http://montera34.com
License: GPLv3
*/

// ACTIONS and FILTERS
// hook failed login
//add_action( 'wp_login_failed', 'm34_login_failed' );
// redirect to right log in page when blank username or password
//add_action( 'authenticate', 'm34_blank_login');

// SHORTCODES
// show user form
add_shortcode('m34_userform', 'm34_userform');


// user login form 
function m34_login_output( $action,$register_url,$feedback_out ) {
	$login_action = wp_login_url($action);
	$form_out = $feedback_out. "
		<form class='row' id='loginform' name='loginform' method='post' action='" .$login_action. "' role='form'>
			<div class='form-horizontal col-md-12'>
			<fieldset class='form-group'>
				<label for='user_login' class='col-sm-3 control-label'>".__('Username','m34forms')."</label>
				<div class='col-sm-5'>
					<input id='user_login' class='form-control' type='text' value='' name='log' />
				</div>
			</fieldset>
			<fieldset class='form-group'>
				<label for='user_pass' class='col-sm-3 control-label'>".__('Password','m34forms')."</label>
				<div class='col-sm-5'>
					<input id='user_pass' class='form-control' type='password' size='20' value='' name='pwd' />
				</div>
			</fieldset>
			<fieldset class='form-group'>
				<div class='col-sm-offset-3 col-sm-3 checkbox'>
					<label>
						<input id='rememberme' type='checkbox' value='forever' name='rememberme' /> ".__('Remember me','m34forms')."
					</label>
				</div>
				<div class='col-sm-2'>
					<div class='pull-right'>
						<input id='wp-submit' class='btn btn-primary' type='submit' value='".__('Log in','m34forms')."' name='wp-submit' />
					</div>
	    			</div>
			</fieldset>
			</div>
		</form>
	";
	return $form_out;

} // end user login form

// user logout form 
function m34_logout_output() {
	$url = wp_logout_url($_SERVER['REQUEST_URI']);
	$text = __('Log out','m34forms');
	$form_out = "<a href='".$url."'>".$text."</a>";
	return $form_out;
}

// show user login/signup form
function m34_userform() {
	if ( is_user_logged_in() ) return m34_logout_output();

	$action = get_permalink();
	$login_action = wp_login_url($redirect_url);
	$feedback_out = "";

	return m34_login_output($action,$register_url,$feedback_out);
}
