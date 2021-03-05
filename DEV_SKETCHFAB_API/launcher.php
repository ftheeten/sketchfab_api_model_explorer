<?php
 session_start();
  $session_id=session_id();
header('Content-Type: application/json');
if(array_key_exists("token", $_REQUEST))
{
	$token=$_REQUEST["token"];
	$date_from="";
	$date_to="";
	if(array_key_exists("published_from", $_REQUEST))
	{
		$published_from=$_REQUEST["published_from"];
		
	}
	if(array_key_exists("published_to", $_REQUEST))
	{
		$published_to=$_REQUEST["published_to"];		
	}
	$hash_file= md5($session_id);
	shell_exec("php caller_console.php $token $hash_file '$published_from' '$published_to' > /dev/null 2>&1  &");
	echo json_encode(["file_id"=>$hash_file]);
	session_destroy();
    session_commit();
}

?>