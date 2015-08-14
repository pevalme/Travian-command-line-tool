<?php

require_once('Village/Village.php');


class Account
{

    // Constructor: Inicializa aldea y conexión.
    function __construct() {

        $this->indices = null;

        //Orden de construccion, basado en:
        // http://www.browsergamesforum.com.ar/guia-para-una-rapida-construccion-de-tus-aldeas-en-travian-t1103.html
        $this->automatizadorFuera1 = array(
            "Granja1",
            "Granja4",
            "Granja5",
            "Granja6",
            "Barrera1",
            "Barrera2"
            );

        $this->automatizadorCentro1 = array(
            "subir-Escondite",
            "subir-Escondite"
            );

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
        "Escondite" =>  'id=21', 
        "Almacén" =>  'id=20',
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

       
        //open connection
        $this->ch = curl_init();
   	}

    // Methods

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

    public function iniciarIndices(){
        //Inicializamos indices
        $this->indices = $this->obtenerIndices();
    }

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

    public function ia(){

        $this->iniciarIndices();

        while(1){
            for($numAldea = 0; $numAldea < count($this->indices); $numAldea++){
                //Miramos si hay ordenes de constrruccion definidas:
                if ($this->indices[$numAldea][0] < count($this->automatizadorFuera1)){
                    //Miramos si se puede subir de nivel algún recurso y lo subimos si se puede.
                    if ($this->upgradeField($this->automatizadorFuera1[$this->indices[$numAldea][0]], $numAldea) == 0){
                        print "Hemos subido de nivel el ".$this->automatizadorFuera1[$this->indices[$numAldea][0]]." a las ";
                        print date("Y-m-d H:i:s"); 
                        print " en la aldea ".$numAldea.".\n";
                        $this->indices[$numAldea][0]++;
                    }
                }else{
                    print "No hay ordenes de construcción definidas en el exterior de la aldea ".$numAldea.".\n";
                }


                //Miramos si hay ordenes de constrruccion definidas:
                if ($this->indices[$numAldea][0] < count($this->automatizadorFuera1)){
                    //Miramos si en el centro toca consturir o subir de nivel
                    $accion = $this->obtenerAccion($this->automatizadorCentro1[$this->indices[$numAldea][1]]);
                    $edificio = $this->obtenerEdificio($this->automatizadorCentro1[$this->indices[$numAldea][1]]);

                    //Miramos si se puede realizar esa accion y si se puede la realizamos.
                    if($accion == 0){
                        if ($this->buildCenter($edificio, $numAldea) == 0){
                            print "Hemos construido el ".$edificio." a las ";
                            print date("Y-m-d H:i:s"); 
                            print " en la aldea ".$numAldea.".\n";
                            $this->indices[$numAldea][1]++;
                        }

                    }else{
                        if ($this->upgradeCenter($edificio, $numAldea) == 0){
                            print "Hemos subido de nivel el ".$edificio." a las ";
                            print date("Y-m-d H:i:s"); 
                            print " en la aldea ".$numAldea.".\n";
                            $this->indices[$numAldea][1]++;
                        }
                    }
                }else{
                    print "No hay ordenes de construcción definidas en el centro de la aldea ".$numAldea.".\n";
                }

                $numAldea++;
            }

            $this->guardarIndices();

            print "Turno: ";
            print date("Y-m-d H:i:s"); 
            print ".\n";

            $espera = rand(150,600);
            print ("Siguiente ejecución en ".$espera." segundos.\n\n");
            sleep($espera);
        }
    }

    private function obtenerAccion($cadena){
        $orden = explode("-",$cadena);

        if(strcmp($cadena[0] , "construir")==0){
            return 0; //construir
        }
        return 1; //subir
    }

    private function obtenerEdificio($cadena){
        $edificio = explode("-",$cadena);
        return $edificio[1];
    }

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

    public function obtenerIndices(){
        
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

    public function guardarIndices(){
        $cadena = "";
        $numAldea = 0;

        foreach ($this->indices as $indice){
            if($numAldea > 0){
                $cadena = $cadena."_";
            }
            $cadena = $cadena.$numAldea.":".$indice[0].":".$indice[1];
            $numAldea++;
        }

        $this->escribirNota($cadena);

    }
}
?>