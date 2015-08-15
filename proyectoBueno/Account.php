<?php

require_once('Village/Village.php');


class Account
{

    // Constructor: Inicializa aldea y conexión.
    function __construct() {

        //open connection
        $this->ch = curl_init();

        //Array de aldeas a rellenar:
        $this->villages = null;
   	}

    

    /*______________ METODO PRINCIPAL: INTELIGENCIA ARTIFICIAL QUE HARÁ TODO _____________*/

    public function ia(){

        $this->iniciarSesion();
        $this->inicializarAldeas();

        while(1){

            print "___________________________________________________________________\n";
            print "_____________ Comienzo del turno: ".date("Y-m-d H:i:s")." _____________\n";


            for($numAldea = 0; $numAldea < count($this->villages); $numAldea++){

                print "\n______ Aldea: ".$this->getNameAldea($numAldea)." ______\n";

                //Miramos si hay ordenes de constrruccion definidas:
                if ($this->getIndiceFieldsAldea($numAldea) < count($this->getAutFieldsAldea($numAldea))){
                    //Miramos si se puede subir de nivel algún recurso y lo subimos si se puede.
                    if ($this->upgradeFields($this->getAutFieldsAldea($numAldea)[$this->getIndiceFieldsAldea($numAldea)], $numAldea) == 0){
                        print "Hemos subido de nivel el ".$this->getAutFieldsAldea($numAldea)[$this->getIndiceFieldsAldea($numAldea)].".\n";
                        $this->increaseIndiceFieldsAldea($numAldea);
                    }
                }else{
                    print "No hay ordenes de construcción definidas en el exterior de la aldea.\n";
                }


                //Miramos si hay ordenes de constrruccion definidas:
                if ($this->getIndiceCenterAldea($numAldea) < count($this->getAutCenterAldea($numAldea))){
                    //Miramos si en el centro toca consturir o subir de nivel
                    $accion = $this->obtenerAccion($this->getAutCenterAldea($numAldea)[$this->getIndiceCenterAldea($numAldea)]);
                    $edificio = $this->obtenerEdificio( $this->getAutCenterAldea($numAldea)[$this->getIndiceCenterAldea($numAldea)]);

                    //Miramos si se puede realizar esa accion y si se puede la realizamos.
                    if($accion == 0){
                        if ($this->buildCenter($edificio, $numAldea) == 0){
                            print "Hemos construido el ".$edificio.".\n";
                            $this->increaseIndiceCenterAldea($numAldea);
                        }

                    }else{
                        if ($this->upgradeCenter($edificio, $numAldea) == 0){
                            print "Hemos subido de nivel el ".$edificio.".\n";
                            $this->increaseIndiceCenterAldea($numAldea);
                        }
                    }
                }else{
                    print "No hay órdenes de construcción definidas en el centro de la aldea.\n";
                }

                $numAldea++;
            }

            $this->guardarIndices();

            $espera = rand(100,600);
            print "\n-Siguiente ejecución en ".$espera." segundos.\n";
            print "_______________ Fin del turno: ".date("Y-m-d H:i:s")." ________________\n";
            print "___________________________________________________________________\n";
            sleep($espera);
        }
    }

    /* _____________ METODOS AUXILIARES QUE USA LA IA ______________*/

    private function obtenerAccion($cadena){
        $orden = explode("-",$cadena);

        if(strcmp($orden[0] , "construir")==0){
            return 0; //construir
        }
        return 1; //subir
    }

    private function obtenerEdificio($cadena){
        $edificio = explode("-",$cadena);
        return $edificio[1];
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

    public function upgradeCenter($buildingName, $numAldea){
        return $this->villages[$numAldea]->upgradeCenter($buildingName, $this->ch);;
    }

    public function upgradeFields($buildingName, $numAldea){
        return $this->villages[$numAldea]->upgradeFields($buildingName, $this->ch); ;
    }



    /*____________ METODOS PARA INICIAR Y CERRAR LA CONEXIÓN ___________*/

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
            "Hierro1",
            "Hierro2"
            );

        $automatizadorCentro1 = array(
            "subir-Escondite2",
            "subir-Escondite2"
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
        "Edificio principal" => 'id=19',
        "Plaza de reuniones" => 'id=39',
        "Escondite" =>  'id=21', 
        "Almacén" =>  'id=20',
        "Granero" =>  'id=22',
        "Embajada" => 'id=23',
        "Mercado" => 'id=24',
        "Residencia" => 'id=25',
        "Escondite2" => 'id=28',
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

        //Inicializamos las aldeas.
        $this->villages[0] = new Village($buildingsPositionFields[0], $buildingsPositionCenter[0], $automatizadorFields1, $automatizadorCentro1, 'Aldea[0]', [1,-95], $indices[0], '?newdid=73786&');

    }



}
?>