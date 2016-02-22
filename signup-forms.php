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


// PAGE TEMPLATES CREATOR
include("include/page-templater.php");


// TEXT DOMAIN AND STRING TRANSLATION
function m34_load_textdomain() {
	load_plugin_textdomain( 'm34forms', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' ); 
}


// login form output
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

} // end login form output

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

		} elseif ( $login_fail == 'success' ) {
			$feedback_type = "success"; $feedback_text = __('You have logged in successfully.','m34forms');

		}
		$feedback_out = "<div class='alert alert-".$feedback_type."' role='alert'>".$feedback_text."</div>";

	} elseif ( array_key_exists('signup',$_GET) ) {
		$register_fail = sanitize_text_field($_GET['signup']);
		if ( $register_fail == 'success' ) {
			$feedback_type = "success"; $feedback_text = __('Great! You have signed up successfully. You can log in now.','m34forms');
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

// sign up form output
function m34_signup_output($action,$login_url) {

	if ( array_key_exists('wp-submit',$_POST) ) {
		$username = sanitize_text_field($_POST['user_login']);
		$email = sanitize_text_field($_POST['user_email']);
		$pass = sanitize_text_field($_POST['user_pass']);
		$pass2 = sanitize_text_field($_POST['user_pass_confirm']);
		$searchfor = " ";
		$username_with_spaces = strpos($username,$searchfor);

		if ( username_exists($username) ) {
			$feedback_type = "danger"; $feedback_text = __('This username is already in use. Try someone other.','m34forms');

		} elseif ( validate_username($username) === false || $username_with_spaces !== false ) {
			$feedback_type = "danger"; $feedback_text = __('This username is not valid. A username can only have alphanumerical character, no special characters neither spaces','m34forms');

		} elseif ( email_exists($email) ) {
			$feedback_type = "danger"; $feedback_text = __('This email address is already in use. Try another one.','m34forms');

		} elseif ( $username == '' || $email == '' || $pass == '' ) {
			$feedback_type = "danger"; $feedback_text = __('Some of the required fields are empty.','m34forms');

		} elseif ( $pass != '' && $pass != $pass2 ) {
			$feedback_type = "danger"; $feedback_text = __('The password doesn\'t match. Check it, please.','m34forms');

		} else { $feedback_type = ""; }

		if ( $feedback_type != "" ) { $feedback_out = "<div class='alert alert-".$feedback_type."' role='alert'>".$feedback_text."</div>"; }
		else {
			if ( $pass == '' ) { $pass = wp_generate_password( 16, false ); }
			$user_id = wp_create_user( $username, $pass, $email );

			wp_redirect($login_url."?signup=success");
			exit;
		}

	} else { $username = ""; $email = ""; $feedback_out = ""; }

	$req_class = " <span class='glyphicon glyphicon-asterisk'></span>";
	$form_out = $feedback_out. "
	<form class='row' name='registerform' action='".$action."' method='post'>
		<div class='form-horizontal col-md-12'>
		<fieldset class='form-group'>
			<label for='user_login' class='col-sm-3 control-label'>".__('Username','m34forms').$req_class."</label>
			<div class='col-sm-5'>
				<input id='user_login' class='form-control' type='text' value='".$username."' name='user_login' />
			</div>
		</fieldset>
		<fieldset class='form-group'>
			<label for='user_email' class='col-sm-3 control-label'>".__('Email','m34forms').$req_class."</label>
			<div class='col-sm-5'>
				<input id='user_email' class='form-control' type='text' value='".$email."' name='user_email' />
			</div>
		</fieldset>
		</fieldset>
		<fieldset class='form-group'>
			<label for='user_pass' class='col-sm-3 control-label'>".__('Password','m34forms')."</label>
			<div class='col-sm-5'>
				<input id='user_pass' class='form-control' type='password' size='20' value='' name='user_pass' />
			</div>
		</fieldset>
		<fieldset class='form-group'>
			<label for='user_pass_confirm' class='col-sm-3 control-label'>".__('Confirm password','m34forms')."</label>
			<div class='col-sm-5'>
				<input id='user_pass_confirm' class='form-control' type='password' size='20' value='' name='user_pass_confirm' />
			</div>
		</fieldset>
		<fieldset class='form-group'>
			<div class='col-sm-offset-3 col-sm-5'>
				<div class='pull-right'>
					<input id='wp-submit' class='btn btn-success' type='submit' value='".__('Sign up','m34forms')."' name='wp-submit' />
				</div>
    			</div>
		</fieldset>
		</div>
	</form>
	<div class='row'>
		<div class='col-md-5 col-md-offset-3'>
			<div class='pull-right'>
				<a class='btn btn-primary' href='".$login_url."'>".__('I already have an account.','m34forms')."</a>
			</div>
		</div>
	</div>
	";
	return $form_out;

} // end display register form


// show login/signup form and run actions
function m34_userform() {
	if ( is_user_logged_in() ) return m34_logout_output();

	$login_url = get_permalink();
	$signup_url = get_permalink()."?action=signup";

	if ( array_key_exists('action',$_GET) && sanitize_text_field($_GET['action']) == 'signup' ) {
		// if action is register
		return m34_signup_output($signup_url,$login_url);

	} else {
		$feedback_out = m34_login_error_output();
		return m34_login_output($login_url,$signup_url,$feedback_out);
	}

} // end show login/signup form and run actions
