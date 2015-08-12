<?php

	require_once('Village/Center.php');
	require_once('Village/Fields.php');

	echo "ALBERTO SCRIPT\n";
	//set POST variables
	$urlVistaExterior = 'http://ts5.travian.net/dorf1.php';
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
	curl_setopt($ch,CURLOPT_URL, $urlVistaExterior);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//curl will take care about the cookies
	curl_setopt($ch, CURLOPT_COOKIEJAR, '');

	//execute post
	$vistaExteriorHTML = curl_exec($ch);

	print $vistaExteriorHTML;
/////////////////////////////////////////////////////////////////

	$docVistaExterior = new DOMDocument();
	libxml_use_internal_errors(true);
	$docVistaExterior->loadHTML($vistaExteriorHTML);
	//echo $doc->saveXML();

	echo "ALBERTO PRUEBA 1\n";

	print "AQUI: ".$ch." AQUI\n";
	$centroAldea = new Fields($ch);
    $centroAldea->upgrade("Leñador3");
	
	

	/*

	//$addlevel = explode("'", $doc->getElementById('stockBarResource1')->getElementsByTagName('middle')->item(4)->getElementsByTagName('button')->item(0)->getAttribute('onclick'))[1];
	$madera =  $docVistaExterior->getElementById('l1')->nodeValue;
	$barro =  $docVistaExterior->getElementById('l2')->nodeValue;
	$hierro = $docVistaExterior->getElementById('l3')->nodeValue;
	$cereal =  $docVistaExterior->getElementById('l4')->nodeValue;
	$saldoCereal = $docVistaExterior->getElementById('stockBarFreeCrop')->nodeValue;

	print "Madera: ".$madera."	Barro: ".$barro."	Hierro: ".$hierro."	  Cereal: ".$cereal."   Saldo de cereal: ".$saldoCereal."\n";

	print "Exterior de la aldea: \n";

	
	//CODIGO QUE SE METE EN CADA RECURSO Y ESCRIBE SU NIVEL Y TIPO
	for ($numCampo = 1; $numCampo <= 18; $numCampo++) {
		$urlVistaExterior = 'http://ts5.travian.net/build.php?id='.$numCampo;
    	curl_setopt($ch,CURLOPT_URL, $urlVistaExterior);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		$vistaExteriorHTML = curl_exec($ch);

		$docVistaExterior = new DOMDocument();
		libxml_use_internal_errors(true);
		$docVistaExterior->loadHTML($vistaExteriorHTML);

		print "Posición: ".$numCampo. " -> ".$docVistaExterior->getElementById('content')->childNodes->item(1)->nodeValue."\n";
	}
	

	/////////////////////////////////////////////////////////

	
	//CODIGO QUE SE METE EN CADA HUECO Y ESCRIBE SU NIVEL Y LO QUE HAY
	print "Centro de la aldea: \n";

	
	for ($numCampo = 19; $numCampo <= 39; $numCampo++) {
		$urlVistaCentro = 'http://ts5.travian.net/build.php?id='.$numCampo;
    	curl_setopt($ch,CURLOPT_URL, $urlVistaCentro);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		$vistaCentroHTML = curl_exec($ch);

		$docVistaCentro = new DOMDocument();
		libxml_use_internal_errors(true);
		$docVistaCentro->loadHTML($vistaCentroHTML);

		print "Posición: ".$numCampo. " -> ".$docVistaCentro->getElementById('content')->childNodes->item(1)->nodeValue."\n";
	}
	
	
 	*/

	print "\n END \n";


	curl_close($ch);

?>
