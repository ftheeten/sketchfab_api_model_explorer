<?php
header('Content-Type: application/json');
if(array_key_exists("file_id", $_REQUEST))
{
		$root_folder="files/";
		$flag_file=$root_folder.".".$_REQUEST["file_id"];
		$data_file=$root_folder.$_REQUEST["file_id"];
		if(file_exists($flag_file))
		{
			echo json_encode(["status"=>"pending"]);
		}
		elseif(file_exists($data_file))
		{
			echo json_encode(["status"=>"available"]);
		}
		else
		{
			echo json_encode(["status"=>"error"]);
		}
		
}

?>