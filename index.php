<?php

    $data = curl_get_request("https://coursepress.lnu.se/kurser/");

    $dom = new DomDocument();

    if($dom->loadHTML($data)) {
    	$xpath = new DOMXPath($dom);

    	$dom_node_list = $xpath->query("//ul[@id='blogs-list']/li/div/div[@class='item-title']/a/@href");

    	//

    	//var_dump($dom_node_list);

    	$temp_dom = new DOMDocument();
		foreach($dom_node_list as $n) $temp_dom->appendChild($temp_dom->importNode($n,true));
		print_r($temp_dom->saveHTML());

    	//var_dump($items);

    } else {
    	die("Fel vid DOM-inläsning.");
    }


    
    function curl_get_request($url) {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $data = curl_exec($ch);
        curl_close($ch);
        
//        var_dump($data);

        return $data;
    }