<?php

/**
 * callback_model
 */

session_start();
require_once("dataApp.php");
require_once("model/connection_token_model.php");

//C:\Users\Cesar Castillo Lopez\AppData\Local\Programs\Python\Python37

$access_token = "APP_USR-3049435460117644-091601-2be809ec45e3037143086c031c23ee75-268590414";
$refresh_token = "TG-5d7d61fab0065e00066cdd07-268590414";

$conexion_token = new Conexion_Token($appId, $secretKey,$access_token);

// Adicionar 
//,$access_token,$refresh_token


/*if (!$_GET['code'] && !$_SESSION['access_token']) {
    $callback = $conexion_token->getAuthUrl($redirectURI, Conexion_Token::$AUTH_URL[$siteId]);
}*/

if($_GET['code'] || $_SESSION['access_token']) {

    // If code exist and session is empty
    if($_GET['code'] && !($_SESSION['access_token'])) {
        // If the code was in get parameter we authorize
        $user = $conexion_token->authorize($_GET['code'], $redirectURI);

        // Now we create the sessions with the authenticated user
        $_SESSION['access_token'] = $user['body']->access_token;
        $_SESSION['expires_in'] = time() + $user['body']->expires_in;
        $_SESSION['refresh_token'] = $user['body']->refresh_token;
    } else {
        // We can check if the access token in invalid checking the time
        if($_SESSION['expires_in'] < time()) {
            try {
                // Make the refresh proccess
                $refresh = $conexion_token->refreshAccessToken();

                // Now we create the sessions with the new parameters
                $_SESSION['access_token'] = $refresh['body']->access_token;
                $_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
                $_SESSION['refresh_token'] = $refresh['body']->refresh_token;
            } catch (Exception $e) {
                echo "Exception: ",  $e->getMessage(), "\n";
            }
        }
    }

    echo '<pre>';
        print_r($_SESSION);
    echo '</pre>';

} else {
    echo '<p><a alt="Login using MercadoLibre oAuth 2.0" class="btn" href="' . $conexion_token->getAuthUrl($redirectURI, $conexion_token::$AUTH_URL[$siteId]) . '">Authenticate</a></p>';
}
