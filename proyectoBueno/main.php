<?php

	require_once('Account.php');


	/*
		LEER:

		DESCRIPCIÓN: php para probar funciones de construir y subir de nivel

		INSTRUCCIONES:
		-Poner en $edificio el nombre exacto, con tildes del edificio a subir.
		-Poner en $numAldea 0 ya que solo tenemos una aldea
		-Descomentar la linea oportuna según quieras construir un eddificio o subirlo de nivel en el centro o fuera.


		OBSERVACIONES:
		-Si intentamos subir de nivel un edificio que no esta construido o construir un edificio que ya esta construido, peta.
		-Si intentamos subir de nivel un edificio que no existe, peta.
		-Si intentamos subir de nivel un edificio cuando no hay recursos o no quedan constructores, simplemente devuelve -1.
	*/

	echo "INICIO\n";

	$edificio = "Barrera3";
	$numAldea = 0;

	$cuenta = new Account();

	//print "La salida de contruir ".$edificio." en aldea ".$numAldea." es: ".$cuenta->buildCenter($edificio, $numAldea). "\n";
	//print "La salida de subir ".$edificio." en aldea ".$numAldea." es: ".$cuenta->upgradeCenter($edificio, $numAldea). "\n";
	print "La salida de subir ".$edificio." en aldea ".$numAldea." es: ".$cuenta->upgradeField($edificio, $numAldea). "\n";

    echo "FIN\n";
	

	$cuenta->closeConnection();

?>
