<?php

$mysql_hostname = "mysql.kathabimb.com";
$mysql_user = "kathabimb";
$mysql_password = "vesit!!roxx";
$mysql_database = "kathabimb1";

$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Opps some thing went wrong");
mysql_select_db($mysql_database, $bd) or die("Opps some thing went wrong");

?>