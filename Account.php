<?php

require_once('Village/Village.php');
require_once('HeroAdventure.php');

class Account
{

    private $ch;
    private $villages;
    private $hero;
    private $url;
    // Constructor: Inicializa aldea y conexión.
    function __construct($url) {
        $this->url = $url;

        //open connection
        $this->ch = curl_init();

        //Array de aldeas a rellenar:
        $this->villages = null;

        // Hero
        $this->hero = null;

   	}



    /*______________ METODO PRINCIPAL: INTELIGENCIA ARTIFICIAL QUE HARÁ TODO _____________*/

    public function ia(){

        $this->iniciarSesion();
        $this->inicializarAldeas();

        while(1){
            $this->comprobarSesion();  //METODO AUN SIN FUNCIONAR

            print "\n\n___________________________________________________________________\n";
            print "_____________ Comienzo del turno: ".date("Y-m-d H:i:s")." _____________\n";


            for($numAldea = 0; $numAldea < count($this->villages); $numAldea++){

                print "\n______ Aldea: ".$this->getNameAldea($numAldea)." ______\n";

                //Miramos si hay ordenes de constrruccion definidas:
                if ($this->getIndiceFieldsAldea($numAldea) < count($this->getAutFieldsAldea($numAldea))){
                    //Miramos qué edificio vamos a subir de nivel.
                    $level = $this->obtenerNivel($this->getAutFieldsAldea($numAldea)[$this->getIndiceFieldsAldea($numAldea)]);
                    $edificio = $this->obtenerEdificio($this->getAutFieldsAldea($numAldea)[$this->getIndiceFieldsAldea($numAldea)]);

                    //Miramos si se puede subir de nivel el edificio y lo subimos si se puede.
                    if ($this->upgradeFields($edificio, $level, $numAldea) == 0){
                        $this->increaseIndiceFieldsAldea($numAldea);
                    }
                }else{
                    print "No hay ordenes de construcción definidas en el exterior de la aldea.\n";
                }


                //Miramos si hay ordenes de constrruccion definidas:
                if ($this->getIndiceCenterAldea($numAldea) < count($this->getAutCenterAldea($numAldea))){
                    //Miramos si en el centro toca construir o subir de nivel. Y el edficio en cuestión.
                    $level = $this->obtenerNivel($this->getAutCenterAldea($numAldea)[$this->getIndiceCenterAldea($numAldea)]);
                    $edificio = $this->obtenerEdificio( $this->getAutCenterAldea($numAldea)[$this->getIndiceCenterAldea($numAldea)]);

                    //Miramos si se puede realizar esa accion y si se puede la realizamos.
                    if($level == 1){
                        if ($this->buildCenter($edificio, $numAldea) == 0){
                            $this->increaseIndiceCenterAldea($numAldea);
                        }

                    }else{
                        if ($this->upgradeCenter($edificio, $level, $numAldea) == 0){
                            $this->increaseIndiceCenterAldea($numAldea);
                        }
                    }
                }else{
                    print "No hay órdenes de construcción definidas en el centro de la aldea.\n";
                }

                $numAldea++;
            }

            $this->guardarIndices();

            $espera = rand(150,400);
            print "\n-Siguiente ejecución en ".$espera." segundos.\n";
            print "_______________ Fin del turno: ".date("Y-m-d H:i:s")." ________________\n";
            print "___________________________________________________________________\n";
            sleep($espera);
        }
    }

    /* _____________ METODOS AUXILIARES PARA MANEJAR LA INFORMACION DEL ARRAY DE AUTOMATIZACION ______________*/

    private function obtenerNivel($cadena){
        $nivel = explode("-",$cadena);
        return (int)$nivel[1];
    }

    private function obtenerEdificio($cadena){
        $edificio = explode("-",$cadena);
        return $edificio[0];
    }

    // Obtiene array doble, leyendo del bloc de notas, con lo que toca construir en cada aldea
    private function obtenerIndices(){

        $notas = $this->leerNota();

        $notasToken = explode("_",$notas);
        $index = 0;

        foreach ($notasToken as $token) {
            $indexToken = explode(":",$token);
            $indices[$index][0] = $indexToken[1];
            $indices[$index][1] = $indexToken[2];
            $index++;
        }

        return $indices;
    }

    // Guarda en el bloc de notas el array con la información de lo que toca consturir en cada aldea
    private function guardarIndices(){
        $cadena = "";
        $numAldea = 0;

        for($numAldea = 0; $numAldea < count($this->villages); $numAldea++){
            if($numAldea > 0){
                $cadena = $cadena."_";
            }
            $cadena = $cadena.$numAldea.":".$this->getIndiceFieldsAldea($numAldea).":".$this->getIndiceCenterAldea($numAldea);
            $numAldea++;
        }

        $this->escribirNota($cadena);
    }


