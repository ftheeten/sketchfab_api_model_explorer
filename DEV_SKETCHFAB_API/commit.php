<?php
header('Content-Type: application/json');
	if(array_key_exists("id",$_REQUEST))
	{
		$root_folder="files/";
		$id=$_REQUEST['id'];
		unlink($root_folder.$id);
		echo json_encode(["file_id"=> $id]);
	}
?>