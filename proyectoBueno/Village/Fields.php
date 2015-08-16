<?php
class Fields
{
    // Attributes
    private $relativeUrl = 'dorf1.php';

	// This variable will be an array [building, slot]

    // Constructor
    function __construct($buildingsPosition) {
        $this->buildingsPosition = $buildingsPosition;
    }

    // Methods
    public function upgrade($buildingName, $level, $ch) {
        $noAvanzarIndice=0;

        //Obtenemos de la lista el id en el que está construido.
        $id = $this->idBuilding($buildingName);
        
        //Cargamos el html que aparece al pinchar sobre el edificio.
        $urlVistaRecurso = 'http://ts5.travian.net/build.php?id='.$id;
        curl_setopt($ch,CURLOPT_URL, $urlVistaRecurso);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $vistaRecursoHTML = curl_exec($ch);

        $docVistaRecurso = new DOMDocument();
        libxml_use_internal_errors(true);
        $docVistaRecurso->loadHTML($vistaRecursoHTML);

        //______Comprobamos si se puede subir nivel_____________
        //Comprobamos si hay suficientes recursos.
        //Obtenemos los recursos actuales:
        $recursos[0] =  (int)$docVistaRecurso->getElementById('l1')->nodeValue;
        $recursos[1] =  (int)$docVistaRecurso->getElementById('l2')->nodeValue;
        $recursos[2] = (int)$docVistaRecurso->getElementById('l3')->nodeValue;
        $recursos[3] =  (int)$docVistaRecurso->getElementById('l4')->nodeValue;
        $recursos[4] = (int)$docVistaRecurso->getElementById('stockBarFreeCrop')->nodeValue;

        //Obtenemos los recursos que cuesta subir el edificio:
        $recursosCoste[0] =  (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(0)->nodeValue;
        $recursosCoste[1] =  (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(1)->nodeValue;
        $recursosCoste[2] = (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(2)->nodeValue;
        $recursosCoste[3] =  (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(3)->nodeValue;
        $recursosCoste[4] = (int)$docVistaRecurso->getElementById('contract')->childNodes->item(1)->childNodes->item(0)->childNodes->item(4)->nodeValue;

        //Comprobamos si son suficientes y en caso de no serlo salimos de la función.
        if(!(($recursos[0]-$recursosCoste[0]>=0)&&($recursos[1]-$recursosCoste[1]>=0)&&($recursos[2]-$recursosCoste[2]>=0)&&($recursos[3]-$recursosCoste[3]>=0)&&($recursos[4]-$recursosCoste[4]>=0))){
            print "No hay suficientes recursos para subir ".$buildingName." a grado ".$levelUp.".\n";
            return -1;
        }

        
        //Comprobamos si hay constructores
        //NOTA: este control sirve para comprobar todo, pero para identificar el problema por el cual no se sube el edificio lo hemos separado en dos controles.
        $constructores =  explode("'",$docVistaRecurso->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('class')."\n");
        
        if(strncmp ($constructores[0] , "none",4)==0){
            print "No hay constructores disponibles en el interior de la aldea\n";
            return -1;
        }

        //Comprobamos el nivel al que vamos a subir:
        $buttonLevelUp =  $docVistaRecurso->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->nodeValue;
        $levelUp = (int) explode(" ",$buttonLevelUp)[3];
        if($level > $levelUp){
            print "Se quiere subir ".$buildingName." a grado ".$level." cuando esta en grado ".($levelUp-1).". Se va a subir a grado ".$levelUp.".\n";
            $noAvanzarIndice = 1;
        }else if($level < $levelUp){
            print "Se ha querido subir ".$buildingName." a grado ".$level." cuando esta en grado ".($levelUp-1).". Se ignora acción.\n";
            return 0;
        }

        //_________Comprobación de si se puede subir nivel terminada___________
        

        //Ahora ya sabemos que existe el botón de subir nivel, accedemos a él y lo pinchamos:
        $aux = explode("'",$docVistaRecurso->getElementById('contract')->childNodes->item(2)->childNodes->item(0)->getAttribute('onclick')."\n");
        $build = 'http://ts5.travian.net/'.$aux[1];
        curl_setopt($ch,CURLOPT_URL, $build);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        print "Se ha subido ".$buildingName." a grado ".$levelUp.".\n";
        
        if($noAvanzarIndice == 1){
            return -1;
        }
        
        return 0;
    }


    private function idBuilding($buildingName){
        $array = $this->buildingsPosition[$buildingName];
        $array2 = explode('=',$array);
        return (int)$array2[1];
    }
}
?>