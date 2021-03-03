<?php
header('Content-Type: application/json');
if(array_key_exists("token", $_REQUEST))
{
	$token=$_REQUEST["token"];
	if(array_key_exists("published_from", $_REQUEST))
	{
		$published_from=$_REQUEST["published_from"];
		$hash_file=md5($token.$published_from);
		shell_exec("php caller_console.php $token $hash_file '$published_from' > /dev/null 2>&1  &");
		echo json_encode(["file_id"=> $hash_file]);
	}
	else
	{
		$hash_file=md5($token);
		shell_exec("php caller_console.php $token $hash_file > /dev/null 2>&1  &");
		echo json_encode(["file_id"=> $hash_file]);
	}
}

?>