<?php
/**
 * Config for authorization
 * 
 * @author Taotaotheripper <taotaotheripper@gmail.com>
 */
header('Content-Type: text/html; charset=UTF-8');

define("WB_AKEY", '2359821431');
define("WB_SKEY", 'cb09302d0e1f0bd236075f2c6b777073');
define("WB_CALLBACK_URL", 'http://127.0.0.1/prophet-pku-eecs/auth_cb.php');

/*
 * WB_CALLBACK_SUCCESS_PAGE : Page shown after successful authorization
 * WB_CALLBACK_ERROR_PAGE : Page shown after unsuccessful authorization
 */
define("WB_CALLBACK_SUCCESS_PAGE", 'weibolist.php');
define("WB_CALLBACK_ERROR_PAGE", 'weibolist.php');
?>
