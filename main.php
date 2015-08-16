<?php

	require_once('Account.php');

	echo "INICIO\n";

	$cuenta = new Account();


	//______ PROBANDO LOS METODOS DE SUBIR Y CONSTRUIR EDIFICIOS _____________
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

	/*
	$edificio = "Barrera3";
	$numAldea = 0;

	//print "La salida de contruir ".$edificio." en aldea ".$numAldea." es: ".$cuenta->buildCenter($edificio, $numAldea). "\n";
	//print "La salida de subir ".$edificio." en aldea ".$numAldea." es: ".$cuenta->upgradeCenter($edificio, $numAldea). "\n";
	//print "La salida de subir ".$edificio." en aldea ".$numAldea." es: ".$cuenta->upgradeField($edificio, $numAldea). "\n";
	//__________________________________________________________________________
	*/




	/*
	//_______ PROBANDO EL METODO DE AUTOMATIZAR LA SUBIDA DE EDFICIAS, VERSION SENCILLA __________________

	if ($cuenta->ia()==0){
		print "FIN DEL AUTOMATIZADOR \n";
	}
	//____________________________________________________________________________________________________
	*/

	//print $cuenta->leerNota();

	$cuenta->ia();

	$cuenta->closeConnection();
    echo "FIN\n";


    /*
		Subimos a nivel 3 un edificio que está en nivel 1, lo sube al 2 y no cambia indice.
		Subimos a nivel 2 un edificio que no esta construido y lo construye sin incrementar el indice.
		Subimos a nivel 2 edificio que esta al 4. Se ignora la accion. PETA SI EL EDIFICIO QUE HEMOS QUERIDO SUBIR ESTÁ YA AL MAXIMO.
    */

?>
