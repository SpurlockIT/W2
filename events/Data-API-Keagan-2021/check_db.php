<?php
set_time_limit(0);

define('BASE_URL', 'http://www.spurlock.illinois.edu/db-support/');
require_once(dirname(__FILE__).'/fmREST/fmREST.php');

$database_server_ip = '128.174.89.153';
$database_server_name = 'db1.spurlock.illinois.edu';

function check_db($database){
	$start_time = time();
	global $database_server_name;
	$fm = new fmREST($database_server_name, $database, "", ""); 
	$connected = $fm -> login();
	$err_code = $connected['messages'][0]['code'];
	
	if($err_code != 0){
		return 0;
	} else {
		return 1;
	}
}
?>
