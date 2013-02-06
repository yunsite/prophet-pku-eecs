<?php
/**
 * Data statistics for the client
 * @param
 * 	n: integer
 *	limit: integer hours
 *	order_by: 'rep_cnt' | 'cmt_cnt'
 * @result
 * 	{'status' : 'ok' | 'error',
		'top_statuses' : [
			{'w_id' : *,
			'time' : *,
			'text' : *,
			'u_id' : *,
			'u_name' : *,
			'rep_cnt' : *,
			'cmt_cnt' : *,
			'att_cnt' : *	//never updated
			},
			...
		]
	}
 * @author Taotaotheripper <taotaotheripper@gmail.com>
 */
header("Content-Type: text/html; charset=utf-8");

session_start();

include_once('db_cfg.php');
include_once('auth_cfg.php');
include_once('saetv2.ex.class.php');
/*
 * bind:
 * 	time_limit + count
 */
define('SB_REP', 'rep_cnt');
define('SB_CMT', 'cmt_cnt');

/*
 * Fetch statuses with top N rep_cnt or cmt_cnt.
 * $count: N
 * $limit: limit hours
 * $SORT: SB_REP or SB_CMT
 */
function top_N_statuses($conn, $count, $limit, $SORT) {
	//Format select
	$sql = sprintf("select * from %s where timediff(now(), time) <= '%s' 
		order by %s desc limit %d", DB_TABLENAME, $limit.':00:00', $SORT, $count);
	$sts = array();
	if($result = mysql_query($sql, $conn)) {
		while($item = mysql_fetch_assoc($result))
			array_push($sts, $item);
		$res = array('status' => 'ok', 'top_statuses' => $sts);
		return json_encode($res, JSON_UNESCAPED_UNICODE);
	}
	else
		return json_encode(array('status' => 'error', 'reason' => mysql_error()));
}

//Default value
$n = 10;
$limit = 24;
$order_by = SB_REP;

//Get command parameters
isset($_REQUEST['n']) and ($n = $_REQUEST['n']);
isset($_REQUEST['limit']) and ($limit = $_REQUEST['limit']);
isset($_REQUEST['order_by']) and ($order_by = $_REQUEST['order_by']);

$c = new SaeTClientV2(WB_AKEY, WB_SKEY, $_SESSION['token']['access_token']);

//mysql_connect
$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("connect failed: " . mysql_error());
mysql_select_db(DB_DATABASENAME, $conn);

print top_N_statuses($conn, $n, $limit, $order_by);

mysql_close($conn);
?>