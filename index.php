<?php

    $data = curl_get_request("https://coursepress.lnu.se/kurser/");

	$courseUrlsData = domItAndExtract($data, "//ul[@id='blogs-list']/li/div/div[@class='item-title']/a");    

	$courseUrls = array();

	foreach($courseUrlsData as $n) {
		array_push($courseUrls, $n->getAttribute("href"));
	}

	$courseData;

	$courseDataArr = array();



	foreach ($courseUrls as $url) {

		$courseData = curl_get_request($url);

		array_push($courseDataArr, $courseData);

	}


	//Kollar efter kursnamnen på varje sida
	$courseNames = getCourseData($courseDataArr, "//div[@id='header-wrapper']/h1/a", false);
	//var_dump($courseNames);
	
	//Kollar efter kurskod på varje sida
	$courseCodes = getCourseData($courseDataArr, "//div[@id='header-wrapper']/ul/li[position()=3]/a", false);

	//Hämta url:er för kursplaner
	$coursPlanUrls = getCourseData($courseDataArr, "//ul[@id='menu-main-nav-menu']/li/ul/li[a='Kursplan']/a", true);

	//$courseDescriptions = getCourseData($courseData, "//section[@id='content']/", false);

	var_dump($coursPlanUrls);


	function getCourseData($courseDataArr, $query, $getHrefAttr) {

		$newTargetElements = array();

		foreach ($courseDataArr as $cData) {

			libxml_use_internal_errors(true);

			$targetElements = domItAndExtract($cData, $query);
			
			libxml_use_internal_errors(false);

			$temp_dom = new DOMDocument();
		

			foreach ($targetElements as $element) {
				//$temp_dom->appendChild($temp_dom->importNode($element,true));
				//var_dump($element);

				$newdoc = new DOMDocument();
			    $cloned = $element->cloneNode(TRUE);

			    $newdoc->appendChild($newdoc->importNode($cloned,TRUE));
			    //echo $newdoc->saveHTML();

				if($getHrefAttr) {
					foreach($targetElements as $n) {
						array_push($newTargetElements, $n->getAttribute("href"));
					}
				} else {
					array_push($newTargetElements, $cloned->nodeValue);
				}	
			}
		}

		return $newTargetElements;
	}

    function domItAndExtract($data, $query) {
    	$dom = new DomDocument();

	    if($dom->loadHTML($data)) {

	    	$xpath = new DOMXPath($dom);

	    	$dom_node_list = $xpath->query($query);

	    	return $dom_node_list;

	    } else {
	    	die("Fel vid DOM-inläsning.");
	    }
	}

    
    function curl_get_request($url) {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $data = curl_exec($ch);
        curl_close($ch);
        
        //var_dump($data);

        return $data;
    }