    /* __________________ METODOS PARA LEER Y ESCRIBIR EN EL BLOC DE NOTAS _____________*/

    public function leerNota(){
        //POR SEGURIDAD: Cargamos el html correspondiente al menu que aparece al pinchar en mensajes.
        $urlNotas = 'http://ts5.travian.net/nachrichten.php';
        curl_setopt($this->ch,CURLOPT_URL, $urlNotas);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $notasHTML = curl_exec($this->ch);

        //Cargamos el html correspondiente al menu en el que están las notas
        $urlNotas = 'http://ts5.travian.net/nachrichten.php?t=4';
        curl_setopt($this->ch,CURLOPT_URL, $urlNotas);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $notasHTML = curl_exec($this->ch);

        $docNotas = new DOMDocument();
        libxml_use_internal_errors(true);
        $docNotas->loadHTML($notasHTML);

        //obtenemos lo que hay escrito en las notas:
        return (string)$docNotas->getElementById('notepad_container')->childNodes->item(5)->nodeValue;
    }

    public function escribirNota($nota){
        //POR SEGURIDAD: Cargamos el html correspondiente al menu que aparece al pinchar en mensajes.
        $urlNotas = 'http://ts5.travian.net/nachrichten.php';
        curl_setopt($this->ch,CURLOPT_URL, $urlNotas);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $notasHTML = curl_exec($this->ch);

        //Cargamos el html correspondiente al menu en el que están las notas
        $urlNotas = 'http://ts5.travian.net/nachrichten.php?t=4';
        curl_setopt($this->ch,CURLOPT_URL, $urlNotas);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $notasHTML = curl_exec($this->ch);

        //Rellenamos el formulario y lo enviamos.
        $urlNotas = 'http://ts5.travian.net/nachrichten.php';
        $fields = array(
                            't' => urlencode('4'),
                            'speichern' => urlencode('1'),
                            'notepad' => urlencode($nota),
                    );

        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');

        curl_setopt($this->ch,CURLOPT_URL, $urlNotas);
        curl_setopt($this->ch,CURLOPT_POST, count($fields));
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch,CURLOPT_POSTFIELDS, $fields_string);

