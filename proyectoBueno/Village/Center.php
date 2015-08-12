<?php
class Center
{

    //De momento consideramos que en $buildinsType todos los edificios tienen su id de construcción. Lo óptimo es qe como vamos a tener un array
    // de buildings por cada aldea, que sea un array de 21 elementos, con los edificios que se desean construir.

    // Attributes
    private $relativeUrl = 'dorf2.php';
    private $buildingsType = array(
        "Edificio principal" => 'category=1;contract=11',
        "Plaza de reuniones" => 'category=2;contract=11',
    	"Escondite" =>  'category=1;contract=11', 
    	"Almacén" =>  'category=1;contract=11',
    	"Granero" =>  'category=1;contract=11',
    	"Embajada" => 'category=1;contract=18',
    	"Mercado" => 'category=1;contract=17',
    	"Residencia" => 'category=1;contract=25',
    	"Palacio" => 'category=1;contract=26',
    	"Cantero" => 'category=1;contract=34',
    	"Tesoro" => 'category=1;contract=27',
    	"Cervecería" => 'category=1;contract=11',
    	"Ayuntamiento" => 'category=1;contract=24',
    	"Oficina de Comercio" => 'category=1;contract=28',
        "Abrevadero" => 'category=1;contract=41',
    	"Cuartel" => 'category=2;contract=19',
    	"Hogar del heroe" => 'category=2;contract=37',
    	"Academia" => 'category=2;contract=22',
    	"Herrería" => 'category=2;contract=13',
    	"Establo" => 'category=2;contract=20',
    	"Taller" => 'category=2;contract=21',
    	"Plaza de Torneos" => 'category=2;contract=14',
    	"Molino" => 'category=3;contract=8',
    	"Serrería" => 'category=3;contract=5',
    	"Ladrillar" => 'category=3;contract=6',
    	"Fundición" => 'category=3;contract=7',
    	"Panadería" => 'category=3;contract=9',
        "Muralla" => 'category=2;contract=31'
	);

	// This variable will be an array [building, slot]

    // Constructor
    function __construct($buildingsPosition) {
        $this->buildingsPosition = $buildingsPosition;
    }

