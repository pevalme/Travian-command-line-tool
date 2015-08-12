<?php
class Fields
{
    // Attributes
    private $relativeUrl = 'dorf1.php';
    private $buildingsType = array(
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
        "Granja6" => 'id=15',
    );

    // This variable will be initialize on running time
	public $currentBuildings = NULL;
	// This variable will be an array [building, slot]

    // Constructor
    function __construct($ch) {
       print "Initializing village's center\n";
       $this->ch=$ch;
    }

    // Methods
    public function upgrade($buildingName) {
        print "AQUI: ".$this->ch." AQUI\n";

        //Obtenemos de la lista el id en el que está construido.
        $id = $this->idBuilding($buildingName);
        
        //Cargamos el html que aparece al pinchar sobre el edificio.
        $urlVistaRecurso = 'http://ts5.travian.net/build.php?id='.$id;
        curl_setopt($this->ch,CURLOPT_URL, $urlVistaRecurso);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $vistaRecursoHTML = curl_exec($this->ch);

        $docVistaRecurso = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaRecurso->loadHTML($vistaRecursoHTML);

        //______Comprobamos si se puede subir nivel_____________
        //Iniciamos el control mirando si hay suficientes recursos.

        //Obtenemos los recursos actuales:
        $recursos[0] =  (int)$docVistaRecurso->getElementById('l1')->nodeValue;
        $recursos[1] =  (int)$docVistaRecurso->getElementById('l2')->nodeValue;
        $recursos[2] = (int)$docVistaRecurso->getElementById('l3')->nodeValue;
        $recursos[3] =  (int)$docVistaRecurso->getElementById('l4')->nodeValue;
        $recursos[4] = (int)$docVistaRecurso->getElementById('stockBarFreeCrop')->nodeValue;

        //Obtenemos los recursos que cuesta subir el edificio:
        $recursosCoste[0] =  (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $recursosCoste[1] =  (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $recursosCoste[2] = (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $recursosCoste[3] =  (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $recursosCoste[4] = (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(4)->nodeValue;

        //Comprobamos si son suficientes y en caso de no serlo salimos de la función.
        if(!(($recursos[0]-$recursosCoste[0]>=0)&&($recursos[1]-$recursosCoste[1]>=0)&&($recursos[2]-$recursosCoste[2]>=0)&&($recursos[3]-$recursosCoste[3]>=0)&&($recursos[4]-$recursosCoste[4]>=0))){
            print "No hay suficientes recursos\n";
            return -1;
        }

        
        //Terminamos el control mirando si hay constructores
        //NOTA: este control sirve para comprobar todo, pero para identificar el problema por el cual no se sube el edificio lo hemos separado en dos controles.
        $constructores =  explode("'",$docVistaRecurso->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles\n";
            return -1;
        }
        //_________Comprobación de si se puede subir nivel terminada___________
        

        //Ahora ya sabemos que existe el botón de subir nivel, accedemos a él y lo pinchamos:
        /*$aux = explode("'",$docVistaRecurso->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n");
        $build = 'http://ts5.travian.net/'.$aux[1];
        curl_setopt($this->ch,CURLOPT_URL, $build);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->ch);
        */
        
        
    }

    public function listData() {
        echo "listData method";
    }

    private function idBuilding($buildingName){
        $array = $this->buildingsType[$buildingName];
        $array2 = explode('=',$array);
        return (int)$array2[1];
    }
}
?>