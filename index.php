<?php

	require_once("DataObj.php");

    $data = curl_get_request("https://coursepress.lnu.se/kurser/");

    $courseUrls = extractPageData($data, "//ul[@id='blogs-list']/li/div/div[@class='item-title']/a", true);


   	$coursesObjArray = array();

   	foreach ($courseUrls as $url) {

   		$tempObj = new DataObj();

   		$tempObj->url = $url;

   		$coursesObjArray[$url] = $tempObj;
   	}



	$courseDataArr = curl_get_request($courseUrls);


	//Hämtar de olika elementen från sidan

	$courseNames = extractPageData($courseDataArr, "//div[@id='header-wrapper']/h1/a", false);

	updateCourseObjects($courseNames, "courseName", $coursesObjArray);

	
	$courseCodes = extractPageData($courseDataArr, "//div[@id='header-wrapper']/ul/li[position()=3]/a", false);

	updateCourseObjects($courseCodes, "courseCode", $coursesObjArray);


	$coursPlanUrls = extractPageData($courseDataArr, "//ul[@id='menu-main-nav-menu']/li/ul/li[a='Kursplan']/a", true);

	updateCourseObjects($coursPlanUrls, "coursPlanUrl", $coursesObjArray);


	$courseDescriptions = extractPageData($courseDataArr, "(//div[@class='entry-content'])[1]", false);
	
	updateCourseObjects($courseDescriptions, "courseDescription", $coursesObjArray);	



//	$latestPosts = extractPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]", false);



	$latestPostsTitle = extractPageData($courseDataArr, "(//header[@class='entry-header']/h1[@class='entry-title'])[1]", false);

	updateCourseObjects($latestPostsTitle, "latestPostTitle", $coursesObjArray);	


	$latestPostsAuthors = extractPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]/strong", false);

	updateCourseObjects($latestPostsAuthors, "latestPostAuthor", $coursesObjArray);		


	$latestPostsTimestamps = stringSliceMachine(extractPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]", false), "Publicerad ");

	updateCourseObjects($latestPostsTimestamps, "latestPostTimestamp", $coursesObjArray);




	foreach ($coursesObjArray as $key => $object) {
		var_dump($object);
		var_dump("<br><br>");
	}




	//h1[@id='latest-post']/ancestor::header[1]/following::article[1]
	
	//var_dump($latestPostTimestamps);


	function updateCourseObjects($dataArray, $fieldName, $courseObjects) {

		
		foreach ($courseObjects as $urlObject => $object) {
			foreach ($dataArray as $urlKeyData => $dataValue) {
				if($urlObject == $urlKeyData) {
					$object->$fieldName = $dataValue;
					break;
				}
			}
		}
	}


	function stringSliceMachine($strArray, $magicSliceWord) {
	
		$newStrArr = array();

		foreach ($strArray as $string) {
			$newStr;

			if(strpos($string, $magicSliceWord) !== false) {
				$newStr = substr($string, strpos($string, $magicSliceWord) + strlen($magicSliceWord), 16);
				array_push($newStrArr, $newStr);
			}
		}

		return $newStrArr;
	}


	function extractPageData($courseData, $query, $getHrefAttr) {

		$newTargetElements = array();

		$courseDataArr = array();

		if(!is_array($courseData)) {
			array_push($courseDataArr, $courseData);
		} else {
			$courseDataArr = $courseData;
		}

		foreach ($courseDataArr as $url => $cData) {

			libxml_use_internal_errors(true);

			$targetElements = domItAndExtract($cData, $query);
			
			libxml_use_internal_errors(false);

			$temp_dom = new DOMDocument();
		
			$counter = 0;

			foreach ($targetElements as $element) {
				//$temp_dom->appendChild($temp_dom->importNode($element,true));
				//var_dump($element);

				$newdoc = new DOMDocument();
			    $cloned = $element->cloneNode(TRUE);

			    $newdoc->appendChild($newdoc->importNode($cloned,TRUE));
			    //echo $newdoc->saveHTML();

				if($getHrefAttr) {
					//foreach($targetElements as $n) {
						//var_dump($url);
						$newTargetElements[$counter] = $element->getAttribute("href");
						$counter++;
						//var_dump($newTargetElements[$url]);
					//}
				} else {
					//var_dump($url);
					$newTargetElements[$url] = $cloned->nodeValue;
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

    function curl_get_request($tempUrlArr) {
        
        $urlArr = array();

        $dataArray = array();

        $countNumeric = false;

        if(!is_array($tempUrlArr)) {
        	array_push($urlArr, $tempUrlArr);
        	$countNumeric = true;
        } else {
        	$urlArr = $tempUrlArr;
        }

        foreach ($urlArr as $url) {
        
	        $ch = curl_init();

	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        
	        $data = curl_exec($ch);
	        curl_close($ch);
	        
	        if($countNumeric) {
	        	$dataArray[0] = $data;
	    	} else {
	    		$dataArray[$url] = $data;
	    	}
	    }

	    return $dataArray;
    }




