<?php

    $data = curl_get_request("https://coursepress.lnu.se/kurser/");

    $courseUrls = getPageData($data, "//ul[@id='blogs-list']/li/div/div[@class='item-title']/a", true);


	$courseDataArr = array();

	foreach ($courseUrls as $url) {

		$courseData = curl_get_request($url);

		array_push($courseDataArr, $courseData);

	}


	//Hämtar de olika elementen från sidan

	$courseNames = getPageData($courseDataArr, "//div[@id='header-wrapper']/h1/a", false);
	
	$courseCodes = getPageData($courseDataArr, "//div[@id='header-wrapper']/ul/li[position()=3]/a", false);

	$coursPlanUrls = getPageData($courseDataArr, "//ul[@id='menu-main-nav-menu']/li/ul/li[a='Kursplan']/a", true);

	$courseDescriptions = getPageData($courseDataArr, "(//div[@class='entry-content'])[1]", false);
	
	$latestPosts = getPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]", false);

	$latestPostsTitle = getPageData($courseDataArr, "(//header[@class='entry-header']/h1[@class='entry-title'])[1]", false);

	$latestPostAuthors = getPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]/strong", false);

	$latestPostTimestamps = getPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]", false);

	//h1[@id='latest-post']/ancestor::header[1]/following::article[1]

	$latestPostsTimes = stringSliceMachine($latestPostTimestamps);
	
	var_dump($latestPostsTimes);


	function stringSliceMachine($strArray) {
	
		$newStrArr = array();

		foreach ($strArray as $string) {
			$newStr;

			$string = (string) $string;

			if(strpos($string, "Publicerad ") !== false) {
				$newStr = substr($string, strpos($string, "Publicerad ") + strlen("Publicerad "), 10);
				//var_dump(strpos($string, "av"));

				//strpos($string, "av")

				array_push($newStrArr, $newStr);
			}

		}

		return $newStrArr;
	}


	function getPageData($courseData, $query, $getHrefAttr) {

		$newTargetElements = array();

		$courseDataArr = array();

		if(!is_array($courseData)) {
			array_push($courseDataArr, $courseData);
		} else {
			$courseDataArr = $courseData;
		}

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