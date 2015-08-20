<?php

class class3{
	function __construct($b) {
	    $this->b = $b;
	 }

	 public function jej3(){
	 	print $this->b["Granja1"];
	 }
}

class class2
{
	 function __construct($b) {
	    $this->b = new class3($b);
	 }

	 public function jej2(){
	 	$this->b->jej3();
	 }
}

class class1
{
	 function __construct() {
	 	$a[0] = array(
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
        "Granja6" => 'id=15'
        );

        $this->b[0] = new class2($a[0]);
	 }

	 public function jej1(){
	 	$this->b[0]->jej2();
	 }

}

//Generar numeros aleatorios sin repetir del 1 al 100.
$numeros = 10;
print "Generar numeros aleatorios sin repetir del 1 al".$numeros."\n";


for($i=0; $i<$numeros; $i++){
        $cadenaAux[$i] = $i+1;
}

for($i=0; $i<$numeros; $i++){
        $aux = rand(0,$numeros-$i-1);
        $num[$i] = $cadenaAux[$aux];
        $cadenaAux[$aux] = $cadenaAux[$numeros-$i-1]; 
}


print "\n";
for($i=0; $i<$numeros; $i++){
        print '-'.$num[$i];
}



//print count($a);

?>