<?php

require_once('Village/Village.php');


class Account
{

    // Constructor: Inicializa aldea y conexión.
    function __construct() {

        //Orden de construccion, basado en:
        // http://www.browsergamesforum.com.ar/guia-para-una-rapida-construccion-de-tus-aldeas-en-travian-t1103.html
        $automatizadorFuera1 = array(
            "Leñador4",  //
            "Hierro3",
            "Hierro4",
            );
        //Se deberían quedar minas de leñador al 2 minimo y minas de hierro todas al 1.

        $automatizadorCentro1 = array(
            "Subir-Escondite",  //
            "Subir-Escondite",
            "Subir-Escondite",
            );
        //Se deberían quedar escondite al 7

        //Inicializamos las aldeas que tenemos actualmente.
        //______ALDEA 0______
        $buildingsPositionFields[0] = array(
        "Leñador1" => 'id=1',
        "Leñador2" => 'id=3',
        "Leñador3" =>  'id=14', 
        "Leñador4" =>  'id=17',
        "Barrera1" =>  'id=5',
        "Barrera2" => 'id=6',
        "Barrera3" => 'id=16',
        "Barrera4" => 'id=18',
        "Hierro1" => 'id=4',
        "Hierro2" => 'id=7',
        "Hierro3" => 'id=10',
        "Hierro4" => 'id=11',
        "Granja1" => 'id=8',
        "Granja2" => 'id=9',
        "Granja3" => 'id=13',
        "Granja4" => 'id=12',
        "Granja5" => 'id=2',
        "Granja6" => 'id=15'
        );

        $buildingsPositionCenter[0] = array(
        "Edificio principal" => 'id=19',
        "Plaza de reuniones" => 'id=39',
        "Escondite" =>  'id=20', 
        "Almacén" =>  'id=21',
        "Granero" =>  'id=22',
        "Embajada" => 'id=23',
        "Mercado" => 'id=24',
        "Residencia" => 'id=25',
        "Tesoro" => 'id=28',
        "Ayuntamiento" => 'id=30',
        "Oficina de Comercio" => 'id=31',
        "Cuartel" => 'id=33',
        "Hogar del heroe" => '34',
        "Academia" => 'id=35',
        "Herrería" => 'id=36',
        "Establo" => 'id=37',
        "Molino" => 'id=26',
        "Serrería" => 'id=27',
        "Ladrillar" => 'id=29',
        "Fundición" => 'id=32',
        "Panadería" => 'id=38',
        "Muralla" => 'id=40' 
        );

        $this->villages[0] = new Village($buildingsPositionCenter[0], $buildingsPositionFields[0]);

       

        //Inicializamos conexión.
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
        $this->ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($this->ch,CURLOPT_URL, $url);
        curl_setopt($this->ch,CURLOPT_POST, count($fields));
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch,CURLOPT_POSTFIELDS, $fields_string);

        //curl will take care about the cookies
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');

        //execute post
        $result = curl_exec($this->ch);

        // TODO.
        // Look for the list of villages and their url on the $result
        // Once we have it, call several times to new Village() by grouping the results in an array
   	}

    // Methods

    public function buildCenter($buildingName, $numAldea){
        return $this->villages[$numAldea]->buildCenter($buildingName, $this->ch);
    }

    public function upgradeCenter($buildingName, $numAldea){
        return $this->villages[$numAldea]->upgradeCenter($buildingName, $this->ch);;
    }

    public function upgradeField($buildingName, $numAldea){
        return $this->villages[$numAldea]->upgradeField($buildingName, $this->ch); ;
    }

    public function closeConnection(){
        curl_close($this->ch);
    }

    public function ia();
        $espera = 30;

        while(1){
            
        }

}
?>