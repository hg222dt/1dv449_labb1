<?php

	require_once("DataObj.php");

	$resultFilename = "jsonResult.json";

	$cacheStrategyOn = false;
	$cachingTimeInSeconds = 300;

	$timeStampNow =  time();

	$json_data = file_get_contents($resultFilename);
	$decodedJson = json_decode($json_data);





	$timeSinceLastScrape = checkTimeSinceLastScrape($decodedJson, $timeStampNow);
	

	if($timeSinceLastScrape < $cachingTimeInSeconds && $cacheStrategyOn) {

		$new_datetime = date('Y-m-d H:i:sa', $timeStampNow);

		$decodedJson->Meta_data->Timestamp = $new_datetime;

		$jsonStr = (string) json_encode($decodedJson, JSON_PRETTY_PRINT);

		$myfile = fopen($resultFilename, "w");
		fwrite($myfile, $jsonStr);


		echo "<a href='$resultFilename'>Fil med resultat (json)</a> (Cashed file)";

	} else {

	    $data = curl_get_request("https://coursepress.lnu.se/kurser/");

	    $numberOfPages = countNumberOfPages($data);

	    $courseUrls = extractPageData($data, "//ul[@id='blogs-list']/li/div/div[@class='item-title']/a", true);

	    for($i=1; $i<$numberOfPages; $i++) {

	    	$pageNumber = $i + 1;

	    	$newPageData = curl_get_request("https://coursepress.lnu.se/kurser/?bpage=" . $pageNumber);

	    	$newCourseUrls = extractPageData($newPageData, "//ul[@id='blogs-list']/li/div/div[@class='item-title']/a", true);

	    	//var_dump($newCourseUrls);

	    	foreach ($newCourseUrls as $url) {
	    		array_push($courseUrls, $url);
	    	}
	    }

	    //var_dump(sizeof($courseUrls));


	   	$coursesObjArray = array();

	   	foreach ($courseUrls as $url) {

	   		$tempObj = new DataObj();

	   		$tempObj->url = $url;

	   		$coursesObjArray[$url] = $tempObj;
	   	}

		$courseDataArr = curl_get_request($courseUrls);




		$courseNames = extractPageData($courseDataArr, "//div[@id='header-wrapper']/h1/a", false);

		updateCourseObjects($courseNames, "setCourseName", $coursesObjArray);

		
		$courseCodes = extractPageData($courseDataArr, "//div[@id='header-wrapper']/ul/li[position()=3]/a", false);

		updateCourseObjects($courseCodes, "setCourseCode", $coursesObjArray);


		$coursPlanUrls = extractPageData($courseDataArr, "//ul[@id='menu-main-nav-menu']/li/ul/li[a='Kursplan']/a", true);

		updateCourseObjects($coursPlanUrls, "setCoursePlanUrl", $coursesObjArray);


		$courseDescriptions = extractPageData($courseDataArr, "(//div[@class='entry-content'])[1]", false);
		
		updateCourseObjects($courseDescriptions, "setCourseDescription", $coursesObjArray);	


		$latestPostsTitle = extractPageData($courseDataArr, "(//header[@class='entry-header']/h1[@class='entry-title'])[1]", false);

		updateCourseObjects($latestPostsTitle, "setLatestPostTitle", $coursesObjArray);	


		$latestPostsAuthors = extractPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]/strong", false);

		updateCourseObjects($latestPostsAuthors, "setLatestPostAuthor", $coursesObjArray);		


		$latestPostsTimestamps = stringSliceMachine(extractPageData($courseDataArr, "(//h1[@id='latest-post']/ancestor::section//p[@class='entry-byline'])[1]", false), "Publicerad ");

		updateCourseObjects($latestPostsTimestamps, "setLatestPostTimestamp", $coursesObjArray);





		$metaDataArray = createMetaData($coursesObjArray);



		jsonify($coursesObjArray, $metaDataArray, $resultFilename);


		echo "<a href='$resultFilename'>Fil med resultat (json)</a>";

	}





	function countNumberOfPages($firstPageData) {

		$numberOfPages = extractPageData($firstPageData, '//div[@id = "blog-dir-pag-top"]/a[@class ="page-numbers"]', false);
		

		foreach ($numberOfPages as $key => $value) {
			$numericPageValue = (int) $value;			
		}

		return $numericPageValue; 
		
	}




	function checkTimeSinceLastScrape($decodedJson, $timeStampNow) {

		$timeStr = $decodedJson->Meta_data->Timestamp;

		$timeStampLastScrape = strtotime($timeStr);

		$timeDifference = $timeStampNow - $timeStampLastScrape;

		return $timeDifference;
	}


	function createMetaData($coursesObjArray) {
		$metaDataArray = array();

		$amountOfCoursesScraped = sizeof($coursesObjArray);
		$time = time();

		$currentTime = date("Y-m-d h:i:sa", $time);

		$metaDataArray["Amount of scraped course pages"] = $amountOfCoursesScraped;
		$metaDataArray["Timestamp"] = $currentTime;

		return $metaDataArray;
	}


	function jsonify($pagesObjectsArray, $metaDataArray, $resultFilename) {

		$count = 0;

		$jsonArray = array();

		$pageDataArray = array();

		$arrWithObjects = array();

		foreach ($pagesObjectsArray as $key => $object) {

			$pageArr = array();

			$count++;

			$pageArray = array("Page $count" => $object->getObjectAsArray());

			array_push($pageDataArray, $pageArray);
		}

		$jsonArray['Meta_data'] = $metaDataArray;
		$jsonArray['Page_data'] = $pageDataArray;

		$jsonStr = (string) json_encode($jsonArray, JSON_PRETTY_PRINT);

		$myfile = fopen($resultFilename, "w");
		fwrite($myfile, $jsonStr);
	}



	function updateCourseObjects($dataArray, $objAction, $courseObjects) {
		foreach ($courseObjects as $urlObject => $object) {
			foreach ($dataArray as $urlKeyData => $dataValue) {
				if($urlObject == $urlKeyData) {
					$object->$objAction($dataValue);
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
		unset($newTargetElements);
		$newTargetElements = array();

		$courseDataArr = array();
		unset($courseDataArr);
		$newTargetElements = array();


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
        unset($urlArr);
        $urlArr = array();

        $dataArray = array();
        unset($dateArray);
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

	    	$userAgent = "test";

		    $options = array(
		        CURLOPT_RETURNTRANSFER => TRUE,  // Setting cURL's option to return the webpage data
		        CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
		        CURLOPT_USERAGENT => $userAgent,// Setting the useragent
		        CURLOPT_URL => $url, // Setting cURL's URL option with the $url variable passed into the function
		    );
		    
		    curl_setopt_array($ch, $options);

	        $data = curl_exec($ch);
	        curl_close($ch);
	        
	        if($countNumeric) {
	        	$dataArray[0] = $data;
	    	} else {
	    		$dataArray[$url] = $data;
	    	}
	    }

	    sizeof($dataArray);

	    return $dataArray;
    }




