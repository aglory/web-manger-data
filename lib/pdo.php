<?php
if(!defined('Execute')){ exit();}
$pdomysql = new PDO('mysql:host=192.168.21.127;dbname=test;charset=utf8','root','root',array(PDO::MYSQL_ATTR_FOUND_ROWS => true,PDO::ATTR_STRINGIFY_FETCHES => false,PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
?>