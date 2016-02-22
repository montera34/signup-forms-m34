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
add_action( 'wp_login_failed', 'm34_login_failed' );
// redirect to right log in page when blank username or password
add_action( 'authenticate', 'm34_login_blank');

// SHORTCODES
// show user form
add_shortcode('m34_userform', 'm34_userform');


// user login form output
function m34_login_output( $redirect_url,$register_url,$feedback_out ) {
	$login_action = wp_login_url($redirect_url);
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
		<div class='row'>
			<div class='col-md-5 col-md-offset-3'>
				<div class='pull-right'>
					".__("If you don't have an account yet:",'m34forms')." <a class='btn btn-success' href='".$register_url."'>".__('Sign up','m34forms')."</a>
				</div>
			</div>
		</div>
	";
	return $form_out;

} // end user login form

// redirect to right log in page when log in failed
function m34_login_failed( $user ) {
	// check what page the login attempt is coming from
	if ( array_key_exists('ref',$_GET) ) {
		$ref = sanitize_text_field($_GET['ref']);
	} else {
		$ref = $_SERVER['HTTP_REFERER'];
		$ref = preg_replace("/\?.*$/","",$ref);
	}

	// check that we're not on the default login page
	if ( !empty($ref) && !strstr($ref,'wp-login') && !strstr($ref,'wp-admin') && $user!=null ) {
		// make sure we don't already have a failed login attempt
		if ( !strstr($ref, '?login=failed' )) {
			// Redirect to the login page and append a querystring of login failed
			wp_redirect( $ref . '?login=failed');
		} else { wp_redirect( $ref ); }

		exit;
	}
} // end redirect to right log in page when log in failed

// redirect to right log in page when blank username or password
function m34_login_blank( $user ){
	// check what page the login attempt is coming from
	if ( array_key_exists('ref',$_GET) ) {
		$ref = sanitize_text_field($_GET['ref']);
	} else {
		$ref = $_SERVER['HTTP_REFERER'];
		$ref = preg_replace('/\?.*$/','',$ref);
	}

	$error = false;
	if( array_key_exists('log',$_POST) && sanitize_text_field($_POST['log']) == '' ||
	array_key_exists('log',$_POST) && sanitize_text_field($_POST['pwd']) == '') { $error = true; }

  	// check that we're not on the default login page
	if ( !empty($ref) && !strstr($ref,'wp-login') && !strstr($ref,'wp-admin') && $error ) {

		// make sure we don't already have a failed login attempt
		if ( !strstr($ref, '?login=empty') ) {
			// Redirect to the login page and append a querystring of login failed
			wp_redirect( $ref . '?login=empty' );
		} else { wp_redirect( $ref ); }
		exit;

	}

} // end redirect to right log in page when blank username or password

// login form error output
function m34_login_error_output() {
	if ( array_key_exists('login',$_GET) ) {
		$lost_pass_url = wp_lostpassword_url(get_permalink()."?login=lost-password");
		$login_fail = sanitize_text_field($_GET['login']);
		if ( $login_fail == 'failed' ) {
			$feedback_type = "danger"; $feedback_text = __('Username or password is not correct. Check them, please. Password forgotten?','sstfg')." <a class='btn btn-default' href='".$lost_pass_url."'>".__('get another one','sstfg')."</a>";

		} elseif ( $login_fail == 'empty' ) {
			$feedback_type = "danger"; $feedback_text = __('Username or password are empty. If you forgot your password','sstfg'). "<a class='btn btn-default' href='".$lost_pass_url."'>".__('get another one','sstfg')."</a>";
		
		} elseif ( $login_fail == 'lost-password' ) {
			$feedback_type = "info"; $feedback_text = __('<strong>A new password has been sent to your email address</strong>. You should receive it in a few moments. It may go to your spam folder.','sstfg');

		}
		$feedback_out = "<div class='alert alert-".$feedback_type."' role='alert'>".$feedback_text."</div>";

	} else { $feedback_out = ""; }
	return $feedback_out;

} // end login form error output

// logout form output
function m34_logout_output() {
	$url = wp_logout_url($_SERVER['REQUEST_URI']);
	$text = __('Log out','m34forms');
	$form_out = "<a href='".$url."'>".$text."</a>";
	return $form_out;
} // end logout form output

// show login/signup form and run actions
function m34_userform() {
	if ( is_user_logged_in() ) return m34_logout_output();

	$login_redirect_url = get_permalink();
	$register_url = "";
	// build error feedback
	$feedback_out = m34_login_error_output();

	return m34_login_output($login_redirect_url,$register_url,$feedback_out);
} // end show login/signup form and run actions
