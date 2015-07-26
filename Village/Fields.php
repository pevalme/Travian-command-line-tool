<?php
class Fields
{
    // Atributes
    private $relativeUrl = 'dorf1.php';

    // This variable will be initialize on running time
	public $currentBuildings = NULL;
	// This variable will be an array [building, slot]

    // Constructor
    function __construct($ch, $url) {
       print "Initializing village's fields\n";
   	}

    // Methods
    public function upgrade($buildingName) {
        echo "upgrade method";
    }

    public function listData() {
        echo "listData method";
    }
}
?>