<?php
require_once 'config.php';
$msg="";
if (!isset($_REQUEST['account']))
	$msg.=(empty($msg)?'':', ').'No account';
else
	$account=$_REQUEST['account'];
if (!isset($_REQUEST['pwd']))
	$msg.=(empty($msg)?'':', ').'No password';
else
	$account=$_REQUEST['pwd'];
if (!empty($msg))
	die($msg);
require_once 'dbconnect.php';
$login_time=time();
$ret=array();
$statement=$dbLink->prepare('select * from member where account=?');
$statement->execute(array($account));
if ($statement->rowCount()>0)
{
	$rowdata=$statement->fetch(PDO::FETCH_ASSOC);
	$statement->closeCursor();
	$statement=null;
	if ($pwd==$rowdata->pwd)
	{
		$tmpsid=date('YmdHis', $login_time);
		$dotted = preg_split( '/[.]+/', $_SERVER['REMOTE_ADDR']);
		for($i=0;$i<4;$i++)
			$tmpsid .= sprintf('%03d', intval($dotted[$i]));
		$tmpsid .= uniqid();
		ini_set('session.gc_maxlifetime', SESSION_TIME);
		session_id($tmpsid);
		session_start();
		session_register('account', $rowdata->account);
		session_register('last_access_time', $login_time);
		$ret['msg']='ok';
		$ret['session_id']=session_id();
		$statement=$dbLink->prepare('insert into login_history (account, login_time, remote_ip, session_id, last_access_time) values (?, current_timestamp, ?, ?, current_timestamp)');
		$statement->execute(array($rowdata->account, $_SERVER['REMOTE_ADDR'], session_id()));
		$statement=null;
	}
	else
		$ret['msg']="密碼錯誤";
}
else
{
	$statement->closeCursor();
	$statement=null;
	$msg="找不到這個帳號[$account]";
}
$dbLink=null;
echo(json_encode($ret));
?>