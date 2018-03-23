<?php
require_once( DP_PLUGIN_DIR . 'class.passwordhash.php' );
require_once( DP_PLUGIN_DIR . 'models/user.php' );
require_once( DP_PLUGIN_DIR . 'class.authenticate.php' );

$nonce_name = 'login_nonce';
if ( isset( $_POST[$nonce_name] ) && !wp_verify_nonce( $_POST[$nonce_name], 'submit' ) ) {
    die( 'Bad token' );
}

$error = '';
$info = '';

if ( isset( $_POST[$nonce_name] ) ) {
    try {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
		if ( empty( $email ) || empty( $password ) ) {
			throw new BadInputException( "Field empty" );
		}
    
		$usr = User::query_users_from_email($email);
		if ( count( $usr ) === 0 ) {
		    throw new BadInputException( "$email does not have an associated account" );
		}
		$password_hash = $usr[0]->password;

		if ( !Password::verify($password, $password_hash) ) {
			throw new BadInputException( "Invalid password" );
		}

		$_SESSION['id'] = $usr[0]->id;
		$_SESSION['role'] = $usr[0]->role_id;
        
        $info = "Login successful.";
        redirect("test");
    }
    catch ( Exception $e ) {
        if ( get_class( $e ) !== BadInputException ) {
            error_log($e);
        }

        $error = $e->getMessage();

        if ( get_class( $e ) === PDOException ) {
            $error = "Database error";
        }
    }
}

DanceParty::render_view_with_template( 'login.php',
    array(
        'error' => $error,
        'info' => $info
    )
);

?>
