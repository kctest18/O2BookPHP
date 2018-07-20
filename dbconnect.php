<?php
require_once 'config.php';
try
{
	$dbLink = new PDO(DB_DRIVER . ":dbname=" . DB_DATABASE . ";host=" . DB_SERVER, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	date_default_timezone_set("Asia/Taipei");
}
catch( PDOException $Exception )
{
	die($Exception->getMessage());
}
?>