    // Methods
    public function upgrade($buildingName, $ch) {
        //Obtenemos de la lista el id en el que está construido
        $id = $this->idBuilding($buildingName);

        //Cargamos el html correspondiente al menu en el que está el botón de construir edficio
        $urlVistaEdificio = 'http://ts5.travian.net/build.php?id='.$id;
        curl_setopt($ch,CURLOPT_URL, $urlVistaEdificio);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $vistaEdficioHTML = curl_exec($ch);

        $docVistaEdficio = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaEdficio->loadHTML($vistaEdficioHTML);

        //______Comprobamos si se puede subir nivel_____________
        //Iniciamos el control mirando si hay suficientes recursos.

        //Obtenemos los recursos actuales:
        $recursos[0] =  (int)$docVistaEdficio->getElementById('l1')->nodeValue;
        $recursos[1] =  (int)$docVistaEdficio->getElementById('l2')->nodeValue;
        $recursos[2] = (int)$docVistaEdficio->getElementById('l3')->nodeValue;
        $recursos[3] =  (int)$docVistaEdficio->getElementById('l4')->nodeValue;
        $recursos[4] = (int)$docVistaEdficio->getElementById('stockBarFreeCrop')->nodeValue;

        //Obtenemos los recursos que cuesta subir el edificio:
        $recursosCoste[0] =  (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $recursosCoste[1] =  (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $recursosCoste[2] = (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $recursosCoste[3] =  (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $recursosCoste[4] = (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(4)->nodeValue;


        //Comprobamos si son suficientes y en caso de no serlo salimos de la función.
        if(!(($recursos[0]-$recursosCoste[0]>=0)&&($recursos[1]-$recursosCoste[1]>=0)&&($recursos[2]-$recursosCoste[2]>=0)&&($recursos[3]-$recursosCoste[3]>=0)&&($recursos[4]-$recursosCoste[4]>=0))){
            print "No hay suficientes recursos\n";
            return -1;
        }


        //Terminamos el control mirando si hay constructores
        //NOTA: este control sirve para comprobar todo, pero para identificar el problema por el cual no se sube el edificio lo hemos separado en dos controles.
        $constructores =  explode("'",$docVistaEdficio->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles\n";
            return -1;
        }
        //_________Comprobación de si se puede subir nivel terminada___________
        

        //Ahora ya sabemos que existe el botón de subir nivel, accedemos a él y lo pinchamos:
        $aux = explode("'",$docVistaEdficio->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n");
        $upgrade = 'http://ts5.travian.net/'.$aux[1];
        curl_setopt($ch,CURLOPT_URL, $upgrade);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        return 0;
    }

    public function build($buildingName, $ch) {
        //Obtenemos de la lista el id en el que está construido
        $id = $this->idBuilding($buildingName);

        if($id==39){
            return $this->buildPlazaReuniones($ch);
        }

        if($id==40){
            return $this->buildMuralla($ch);
        }

        //Obtenemos la categoría del edificio:
        $category = $this->categoryBuilding($buildingName);

        //Obtenemos el contract_building del edificio, que identifica el id del html al que queremos acceder
        $contract = "contract_building".$this->contractBuilding($buildingName);

        
        //Cargamos el html correspondiente al menu en el que está el botón de construir edficio
        $urlVistaConstruccion = 'http://ts5.travian.net/build.php?id='.$id."&category=".$category;
        curl_setopt($ch,CURLOPT_URL, $urlVistaConstruccion);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $vistaConstruccionHTML = curl_exec($ch);

        $docVistaConstruccion = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaConstruccion->loadHTML($vistaConstruccionHTML);

        //______Comprobamos si se puede subir nivel_____________
        //Iniciamos el control mirando si hay suficientes recursos.

        //Obtenemos los recursos actuales:
        $recursos[0] =  (int)$docVistaConstruccion->getElementById('l1')->nodeValue;
        $recursos[1] =  (int)$docVistaConstruccion->getElementById('l2')->nodeValue;
        $recursos[2] = (int)$docVistaConstruccion->getElementById('l3')->nodeValue;
        $recursos[3] =  (int)$docVistaConstruccion->getElementById('l4')->nodeValue;
        $recursos[4] = (int)$docVistaConstruccion->getElementById('stockBarFreeCrop')->nodeValue;

        //Obtenemos los recursos que cuesta subir el edificio:
        $recursosCoste[0] =  (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $recursosCoste[1] =  (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $recursosCoste[2] = (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $recursosCoste[3] =  (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $recursosCoste[4] = (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(4)->nodeValue;

        //Comprobamos si son suficientes y en caso de no serlo salimos de la función.
        if(!(($recursos[0]-$recursosCoste[0]>=0)&&($recursos[1]-$recursosCoste[1]>=0)&&($recursos[2]-$recursosCoste[2]>=0)&&($recursos[3]-$recursosCoste[3]>=0)&&($recursos[4]-$recursosCoste[4]>=0))){
            print "No hay suficientes recursos\n";
            return -1;
        }
        

        //Terminamos el control mirando si hay constructores
        //NOTA: este control sirve para comprobar todo, pero para identificar el problema por el cual no se sube el edificio lo hemos separado en dos controles.
        $constructores =  explode("'",$docVistaConstruccion->getElementById($contract)->childNodes->item(5)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles\n";
            return -1;
        }
        //_________Comprobación de si se puede subir nivel terminada___________
        
        

        //Ahora ya sabemos que existe el botón de construir, accedemos a él y lo pinchamos:
        $aux = explode("'",$docVistaConstruccion->getElementById($contract)->childNodes->item(5)->childNodes->item(0)->getAttribute('onclick')."\n");
        $build = 'http://ts5.travian.net/'.$aux[1];        
        
        curl_setopt($ch,CURLOPT_URL, $build);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        return 0;
    }

    //Estas dos funciones seran necesarias para construir una aldea desde 0.
    
    public function buildMuralla($ch){
        //Cargamos el html correspondiente al menu en el que está el botón de construir edficio
        $urlVistaConstruccion = 'http://ts5.travian.net/build.php?id=40';
        curl_setopt($ch,CURLOPT_URL, $urlVistaConstruccion);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $vistaConstruccionHTML = curl_exec($ch);

        $docVistaConstruccion = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaConstruccion->loadHTML($vistaConstruccionHTML);

        //______Comprobamos si se puede subir nivel_____________
        //Iniciamos el control mirando si hay suficientes recursos.

         print (int)$docVistaConstruccion->getElementById('l1')->nodeValue;

         
        //Obtenemos los recursos actuales:
        $recursos[0] =  (int)$docVistaConstruccion->getElementById('l1')->nodeValue;
        $recursos[1] =  (int)$docVistaConstruccion->getElementById('l2')->nodeValue;
        $recursos[2] = (int)$docVistaConstruccion->getElementById('l3')->nodeValue;
        $recursos[3] =  (int)$docVistaConstruccion->getElementById('l4')->nodeValue;
        $recursos[4] = (int)$docVistaConstruccion->getElementById('stockBarFreeCrop')->nodeValue;

        //Obtenemos los recursos que cuesta subir el edificio:
        $recursosCoste[0] =  (int)$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(3)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $recursosCoste[1] =  (int)$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(3)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $recursosCoste[2] = (int)$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(3)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $recursosCoste[3] =  (int)$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(3)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $recursosCoste[4] = (int)$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(3)->childNodes->item(0)->childNodes->item(4)->nodeValue;

        //Comprobamos si son suficientes y en caso de no serlo salimos de la función.
        if(!(($recursos[0]-$recursosCoste[0]>=0)&&($recursos[1]-$recursosCoste[1]>=0)&&($recursos[2]-$recursosCoste[2]>=0)&&($recursos[3]-$recursosCoste[3]>=0)&&($recursos[4]-$recursosCoste[4]>=0))){
            print "No hay suficientes recursos\n";
            return -1;
        }
        

        //Terminamos el control mirando si hay constructores
        //NOTA: este control sirve para comprobar todo, pero para identificar el problema por el cual no se sube el edificio lo hemos separado en dos controles.
        $constructores =  explode("'",$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(5)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles\n";
            return -1;
        }
        //_________Comprobación de si se puede subir nivel terminada___________
        
        

        //Ahora ya sabemos que existe el botón de construir, accedemos a él y lo pinchamos:
        $aux = explode("'",$docVistaConstruccion->getElementById("contract_building31")->childNodes->item(5)->childNodes->item(0)->getAttribute('onclick')."\n");
        $build = 'http://ts5.travian.net/'.$aux[1];        
        
        curl_setopt($ch,CURLOPT_URL, $build);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    
        return 0;
    }

    public function buildPlazaReuniones($ch){
        print "Función buildPlazaReuniones sin implementar\n";
        return -1;
    }



    

    private function idBuilding($buildingName){
        $array = $this->buildingsPosition[$buildingName];  
        $array2 = explode('=',$array);
        return (int)$array2[1];
    }

    private function categoryBuilding($buildingName){
        $array = explode(';',$this->buildingsType[$buildingName]);
        $array2 = explode('=',$array[0]);
        return (int)$array2[1];
    }

    private function contractBuilding($buildingName){
        $array = explode(';',$this->buildingsType[$buildingName]);
        $array2 = explode('=',$array[1]);
        return (int)$array2[1];
    }


}
?>