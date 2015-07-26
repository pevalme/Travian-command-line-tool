<?php

	echo "Let's start the Travian script\n";
	//set POST variables
	$url = 'http://ts5.travian.net/dorf1.php';
	$fields =

	//url-ify the data for the POST
	$fields_string = '';
	foreach($fields as $key=>$value) {
		$fields_string .= $key.'='.$value.'&';
	}
	$fields_string = rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//curl will take care about the cookies
	curl_setopt($ch, CURLOPT_COOKIEJAR, '');

	//execute post
	$result = curl_exec($ch);


	//$ch2 = curl_init();
	curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/build.php?id=2');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	$result2 = curl_exec($ch);
	//echo $result2;

	$doc = new DOMDocument();
	$doc->loadHTML($result2);
	$addlevel = explode("'", $doc->getElementById('contract')->getElementsByTagName('div')->item(4)->getElementsByTagName('button')->item(0)->getAttribute('onclick'))[1];
	echo $addlevel;
	echo "\n";

	curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/'.$addlevel);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);

	//echo $result;



	//close connection
	curl_close($ch);

?>
