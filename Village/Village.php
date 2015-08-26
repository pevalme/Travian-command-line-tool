<?php

require_once('Center.php');
require_once('Fields.php');

class Village
{
    // Attributes
    private $fields;
    private $center;
    private $autFields;
    private $autCenter;
    private $name;
    private $coordenadas;
    private $indices;
    private $url;

    // Constructor
    function __construct($buildingPositionFields, $buildingPositionCenter, $autFields, $autCenter, $name, $coords, $indices, $urlSuffix) {
        $this->fields = new Fields($buildingPositionFields);
        $this->center = new Center($buildingPositionCenter);
        $this->autFields = $autFields;
        $this->autCenter = $autCenter;
        $this->name = $name;
        $this->coordenadas = $coords;
        $this->indices = $indices;
        $this->urlSuffix= $urlSuffix;
   	}

    // Methods
    public function upgradeFields($buildingName, $level, $ch, $url){
        //Cargamos el html correspondiente a la vista de recursos de la aldea en la que vamos a construir.
        $this->enterFields($ch, $url);

        return $this->fields->upgrade($buildingName, $level, $ch, $url);
    }

    public function buildCenter($buildingName, $ch, $url){
        //Cargamos el html correspondiente a la vista central de la aldea en la que vamos a construir.
        $this->enterCenter($ch, $url);

        return $this->center->build($buildingName, $ch, $url);
    }

    public function upgradeCenter($buildingName, $level, $ch, $url){
        //Cargamos el html correspondiente a la vista central de la aldea en la que vamos a construir.
        $this->enterCenter($ch, $url);

        return $this->center->upgrade($buildingName, $level, $ch, $url);
    }

    public function enterFields($ch, $url){
        //Esperamos un tiempo de seguridad para humanizar el both.
        $this->waitMili();

        //Cargamos el html correspondiente a la vista de recursos de la aldea en la que vamos a construir.
        $urlVistaFields = $url.'dorf1.php'.$this->urlSuffix;
        curl_setopt($ch,CURLOPT_URL, $urlVistaFields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $vistaFieldsHTML = curl_exec($ch);
    }

    public function enterCenter($ch, $url){
        //Esperamos un tiempo de seguridad para humanizar el both.
        $this->waitMili();

        //Cargamos el html correspondiente a la vista central de la aldea en la que vamos a construir.
        $urlVistaCentro = $url.'dorf2.php'.$this->urlSuffix;
        curl_setopt($ch,CURLOPT_URL, $urlVistaCentro);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $vistaCentroHTML = curl_exec($ch);
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

    public function attack($ch, $url, $targetName, $targetX, $targetY, $t1, $t2, $t3, $t4, $t5, $t6, $t7, $t8, $t9, $t10, $sendHero, $typeOfAttack, $numberOfWagons){
        $relativeUrl = "build.php?id=39&tt=2"; // Access to "plaza de torneos" an move to "enviar tropas"

        $mh = curl_multi_init();

        for ($i = 1; $i <= $numberOfWagons; $i++) {
            curl_setopt($ch[$i],CURLOPT_URL, $url . $relativeUrl);
            curl_setopt($ch[$i],CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch[$i]);
            $doc = new DomDocument();
            $doc->loadHTML($result);

            $action = $doc->getElementsByTagName('form')->item(0)->getAttribute('action');

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

            $fields_strings = '';
            foreach($fields as $key=>$value) {
                $fields_strings .= $key.'='.urlencode($value).'&';
            }
            $fields_strings = rtrim($fields_strings, '&');
            curl_setopt($ch[$i],CURLOPT_URL, $url . $action);
            curl_setopt($ch[$i],CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch[$i],CURLOPT_POST, count($fields));
            curl_setopt($ch[$i],CURLOPT_POSTFIELDS, $fields_strings);
            $result = curl_exec($ch[$i]);

            $doc = new DomDocument();
            $doc->loadHTML($result);

            $action = $doc->getElementsByTagName('form')->item(0)->getAttribute('action');

            $inputs = $doc->getElementsByTagName('input');

            $fields = array();
            foreach($inputs as $index => $data){
                $fields[$data->getAttribute('name')] = $data->getAttribute('value');
            }

            $fields_strings = '';
            foreach($fields as $key=>$value) {
                $fields_strings .= $key.'='.urlencode($value).'&';
            }

            $fields_strings = rtrim($fields_strings, '&');

            curl_setopt($ch[$i],CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch[$i],CURLOPT_POST, count($fields));

            curl_setopt($ch[$i],CURLOPT_URL, $url . $action);
            curl_setopt($ch[$i],CURLOPT_POSTFIELDS, $fields_strings);
            curl_setopt($ch[$i], CURLOPT_TIMEOUT, 1/$numberOfWagons);
            //curl_multi_add_handle($mh,$ch[$i]);
            //curl_exec($ch[$i]);
        }

        $active = null;
        echo "Let's Attack!!\n";
        for($i=1; $i <= $numberOfWagons; $i++){
            curl_exec($ch[$i]);
        }
        //curl_multi_exec($mh, $active); 
        //curl_multi_close($mh);  
        //curl_exec($ch[1]);
    }

    /* _________________ METODO DE ESPERAR UN TIEMPO MINIMO ____________________*/

    public function waitMili(){
        $espera = rand(500,1000);
        //print "Siguiente ejecución en ".$espera." milisegundos.\n";
        usleep($espera*1000);
    }
}
?>