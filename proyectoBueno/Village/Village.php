<?php

require_once('Center.php');
require_once('Fields.php');

class Village
{
    // Attributes
 

    // Constructor
    function __construct($buildingPositionFields, $buildingPositionCenter, $autFields, $autCenter, $name, $coords, $indices, $url) {
        $this->fields = new Fields($buildingPositionFields);
        $this->center = new Center($buildingPositionCenter);
        $this->autFields = $autFields;
        $this->autCenter = $autCenter;
        $this->name = $name;
        $this->coordenadas = $coords;
        $this->indices = $indices;
        $this->url = $url;
   	}

    // Methods
    public function buildCenter($buildingName, $ch){
        return $this->center->build($buildingName, $ch);
    }

    public function upgradeCenter($buildingName, $ch){
        return $this->center->upgrade($buildingName, $ch);
    }

    public function upgradeFields($buildingName, $ch){
        return $this->fields->upgrade($buildingName, $ch);
    }

    public function getIndiceFields(){
        return $this->indices[0];
    }

    public function getIndiceCenter(){
        return $this->indices[1];
    }

    public function getAutFields(){
        return $this->autFields;
    }

    public function getAutCenter(){
        return $this->autCenter;
    }

    public function increaseIndiceFields(){
        $this->indices[0]++;
    }

    public function increaseIndiceCenter(){
        $this->indices[1]++;
    }

    public function getName(){
        return $this->name;
    }

}
?>