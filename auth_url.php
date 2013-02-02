<?php
/**
 * Auth URL
 * 
 * @author Taotaotheripper <taotaotheripper@gmail.com>
 * @return {"status" : "ok", "auth_url" : "the auth url"}
 */
 
session_start();

include_once('auth_cfg.php');
include_once('saetv2.ex.class.php');

$o = new SaeTOAuthV2(WB_AKEY , WB_SKEY);

$code_url = $o -> getAuthorizeURL(WB_CALLBACK_URL);

//This function will never fail, always return ok.
$result = array("status" => "ok", "auth_url" => $code_url);

print json_encode($result);
?>