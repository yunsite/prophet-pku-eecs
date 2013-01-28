<?php
$pre_api = "https://api.weibo.com/2/";
$pt_api = "statuses/public_timeline.json";
$app_key = 2917663993;
$count = 5;

$ch = curl_init();
$curl_url = $pre_api . $pt_api . "?source=" . $app_key . "&count=" . $count;

$curl_url = "http://localhost:81/flandy/getpost2.php?web=" . $website .
"&pwd=" . $pwd . "&action=check&pseid=" . $psecode .
"&amt=" . $amt;
curl_setopt($ch, CURLOPT_URL, $curl_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
$curl_result = curl_exec($ch);
$result = explode(',', $curl_result);
curl_close($ch);

