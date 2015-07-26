<?php
class HeroAdventure
{
    // Atributes
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
        $adventures = $doc->getElementById('adventureListForm')->getElementByTagName('tr');

        // The frist element will be the title of the table so we have to skip it
        $firstElement = true;
        foreach ($adventures as $i => $adv) {
            if($firstElement){
                $firstElement = false;
            } else {
                // Getting hidden fields
                $fields = array();
                foreach ($adv->getElementByTagName('input') as $j => $input){
                    $fields[] = $input->getAttribute('name');
                    $fields[] = $input->getAttribute('value');
                }

                // Add the link needed to go to this adventure
                $fields[] = $adv->getElementByTagName('a')->getAttribute('onclick');

                $this->adventureList[$adv->getElementByTagName('td')->item(2)->plaintext] = $fields;
            }
        }
   	}

    // Methods
    public function goTo($adventureNumber) {
        echo "goTo method";
    }

    public function listData() {
        echo "listData method";
    }
}
?>