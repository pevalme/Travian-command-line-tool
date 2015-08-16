<?php

	echo "Let's start the Travian script\n";
	//set POST variables
	$url = 'http://ts5.travian.net/dorf1.php';
	$fields = array(
                    'name' => urlencode('Digimon'),
                    'password' => urlencode('noirerve'),
                    'lowRes' => urlencode('1'),
                    'w' => urlencode(''),
                    'login' => urlencode('1437574738')
                    );

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
/////////////////////////////////////////////////////////////////
// Code to upgrade a farm
//	//$ch2 = curl_init();
//	curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/build.php?id=2');
//	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//	$result2 = curl_exec($ch);
//	//echo $result2;
//
//	$doc = new DOMDocument();
//	$doc->loadHTML($result2);
//	$addlevel = explode("'", $doc->getElementById('contract')->getElementsByTagName('div')->item(4)->getElementsByTagName('button')->item(0)->getAttribute('onclick'))[1];
//	echo $addlevel;
//	echo "\n";
//
//	curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/'.$addlevel);
//	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//	$result = curl_exec($ch);

	//echo $result;
//////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
// Code to show available adventures
//    $relativeUrl = 'hero_adventure.php';
//	$adventureList = array();
//
//	print "Initializing hero's adventure data\n";
//
//    curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/' . $relativeUrl);
//    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//    $result = curl_exec($ch);
//
//    $doc = new DomDocument();
//    $doc->loadHTML($result);
//    $adventures = $doc->getElementById('adventureListForm')->getElementsByTagName('tr');
//
//    // The first element will be the title of the table so we have to skip it
//    $firstElement = true;
//    foreach ($adventures as $i => $adv) {
//        if($firstElement){
//            $firstElement = false;
//        } else {
//        	$fields = array();
//
//        	// Add the link needed to go to this adventure
//            $fields['href'] = $adv->getElementsByTagName('a')->item(1)->getAttribute('href');
//
//            // Getting hidden fields
//            foreach ($adv->getElementsByTagName('input') as $j => $input){
//                $fields[$input->getAttribute('name')]=$input->getAttribute('value');
//            }
//
//            $adventureList[trim($adv->getElementsByTagName('td')->item(2)->childNodes->item(0)->nodeValue) . ' <=> ' . $adv->getElementsByTagName('td')->item(4)->childNodes->item(1)->nodeValue] = $fields;
//        }
//    }
//
//    $counter = 0;
//    asort($adventureList);
//    foreach($adventureList as $time => $adv){
//        echo "Aventura ". $counter++ . " encontrada a: " . $time;
//        //foreach($adv as $index => $data){
//        //    echo "[".$index."]=".$data;
//        //}
//        print "\n";
//    }
//////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
// Code to start an adventure
//    $position = 0;
//
//    echo $adventureList[array_keys($adventureList)[$position]]['href'];
//    echo "\n";
//	curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/' . $adventureList[array_keys($adventureList)[$position]]['href']);
//    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//
//    // Acces to the $position adventure
//    $result = curl_exec($ch);
//
//    //print $result;
//    $doc = new DomDocument();
//    $doc->loadHTML($result);
//
//    // Get the form that we have to submit to send the hero to the adventure
//    $action = $doc->getElementsByTagName('form')[0]->getAttribute('action');
//
//    // Get the inputs of the form
//    $inputs = $doc->getElementsByTagName('input');
//    $fields = array();
//
//    foreach($inputs as $i => $data){
//        $fields[$data->getAttribute('name')]=$data->getAttribute('value');
//    }
//    $fields_string = '';
//    for ($x = 0; $x < count($adventureList); $x++) {
//    	$firstElement = true;
//		foreach($adventureList[array_keys($adventureList)[$x]] as $key=>$value) {
//			if($firstElement){
//				$firstElement = False;
//			}else{
//				$fields_string .= $key.'='.urlencode($value).'&';
//				echo $key . "  " . $value . " \n";
//			}
//		}
//	}
//	$fields_string = rtrim($fields_string, '&');
//    $fields_string = '';
//    foreach($fields as $key=>$value) {
//        $fields_string .= $key.'='.urlencode($value).'&';
//    }
//    $fields_string = rtrim($fields_string, '&');
//
//    curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/' . $action);
//    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//	curl_setopt($ch,CURLOPT_POST, count($fields));
//	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
//
//    $result = curl_exec($ch);
//    //print $result;
//	//close connection
//	curl_close($ch);


//////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
// Code to send attack
//    $relativeUrl = "build.php?id=39&tt=2"; // Access to "plaza de torneos" an move to "enviar tropas"
//    curl_setopt($ch,CURLOPT_URL, 'http://ts5.travian.net/' . $relativeUrl);
//    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//    $result = curl_exec($ch);
//
//    $doc = new DomDocument();
//    $doc->loadHTML($result);
//
//    $action = $doc->getElementsByTagName('form')[0]->getAttribute('action');
//
//    $inputs = $doc->getElementsByTagName('input');
//
//    foreach($inputs as $index => $data){
//        echo $data->getAttribute('name');
//        echo "\n";
//    }
//    echo $inputs->length;
//    echo "\n";
?>
