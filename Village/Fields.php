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
        echo "upgrade method \n";

        print "Tratamos de subir el edificio ".$buildingName."\n";

        //Obtenemos de la lista el id en el que está construido
        $id = $this->idBuilding($buildingName);
        print "El id que tenemos es: ".$id."\n";

        
        //Cargamos el html correspondiente al menu en el que está el botón de construir edficio
        $urlVistaRecurso = 'http://ts5.travian.net/build.php?id='.$id;
        curl_setopt($this->ch,CURLOPT_URL, $urlVistaRecurso);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        $vistaRecursoHTML = curl_exec($this->ch);

        $docVistaRecurso = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaRecurso->loadHTML($vistaRecursoHTML);

        //Buscamos el botón
        //Primero buscamos el div del edificio que queremos consturir, ayudándonos del contract_building
        $aux = explode("'",$docVistaRecurso->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n");
        $build = 'http://ts5.travian.net/'.$aux[1];
        curl_setopt($this->ch,CURLOPT_URL, $build);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->ch);
        
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