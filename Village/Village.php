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

    public function upgradeCenter($buildingName, $level, $ch){
        return $this->center->upgrade($buildingName, $level, $ch);
    }

    public function upgradeFields($buildingName, $level, $ch){
        return $this->fields->upgrade($buildingName, $level, $ch);
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

    public function attack($ch, $url, $targetName, $targetX, $targetY, $t1, $t2, $t3, $t4, $t5, $t6, $t7, $t8, $t9, $t10, $sendHero, $typeOfAttack){

        $relativeUrl = "build.php?id=39&tt=2"; // Access to "plaza de torneos" an move to "enviar tropas"

        curl_setopt($ch,CURLOPT_URL, $url . $relativeUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $doc = new DomDocument();
        $doc->loadHTML($result);

        $action = $doc->getElementsByTagName('form')[0]->getAttribute('action');

        $inputs = $doc->getElementsByTagName('input');

        $fields = array();
        foreach($inputs as $index => $data){
            if ($data->getAttribute('type') == "hidden"){
                $fields[$data->getAttribute('name')] = $data->getAttribute('value');
            }else{
                switch ($data->getAttribute('name')) {
                    case 't1':
                        $fields[$data->getAttribute('name')] = $t1;
                        break;
                    case 't2':
                        $fields[$data->getAttribute('name')] = $t2;
                        break;
                    case 't3':
                        $fields[$data->getAttribute('name')] = $t3;
                        break;
                    case 't4':
                        $fields[$data->getAttribute('name')] = $t4;
                        break;
                    case 't5':
                        $fields[$data->getAttribute('name')] = $t5;
                        break;
                    case 't6':
                        $fields[$data->getAttribute('name')] = $t6;
                        break;
                    case 't7':
                        $fields[$data->getAttribute('name')] = $t7;
                        break;
                    case 't8':
                        $fields[$data->getAttribute('name')] = $t8;
                        break;
                    case 't9':
                        $fields[$data->getAttribute('name')] = $t9;
                        break;
                    case 't10':
                        $fields[$data->getAttribute('name')] = $t10;
                        break;
                    case 't11':
                        $fields[$data->getAttribute('name')] = $sendHero;
                        break;
                    case 'dname':
                        if ($targetName != ''){
                            $fields[$data->getAttribute('name')] = "Aldea de iker";
                        }
                        break;
                    case 'x':
                        if ($targetName == ''){
                            $fields[$data->getAttribute('name')] = $targetX;
                        }
                        break;
                    case 'y':
                        if ($targetName == ''){
                            $fields[$data->getAttribute('name')] = $targetY;
                        }
                        break;
                    case 'c':
                        if (intval($data->getAttribute('value')) == $typeOfAttack){
                            $fields[$data->getAttribute('name')] = $data->getAttribute('value');
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.urlencode($value).'&';
        }
        $fields_string = rtrim($fields_string, '&');
        curl_setopt($ch,CURLOPT_URL, $url . $action);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);

        $doc = new DomDocument();
        $doc->loadHTML($result);

        $action = $doc->getElementsByTagName('form')[0]->getAttribute('action');

        $inputs = $doc->getElementsByTagName('input');

        $fields = array();
        foreach($inputs as $index => $data){
            $fields[$data->getAttribute('name')] = $data->getAttribute('value');
        }

        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.urlencode($value).'&';
        }

        $fields_string = rtrim($fields_string, '&');
        curl_setopt($ch,CURLOPT_URL, $url . $action);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
    }
}
?>