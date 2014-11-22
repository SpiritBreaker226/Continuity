<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conContinuty = "10.18.9.190";
$database_conContinuty = "cnty_web";
$username_conContinuty = "cnty_web";
$password_conContinuty = "CntYw3b";
$conContinuty = mysql_pconnect($hostname_conContinuty, $username_conContinuty, $password_conContinuty) or die(mysql_error());?>