<?php
	header('Content-Type: application/json');
	$root_url="https://api.sketchfab.com/v3/me/models?";
	$i=0;
	$results=Array();
	$root_folder="files/";
	
	
	function create_flag_file($flag_file)
	{
		$returned=false;
		
		global $root_folder;
		
		$flag_file=$root_folder.".".$flag_file;
		if(!file_exists($flag_file))
		{
			$flag=fopen($flag_file, "w");
			fclose($flag);
			$returned=true;
		}
		return $returned;
	}
	
	function launch_sketchfab($url, $params, $i, $date=null)
	{
		
		global $results;
		
		if(array_key_exists("token", $params))
		{
			
			$url = sprintf("%s?%s", $url, http_build_query($params));
			$headers = [
				'Accept-Charset : utf-8',
				'Content-Type : application/json',
				'Authorization : Token '.$params["token"],
			];

			
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$server_output = curl_exec ($ch);

			curl_close ($ch);
			
			$elem=  json_decode($server_output, true);
			if(isset($elem))
			{
				if(array_key_exists("results", $elem))
				{
					foreach($elem["results"] as $record)
					{
						$go=true;
						if(isset($date))
						{
							if($record["publishedAt"]<$date)
							{
								$go=false;
							}
						}
						if($go)
						{
							$line=Array();
							#print(record["name"])
							$line["name"]=$record["name"];
							$line["uid"]=$record["uid"];
							$line["uri"]=$record["uri"];
							$categ=Array();
							foreach($record["categories"] as $item)
							{
								$categ[]=$item["name"];
							}
							$line["categories"]=implode(";", $categ);
							$line["description"]=$record["description"];
							$line["vertexCount"]=$record["vertexCount"];
							$line["faceCount"]=$record["faceCount"];
							$line["viewerUrl"]=$record["viewerUrl"];
							$line["viewCount"]=$record["viewCount"];
							$line["likeCount"]=$record["likeCount"];
							$line["isPrivate"]=$record["isPrivate"];
							$line["createdAt"]=$record["createdAt"];
							$line["publishedAt"]=$record["publishedAt"];
							foreach($line as $key=>$item)
							{
								$line[$key]=str_replace("\n","", str_replace("\r", "", $item));
							}
							$results[]=$line;
						}
					}
				}
			
				//print_r($elem);
				$json=json_encode($elem);
				if(array_key_exists("next", $elem ))
				{
					$next=$elem["next"];
					$i++;
					launch_sketchfab($next, ["token"=>$params["token"]] ,$i, $date);
				}
			}
			//print(json_encode($elem));
			
		
		}
	}
	
	function write_file($file)
	{
		global $root_folder;
		global $results;
		$flag_file=$root_folder.".".$file;
		$file=fopen($root_folder.$file,"w");
		$i=0;
		foreach($results as $line)
		{
			if($i==0)
			{
				fwrite($file, implode("\t", array_keys($line)));
			}
			fwrite($file, "\n");
			//print_r(array_values($line));
			fwrite($file, implode("\t", array_values($line)));
			$i++;
		}
		fclose($file);
		unlink($flag_file);		
	}

    if(count($argv)>=3)
	{
		$token=$argv[1];
		$file_hash=$argv[2];
		$date=null;
		if(count($argv)>=4)
		{
			$date=$argv[3];		
		}

		$flag=create_flag_file($file_hash);
		if($flag)
		{
			launch_sketchfab($root_url, ["token"=> $token], $i, $date);
			write_file($file_hash);
		}
	}
?>