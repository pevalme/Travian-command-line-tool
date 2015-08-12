<?php
class Center
{

    //De momento consideramos que en $buildinsType todos los edificios tienen su id de construcción. Lo óptimo es qe como vamos a tener un array
    // de buildings por cada aldea, que sea un array de 21 elementos, con los edificios que se desean construir.

    // Attributes
    private $relativeUrl = 'dorf2.php';
    private $buildingsType = array(
        "Edificio principal" => 'category=1;id=26',
        "Plaza de reuniones" => 'category=2;id=39',
    	"Escondite" =>  'category=1;id=21', 
    	"Almacén" =>  'category=1;id=20',
    	"Granero" =>  'category=1;id=19;contract=11',
    	"Embajada" => 'category=1;id=30;contract=18',
    	"Mercado" => 'category=1;id=30',
    	"Residencia" => 'category=1;id=30',
    	"Palacio" => 'category=1;id=30',
    	"Cantero" => 'category=1;id=30',
    	"Tesoro" => 'category=1;id=30',
    	"Cervecería" => 'category=1;id=30',
    	"Ayuntamiento" => 'category=1;id=30',
    	"Oficina de Comercio" => 'category=1;id=30',
    	"Terraplén" => 'category=2;id=30',
    	"Cuartel" => 'category=2;id=30',
    	"Hogar del héroe" => 'category=2;id=30',
    	"Academia" => 'category=2;id=30',
    	"Herrería" => 'category=2;id=30',
    	"Establo" => 'category=2;id=30',
    	"Taller" => 'category=2;id=30',
    	"Plaza de Torneos" => 'category=2;id=30',
    	"Molino" => 'category=3;id=30',
    	"Serrería" => 'category=3;id=30',
    	"Ladrillar" => 'category=3;id=30',
    	"Fundición" => 'category=3;id=30',
    	"Panadería" => 'category=3;id=30',
        "Muralla" => 'category=2;id=24;contract=31' //a la muralla hay que ponerle un id que este libre en el momento de contruir, o eso o hacer un funcion 
                                                    //especial para construir muralla y plaza de reuniones.
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
        echo "upgrade method\n";
        print "Tratamos de subir el edificio ".$buildingName."\n";

        //Obtenemos de la lista el id en el que está construido
        $id = $this->idBuilding($buildingName);
        print "El id que tenemos es: ".$id."\n";

        //Cargamos el html correspondiente al menu en el que está el botón de construir edficio
        $urlVistaEdificio = 'http://ts5.travian.net/build.php?id='.$id;
        curl_setopt($this->ch,CURLOPT_URL, $urlVistaEdificio);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $vistaEdficioHTML = curl_exec($this->ch);

        $docVistaEdficio = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaEdficio->loadHTML($vistaEdficioHTML);

        //Comprbamos si se puede construir
        //¿Hay suficientes recursos?
        $madera =  (int)$docVistaEdficio->getElementById('l1')->nodeValue;
        $barro =  (int)$docVistaEdficio->getElementById('l2')->nodeValue;
        $hierro = (int)$docVistaEdficio->getElementById('l3')->nodeValue;
        $cereal =  (int)$docVistaEdficio->getElementById('l4')->nodeValue;
        $saldoCereal = (int)$docVistaEdficio->getElementById('stockBarFreeCrop')->nodeValue;

        print "TENEMOS -> Madera: ".$madera."  Barro: ".$barro."   Hierro: ".$hierro."   Cereal: ".$cereal."   Saldo de cereal: ".$saldoCereal."\n";

        $maderaCoste =  (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $barroCoste =  (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $hierroCoste = (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $cerealCoste =  (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $saldoCerealCoste = (int)$docVistaEdficio->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(4)->nodeValue;

        print "CUESTA -> Madera: ".$maderaCoste."  Barro: ".$barroCoste."   Hierro: ".$hierroCoste."   Cereal: ".$cerealCoste."   Saldo de cereal: ".$saldoCerealCoste."\n";

        if(!(($madera-$maderaCoste>=0)&&($barro-$barroCoste>=0)&&($hierro-$hierroCoste>=0)&&($cereal-$cerealCoste>=0)&&($saldoCereal-$saldoCerealCoste>=0))){
            print "No hay suficientes recursos\n";
            return -1;
        }else{
            print "SI Hay suficientes recursos\n";
        }

        //¿Hay constructores disponibles?
        $constructores =  explode("'",$docVistaEdficio->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles\n";
            return -1;
        }else{
            print "SI Hay constructores\n";
        }

        //Buscamos el botón de construir
        $aux = explode("'",$docVistaEdficio->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n");
        $upgrade = 'http://ts5.travian.net/'.$aux[1];
        curl_setopt($this->ch,CURLOPT_URL, $upgrade);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->ch);

    }

    public function build($buildingName) {
        echo "build method\n";

        print "Tratamos de construir el edificio ".$buildingName."\n";

        //Obtenemos de la lista el id en el que está construido
        $id = $this->idBuilding($buildingName);
        print "El id que tenemos es: ".$id."\n";

        //Obtenemos la categoría del edificio:
        $category = $this->categoryBuilding($buildingName);
        print "La categoría que tenemos es: ".$category."\n";

        //Obtenemos el contract_building del edificio, que identifica el id del html al que queremos acceder
        $contract = "contract_building".$this->contractBuilding($buildingName);
        print "El contract que tenemos es: ".$contract."\n";

        
        //Cargamos el html correspondiente al menu en el que está el botón de construir edficio
        $urlVistaConstruccion = 'http://ts5.travian.net/build.php?id='.$id."&category=".$category;
        curl_setopt($this->ch,CURLOPT_URL, $urlVistaConstruccion);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $vistaConstruccionHTML = curl_exec($this->ch);

        $docVistaConstruccion = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaConstruccion->loadHTML($vistaConstruccionHTML);

        //Comprbamos si se puede construir
        //¿Hay suficientes recursos?
        $madera =  (int)$docVistaConstruccion->getElementById('l1')->nodeValue;
        $barro =  (int)$docVistaConstruccion->getElementById('l2')->nodeValue;
        $hierro = (int)$docVistaConstruccion->getElementById('l3')->nodeValue;
        $cereal =  (int)$docVistaConstruccion->getElementById('l4')->nodeValue;
        $saldoCereal = (int)$docVistaConstruccion->getElementById('stockBarFreeCrop')->nodeValue;

        print "TENEMOS -> Madera: ".$madera."  Barro: ".$barro."   Hierro: ".$hierro."   Cereal: ".$cereal."   Saldo de cereal: ".$saldoCereal."\n";

        
        $maderaCoste =  (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $barroCoste =  (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $hierroCoste = (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $cerealCoste =  (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $saldoCerealCoste = (int)$docVistaConstruccion->getElementById($contract)->childNodes->item(3)->childNodes->item(0)->childNodes->item(4)->nodeValue;

        print "CUESTA -> Madera: ".$maderaCoste."  Barro: ".$barroCoste."   Hierro: ".$hierroCoste."   Cereal: ".$cerealCoste."   Saldo de cereal: ".$saldoCerealCoste."\n";

        if(!(($madera-$maderaCoste>=0)&&($barro-$barroCoste>=0)&&($hierro-$hierroCoste>=0)&&($cereal-$cerealCoste>=0)&&($saldoCereal-$saldoCerealCoste>=0))){
            print "No hay suficientes recursos\n";
            return -1;
        }else{
            print "SI Hay suficientes recursos\n";
        }
        

        //¿Hay constructores disponibles?
        $constructores =  explode("'",$docVistaConstruccion->getElementById($contract)->childNodes->item(5)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles\n";
            return -1;
        }else{
            print "SI Hay constructores\n";
        }
        

        //Buscamos el botón
        //Primero buscamos el div del edificio que queremos consturir, ayudándonos del contract_building
        $aux = explode("'",$docVistaConstruccion->getElementById($contract)->childNodes->item(5)->childNodes->item(0)->getAttribute('onclick')."\n");
        $build = 'http://ts5.travian.net/'.$aux[1];        
        
        curl_setopt($this->ch,CURLOPT_URL, $build);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->ch);
        
        

    }

    public function listData() {
        echo "listData method";
    }

    private function idBuilding($buildingName){
        $array = explode(';',$this->buildingsType[$buildingName]);
        $array2 = explode('=',$array[1]);
        return (int)$array2[1];
    }

    private function categoryBuilding($buildingName){
        $array = explode(';',$this->buildingsType[$buildingName]);
        $array2 = explode('=',$array[0]);
        return (int)$array2[1];
    }

    private function contractBuilding($buildingName){
        $array = explode(';',$this->buildingsType[$buildingName]);
        $array2 = explode('=',$array[2]);
        return (int)$array2[1];
    }


}
?>