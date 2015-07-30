<?php

require_once('Center.php');
require_once('Fields.php');
require_once('HeroAdventure.php');

class Village
{
    // Attributes
    public $center;
    public $fields;
    public $heroAdventure;

    // Constructor
    function __construct($url, $ch) {
        print "Initializing village\n";

        $this->center = new Center($this->$ch, $url);
        $this->fields = new Fields($this->$ch, $url);
        $this->heroAdventure = new HeroAdventure($this->$ch, $url);

        print "Villabe initialized";
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