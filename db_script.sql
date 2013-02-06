delimiter //

set names utf8 //

create database if not exists Weibo_DB default CHARSET=utf8 //

use Weibo_DB //

# weiboInfo
# Use index when needed
create table if not exists weiboInfo (w_id varchar(50) not null, time datetime, text varchar(300), 
	u_id varchar(20), u_name varchar(40), rep_cnt int, cmt_cnt int, att_cnt int, primary key(w_id)) default CHARSET=utf8 //

delimiter ;