        $notasHTML = curl_exec($this->ch);
    }


    /*________________ METODOS DE CONSTRUCCIÓN DE EDIFCIOS _______________*/

    public function buildCenter($buildingName, $numAldea){
        return $this->villages[$numAldea]->buildCenter($buildingName, $this->ch);
    }

    public function upgradeCenter($buildingName, $level, $numAldea){
        return $this->villages[$numAldea]->upgradeCenter($buildingName, $level, $this->ch);;
    }

    public function upgradeFields($buildingName, $level, $numAldea){
        return $this->villages[$numAldea]->upgradeFields($buildingName, $level, $this->ch); ;
    }



    /*____________ METODOS PARA INICIAR, MANTENER Y CERRAR LA CONEXIÓN ___________*/

    public function iniciarSesion(){

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

        //set the url, number of POST vars, POST data

        curl_setopt($this->ch,CURLOPT_URL, $url);
        curl_setopt($this->ch,CURLOPT_POST, count($fields));
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch,CURLOPT_POSTFIELDS, $fields_string);

        //curl will take care about the cookies
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');

        //execute post
        $result = curl_exec($this->ch);
    }

    public function comprobarSesion(){

        //Cargamos el html que aparece al pinchar sobre el edificio.
        $urlVistaPpal = 'http://ts5.travian.net/dorf1.php';
        curl_setopt($this->ch,CURLOPT_URL, $urlVistaPpal);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $vistaPpalHTML = curl_exec($this->ch);

        $docVistaPpal = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaPpal->loadHTML($vistaPpalHTML);

        //Comprobamos que estamos con la sesion 
        $estado =  explode("Login",$docVistaPpal->getElementById('content')->childNodes->item(1)->nodeValue."\n");

        //print $docVistaPpal->getElementById('content')->childNodes->item(1)->nodeValue."\n";
        //print $vistaPpalHTML;
               
        if(count($estado)==2){
            print "Se ha cerrado sesión, tratamos de volver a abrirla.\n";
            $this->iniciarSesion();
            return 0;
        }
        

    }


    public function closeConnection(){
        curl_close($this->ch);
    }




    /*________________ METODOS GETTERS Y SETTERS _______________*/

    public function getIndiceFieldsAldea($numAldea){
        return $this->villages[$numAldea]->getIndiceFields();
    }

    public function getIndiceCenterAldea($numAldea){
        return $this->villages[$numAldea]->getIndiceCenter();
    }

    public function getAutFieldsAldea($numAldea){
        return $this->villages[$numAldea]->getAutFields();
    }

    public function getAutCenterAldea($numAldea){
        return $this->villages[$numAldea]->getAutCenter();
    }

    public function increaseIndiceFieldsAldea($numAldea){
        $this->villages[$numAldea]->increaseIndiceFields();
    }

    public function increaseIndiceCenterAldea($numAldea){
        $this->villages[$numAldea]->increaseIndiceCenter();
    }

    public function getNameAldea($numAldea){
        return $this->villages[$numAldea]->getName();
    }


    /*_____________ METODO PARA INICIALIZAR LAS ALDEAS CON TODOS LOS DATOS QUE USAREMOS ___________*/

    public function inicializarAldeas(){

        //Guardamos los indices en una variable
        $indices = $this->obtenerIndices();

        //Inicializamos los automatizadores creados actualmente

        //Orden de construccion, basado en:
        // http://www.browsergamesforum.com.ar/guia-para-una-rapida-construccion-de-tus-aldeas-en-travian-t1103.html
        $automatizadorFields1 = array(
            "Hierro1-2",
            "Hierro2-2"
            );

        $automatizadorCentro1 = array(
            "Escondite-5",
            "Escondite2-5",
            "Escondite3-7",
            "Escondite4-7",
            "Escondite5-7",
            "Escondite-9",
            "Escondite2-9",
            "Escondite3-9",
            "Escondite4-9",
            "Escondite5-9"
            );


        //Inicializamos los campos de posicion de las aldeas que tenemos actualmente.
        //NOTA: La muralla debe ir en id=40 y la plaza de reuniones en id=39.
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
        "Edificio principal" => 'id=26',
        "Plaza de reuniones" => 'id=39',
        "Escondite" =>  'id=21',
        "Almacén" =>  'id=20',
        "Granero" =>  'id=19',
        "Escondite4" => 'id=22',
        "Mercado" => 'id=24',
        "Residencia" => 'id=25',
        "Escondite2" => 'id=28',
        "Escondite3" => 'id=27',
        "Escondite5" => 'id=31',
        "Cuartel" => 'id=33',
        "Hogar del heroe" => '34',
        "Academia" => 'id=35',
        "Herrería" => 'id=36',
        "Establo" => 'id=37',
        "Molino" => 'id=26',
        "Embajada" => 'id=30',
        "Ladrillar" => 'id=29',
        "Fundición" => 'id=32',
        "Panadería" => 'id=38',
        "Muralla" => 'id=40'
        );

        //Inicializamos las aldeas.
        $this->villages[0] = new Village($buildingsPositionFields[0], $buildingsPositionCenter[0], $automatizadorFields1, $automatizadorCentro1, 'Aldea[0]', [1,-95], $indices[0], '?newdid=73786&');

    }

    public function showAdventures(){
        $this->hero = new HeroAdventure($this->ch, $this->url);
        $this->hero->listData();
    }

    public function sendToAdventure($n){
        if (is_null($this->hero)){
            $this->hero = new HeroAdventure($this->ch, $this->url);
        }
        $this->hero->goToAdventure($this->ch, $this->url, $n);
    }

    public function single_attack($origin, $targetName, $targetX, $targetY, $t1, $t2, $t3, $t4, $t5, $t6, $t7, $t8, $t9, $t10, $sendHero, $typeOfAttack){

        foreach($this->villages as $index => $village){
            if ($village->getName() == $origin){
                $originIndex = $index;
            }
        }

        $this->villages[$originIndex]->attack($this->ch, $this->url, $targetName, $targetX, $targetY, $t1, $t2, $t3, $t4, $t5, $t6, $t7, $t8, $t9, $t10, $sendHero, $typeOfAttack, 1);
    }


    public function train_attack_fake($origin, $targetName, $targetX, $targetY, $numberOfWagons){

        foreach($this->villages as $index => $village){
            if ($village->getName() == $origin){
                $originIndex = $index;
            }
        }

        $this->villages[$originIndex]->attack($this->ch, $this->url, $targetName, $targetX, $targetY, "1","0","0","0","0","0","0","0","0","0","0","3",$numberOfWagons);       

    }
}
?>