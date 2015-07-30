<?php
class HeroAdventure
{
    // Attributes
    private $relativeUrl = 'hero_adventure.php';

    // This variable will be initialize on running time
	public $adventureList = array();
	// This variable will be an array [adventure_time, id]

    // Constructor
    function __construct($ch, $url) {
        print "Initializing hero's adventure data\n";

        curl_setopt($ch,CURLOPT_URL, $url . $relativeUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $doc = new DomDocument();
        $doc->loadHTML($result);
        $adventures = $doc->getElementById('adventureListForm')->getElementsByTagName('tr');

        // The first element will be the title of the table so we have to skip it
        $firstElement = true;
        foreach ($adventures as $i => $adv) {
            if($firstElement){
                $firstElement = false;
            } else {
                $fields = array();
                // Add the link needed to go to this adventure
                $fields['href'] = $adv->getElementsByTagName('a')->item(1)->getAttribute('href');

                // Getting hidden fields
                foreach ($adv->getElementsByTagName('input') as $j => $input){
                    $fields[$input->getAttribute('name')]=$input->getAttribute('value');
                }

                $this->adventureList[$adv->getElementsByTagName('td')->item(2)->plaintext . $adv->getElementsByTagName('td')->item(4)->plaintext] = $fields;
            }
        }
   	}

    // Methods
    public function goTo($adventureNumber) {
        echo "goTo method";
    }

    public function listData() {
        echo "listData method\n";

        $counter = 0;
        foreach($adventureList as $time => $adv){
            echo "Aventura ". $counter++ . " encontrada a: " . $time;
            //foreach($adv as $index => $data){
            //    echo "[".$index."]=".$data;
            //} 
            print "\n";
        }  

    }
}
?>