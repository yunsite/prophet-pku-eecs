<?php
/**
 * Automatically statuses fetching, updating and removing
 * 
 * @author Taotaotheripper <taotaotheripper@gmail.com>
 */
header("Content-Type: text/html; charset=utf-8");

set_time_limit(0);

session_start();

include_once('db_cfg.php');
include_once('auth_cfg.php');
include_once('saetv2.ex.class.php');

/*
 * Get max status id current now
 */
function max_id($conn) {
	//Format select
	$sql = sprintf("select max(w_id) as max_id from %s", DB_TABLENAME);
	if($result = mysql_query($sql, $conn)) {
		//Only one record
		$item = mysql_fetch_assoc($result);
		return $item['max_id'];
	} else
		return 0;
}
 
/*
 * Judge whether a status is expired
 */
function is_status_expired($date) {
	$interval = $date -> diff(new DateTime('now'));
	return intval($interval -> format('%a')) >= EXPIRE_TIME;
}

/*
 * Insert a status into the database
 */
function insert_new_status($conn, $status)
{
	global $DBCOLARRAY;
	
	//Recursively insert
	//	PS: the status may be deleted
	if(isset($status['retweeted_status'])
		&& !isset($status['retweeted_status']['deleted']))
		insert_new_status($conn, $status['retweeted_status']);
	
	//Change date format
	$date = DateTime::createFromFormat('D M d H:i:s O Y', $status['created_at']);
	if(is_status_expired($date))
	//If so, the following status must be expired, too.
		return false;
	
	//Format value array
	$vcolarray = array($status['idstr'], "'".$date -> format('Y-m-d H:i:s')."'", 
		"'".mysql_real_escape_string($status['text'])."'", $status['user']['idstr'],
		"'".mysql_real_escape_string($status['user']['screen_name'])."'",
		$status['reposts_count'], $status['comments_count'], $status['attitudes_count']);
	
	//Format insert
	$sql = sprintf("replace into %s (%s) values (%s)", DB_TABLENAME, implode(",", $DBCOLARRAY), implode(",", $vcolarray));
	
	mysql_query($sql, $conn) or die("query failed: " . mysql_error());
	
	return true;
}

/*
 * Initialize the database with all history data
 */
function new_statuses_fetch($conn, $tc, $sin_id)
{
	$page = 1;
	$count = 100; //Max
	$max_id = 0;
	do {
		$sts = $tc -> home_timeline($page, $count, $sin_id);
		while(!is_array($sts['statuses']))
			$sts = $tc -> home_timeline($page);
		foreach($sts['statuses'] as $st) {
			if(!$max_id) $max_id = $st['id'];
			if(!insert_new_status($conn, $st))
				goto expired;
		}
		$page ++;
	} while ($sts['next_cursor'] != 0);
expired:
	if($max_id)
		return $max_id;
	else
		//There's no new status
		return $sin_id;
}

/*
 * Remove expired statuses
 */
function expired_statuses_remove($conn)
{
	$sql = sprintf("delete from %s
		where timediff(now(), time) >= '%s'",
		DB_TABLENAME, (EXPIRE_TIME * 24).':00:00');
	print $sql;
	mysql_query($sql, $conn) or die("query failed: " . mysql_error());
}

/*
 * Update a group of status with
 */
function statuses_update($w_ids, $conn, $tc) {
	$rcs = $tc -> show_count_batch($w_ids);
	while(!is_array($rcs))
		$rcs = $tc -> show_count_batch($w_ids);
	foreach($rcs as $rc) {
		$sql = sprintf("update %s set rep_cnt = %s, cmt_cnt = %s where w_id = %s",
			DB_TABLENAME, $rc['reposts'], $rc['comments'], $rc['id']);
		mysql_query($sql, $conn) or die("query failed: " . mysql_error());
	}
}

/*
 * Update all statuses collected.
 */
function old_statuses_update($conn, $tc) {
	//Format select
	$sql = sprintf("select w_id from %s order by w_id desc limit %d",
		DB_TABLENAME, COUNT_LIMIT);
	$result = mysql_query($sql, $conn);
	if(!$result)
		return;
	$MAX_FETCH_PER_TIME = 100;
	$snum = 0;
	$w_ids = array();
	while($item = mysql_fetch_assoc($result)) {
		array_push($w_ids, $item['w_id']);
		$snum ++;
		if($snum == $MAX_FETCH_PER_TIME) {
			statuses_update($w_ids, $conn, $tc);
			unset($w_ids);
			$w_ids = array();
			$snum = 0;
		}
	}
	if($snum)
		statuses_update($w_ids, $conn, $tc);
	unset($w_ids);
	mysql_free_result($result);
}

$c = new SaeTClientV2(WB_AKEY , WB_SKEY , $_SESSION['token']['access_token']);

/*
 * Start looping
 * 	1. New statuses fetching
 * 	2. Expired statuses removing
 * 	3. Old statuses updating
 */
//mysql_connect
$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("connect failed: " . mysql_error());
mysql_select_db(DB_DATABASENAME, $conn);
$max_id = max_id($conn);
while(true) {
	
	$max_id = new_statuses_fetch($conn, $c, $max_id);
	expired_statuses_remove($conn);
	old_statuses_update($conn, $c);

	mysql_close($conn);
	
	sleep(REFRESH_PERIOD);
	
	//mysql_connect
	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("connect failed: " . mysql_error());
	mysql_select_db(DB_DATABASENAME, $conn);
}
?>