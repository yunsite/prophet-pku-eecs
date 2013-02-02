<?php
/**
 * Callback page for authorization
 * Jumping to property page automatically.
 *
 * @author Taotaotheripper <taotaotheripper@gmail.com>
 */

session_start();

include_once('auth_cfg.php');
include_once('saetv2.ex.class.php');

// Request for ACCESS_TOKEN with CODE
$o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o -> getAccessToken('code', $keys) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie('weibojs_'.$o->client_id, http_build_query($token));
	header('Location: ' . WB_CALLBACK_SUCCESS_PAGE);
} else
	header('Location: ' . WB_CALLBACK_ERROR_PAGE);
?>
