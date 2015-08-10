<?php
class Center
{
    // Attributes
    private $relativeUrl = 'dorf2.php';
    private $buildingsType = array(
    	"Escondite" =>  'category=1;id=21', 
    	"Almacén" =>  'category=1;id=20',
    	"Granero" =>  'category=1;id=19',
    	"Embajada" => 'category=1;id=30',
    	"Mercado" => 'category=1;id=23',
    	"Residencia" => 'category=1;id=31',
    	"Palacio" => 'category=1;id=31',
    	"Cantero" => 'category=1;id=32',
    	"Tesoro" => 'category=1;id=32',
    	"Cervecería" => 'category=1;id=32',
    	"Ayuntamiento" => 'category=1;id=32',
    	"Oficina de Comercio" => 'category=1;id=32',
    	"Terraplén" => 'category=2;id=32',
    	"Cuartel" => 'category=2;id=32',
    	"Hogar del héroe" => 'category=2;id=32',
    	"Academia" => 'category=2;id=32',
    	"Herrería" => 'category=2;id=32',
    	"Establo" => 'category=2;id=32',
    	"Taller" => 'category=2;id=32',
    	"Plaza de Torneos" => 'category=2;id=32',
    	"Molino" => 'category=3;id=32',
    	"Serrería" => 'category=3;id=32',
    	"Ladrillar" => 'category=3;id=32',
    	"Fundición" => 'category=3;id=32',
    	"Panadería" => 'category=3;id=32',
	);

    // This variable will be initialize on running time
	public $currentBuildings = NULL;
	// This variable will be an array [building, slot]

    // Constructor
    function __construct($cha) {
       print "Initializing village's center\n";
       $this->ch=$cha;
   	}

    // Methods
    public function upgrade($buildingName) {
        echo "upgrade method\n";
        print "Tratamos de subir el edificio ".$buildingName."\n";

        //Obtenemos de la lista el id en el que está construido
        $id = $this->idBuilding($buildingName);
        print "El id que tenemos es: ".$id."\n";

        $urlVistaEdificio = 'http://ts5.travian.net/build.php?id='.$id;
        curl_setopt($this->ch,CURLOPT_URL, $urlVistaEdificio);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $vistaEdficioHTML = curl_exec($this->ch);

        $docVistaEdficio = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaEdficio->loadHTML($vistaEdficioHTML);

        //print "Posición: ".$id. " -> ".$docVistaEdficio->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n";
        //print "Posición: ".$id. " -> ".$docVistaEdficio->getElementById('contract')->childNodes->item(2)->getAttribute('class')."\n"; button55c7e34e16ac7
        $aux = explode("'",$docVistaEdficio->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n");
        $upgrade = 'http://ts5.travian.net/'.$aux[1];
        curl_setopt($this->ch,CURLOPT_URL, $upgrade);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->ch);

    }

    public function build($buildingName) {
        echo "build method";
    }

    public function listData() {
        echo "listData method";
    }

    private function idBuilding($buildingName){
        $array = explode(';',$this->buildingsType[$buildingName]);
        $array2 = explode('=',$array[1]);
        return (int)$array2[1];
    }
}
?>