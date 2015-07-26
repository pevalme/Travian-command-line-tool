<?php

require_once('Village/Village.php');


class Account
{
    // Atributes
    private $url = 'http://ts5.travian.net/';
    // For now this works. In a future this array should also be completed automatically by asking the user for the name and password
    private $loginData = array(
                            'name' => urlencode('Filtor'),
                            'password' => urlencode('47230660Ee'),
                            'lowRes' => urlencode('1'),
                            'w' => urlencode(''),
                            'login' => urlencode('1437574738')
                    );

    //Curl connection
    private $ch = null;

    public $villages = null;

    // Constructor
    function __construct() {
        print "Initializing account\n";
        //url-ify the data for the POST
        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');

        //open connection
        $this->$ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($this->$ch,CURLOPT_URL, $url);
        curl_setopt($this->$ch,CURLOPT_POST, count($fields));
        curl_setopt($this->$ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->$ch,CURLOPT_POSTFIELDS, $fields_string);

        //curl will take care about the cookies
        curl_setopt($this->$ch, CURLOPT_COOKIEJAR, '');

        //execute post
        $result = curl_exec($this->$ch);

        // TODO.
        // Look for the list of villages and their url on the $result
        // Once we have it, call several times to new Village() by grouping the results in an array


        print "Account initialized";
   	}

    // Methods

    // TODO. These methods have not been designed yet.
}
?>