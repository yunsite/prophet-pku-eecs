<?php
/**
 * Config for database
 * 
 * @author Taotaotheripper <taotaotheripper@gmail.com>
 */
header('Content-Type: text/html; charset=UTF-8');

/*
 * Configuration for the names of the database
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DATABASENAME', 'Weibo_DB');
define('DB_TABLENAME', 'weiboInfo');
$DBCOLARRAY = array('w_id', 'time', 'text', 'u_id', 'u_name', 'rep_cnt', 'cmt_cnt', 'att_cnt');

/*
 * Configuration for data fetching
 */
define('EXPIRE_TIME', 1); //Days

/*
 * Period suggested: 120-180 seconds
 * Too frequently access may cause 'user requests out of rate limit'.
 * Average api calling each period = RATE_LIMIT / (3600 / REFRESH_PERIOD)
 * Homeline calling each period = 1-2 (some extra requests)
 * Max number of statuses can be updated each period = (RATE_LIMIT / (3600 / REFRESH_PERIOD) - 2) * 100
 *
 * Which means:
 *		Higher refresh frequnt => less statues updated
 *		Lower refresh frequnt => more statues updated
 */
define('RATE_LIMIT', 150); //Times / hour
define('REFRESH_PERIOD', 180); //Seconds
define('COUNT_LIMIT', ((RATE_LIMIT / (3600 / REFRESH_PERIOD)) - 2) * 100);

/*
 * COUNT_LIMIT: Only the latest COUNT_LIMIT statuses will be updated.
 * EXPIRE_TIME: Only the latest statues before in EXPIRE_TIME days will be stored.
 */
?>
