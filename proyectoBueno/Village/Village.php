<?php

require_once('Center.php');
require_once('Fields.php');

class Village
{
    // Attributes
 

    // Constructor
    function __construct($buildingPositionCenter, $buildingPositionFields) {
        $this->center = new Center($buildingPositionCenter);
        $this->fields = new Fields($buildingPositionFields);
   	}

    // Methods
    public function buildCenter($buildingName, $ch){
        return $this->center->build($buildingName, $ch);
    }

    public function upgradeCenter($buildingName, $ch){
        return $this->center->upgrade($buildingName, $ch);
    }

    public function upgradeField($buildingName, $ch){
        return $this->fields->upgrade($buildingName, $ch);
    }

}
?>