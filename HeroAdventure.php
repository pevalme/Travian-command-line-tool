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

        curl_setopt($ch,CURLOPT_URL, $url . $this->relativeUrl);
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

                $this->adventureList[trim($adv->getElementsByTagName('td')->item(2)->childNodes->item(0)->nodeValue) . ' <=> ' . $adv->getElementsByTagName('td')->item(4)->childNodes->item(1)->nodeValue] = $fields;
            }
        }
        asort($this->adventureList);
   	}

    // Methods
    public function goToAdventure($ch, $url, $adventureNumber) {
        curl_setopt($ch,CURLOPT_URL, $url . $this->adventureList[array_keys($this->adventureList)[$adventureNumber]]['href']);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        // Acces to the $adventureNumber adventure
        $result = curl_exec($ch);

        //print $result;
        $doc = new DomDocument();
        $doc->loadHTML($result);

        // Get the form that we have to submit to send the hero to the adventure
        $action = $doc->getElementsByTagName('form')[0]->getAttribute('action');

        // Get the inputs of the form
        $inputs = $doc->getElementsByTagName('input');
        $fields = array();

        foreach($inputs as $i => $data){
            $fields[$data->getAttribute('name')]=$data->getAttribute('value');
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

        curl_exec($ch);
    }

    public function listData() {
        echo "listData method\n";

        $counter = 0;
        foreach($this->adventureList as $time => $adv){
            echo "Aventura ". $counter++ . " encontrada a: " . $time . "\n";
        }
    }
}
?>