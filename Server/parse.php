<?php

$url= $_SERVER["REQUEST_URI"];

$url_split = explode("?", $url);



if(count($url_split) > 1){

	$table = $url_split[1];
	echo($table."<br>");

	if(count($url_split) > 2){

		$constr = explode("&", $url_split[2]);
		echo(count($constr)."<br>");

		for($i = 0; $i < count($constr); $i++){

			echo ($constr[$i]."<br>");

		}
	}
}


	



?>