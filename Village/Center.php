<?php
class Center
{
    // Atributes
    private $relativeUrl = 'dorf2.php';
    private $buildingsType = array(
    	"Escondite" =>  'category=1',
    	"Almacén" =>  'category=1',
    	"Granero" =>  'category=1',
    	"Embajada" => 'category=1',
    	"Mercado" => 'category=1',
    	"Residencia" => 'category=1',
    	"Palacio" => 'category=1',
    	"Cantero" => 'category=1',
    	"Tesoro" => 'category=1',
    	"Cervecería" => 'category=1',
    	"Ayuntamiento" => 'category=1',
    	"Oficina de Comercio" => 'category=1',
    	"Terraplén" => 'category=2',
    	"Cuartel" => 'category=2',
    	"Hogar del héroe" => 'category=2',
    	"Academia" => 'category=2',
    	"Herrería" => 'category=2',
    	"Establo" => 'category=2',
    	"Taller" => 'category=2',
    	"Plaza de Torneos" => 'category=2',
    	"Molino" => 'category=3',
    	"Serrería" => 'category=3',
    	"Ladrillar" => 'category=3',
    	"Fundicón" => 'category=3',
    	"Panadería" => 'category=3',
	);

    // This variable will be initialize on running time
	public $currentBuildings = NULL;
	// This variable will be an array [building, slot]

    // Constructor
    function __construct($ch, $url) {
       print "Initializing village's center\n";
   	}

    // Methods
    public function upgrade($buildingName) {
        echo "upgrade method";
    }

    public function build($buildingName) {
        echo "build method";
    }

    public function listData() {
        echo "listData method";
    }
}
?>