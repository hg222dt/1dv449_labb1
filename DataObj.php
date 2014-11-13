<?php

class DataObj {
	
	public $url;
	public $courseName;
	public $courseCode;
	public $coursePlanUrl;
	public $courseDescription;
	public $latestPostTitle;
	public $latestPostAuthor;
	public $latestPostTimestamp;

	public function __construct() {

	}

	function getField($fieldName) {
		return $this->$fieldName;
	}

	function setUrl($input) {
		if($input == null) {
			$this->url = "no information";
		} else {
			$this->url = $input;
		}
	}

	function setCourseName($input) {
		if($input == null) {
			$this->courseName = "no information";
		} else {
			$this->courseName = $input;
		}	
	}

	function setCourseCode($input) {
		if($input == null) {
			$this->courseCode = "no information";
		} else {
			$this->courseCode = $input;
		}	
	}

	function setCoursePlanUrl($input) {
		if($input == null) {
			$this->coursePlanUrl = "no information";
		} else {
			$this->coursePlanUrl = $input;
		}
	}

	function setCourseDescription($input) {
		if($input == null) {
			$this->courseDescription = "no information";
		} else {
			$this->courseDescription = $input;
		}	
	}

	function setLatestPostTitle($input) {
		if($input == null) {
			$this->latestPostTitle = "no information";
		} else {
			$this->latestPostTitle = $input;
		}
	}

	function setLatestPostAuthor($input) {
		if($input == null) {
			$this->latestPostAuthor = "no information";
		} else {
			$this->latestPostAuthor = $input;
		}	
	}

	function setLatestPostTimestamp($input) {
		if($input == null) {
			$this->latestPostTimestamp = "no information";
		} else {
			$this->latestPostTimestamp = $input;
		}
	}


	function getObjectToString() {
		

		return $this->url . " " . 
				$this->courseName . " " . 
				$this->courseCode . " " . 
				$this->coursePlanUrl . " " . 
				$this->courseDescription . " " . 
				$this->latestPostTitle . " " . 
				$this->latestPostAuthor . " " . 
				$this->latestPostTimestamp;

	}

	function getObjectAsArray() {

		if($this->url == null) {
			$this->url = "no information";
		} 

		if($this->courseName == null) {
			$this->courseName = "no information";
		} 

		if($this->courseCode == null) {
			$this->courseCode = "no information";
		}

		if($this->coursePlanUrl == null) {
			$this->coursePlanUrl = "no information";
		}

		if($this->courseDescription == null) {
			$this->courseDescription = "no information";
		}

		if($this->latestPostTitle == null) {
			$this->latestPostTitle = "no information";
		}

		if($this->latestPostAuthor == null) {
			$this->latestPostAuthor = "no information";
		}
		
		if($this->latestPostTimestamp == null) {
			$this->latestPostTimestamp = "no information";
		}

		$arr = array("Url" => $this->url, 
				"CourseName" => $this->courseName, 
				"CourseCode" => $this->courseCode, 
				"CoursePlanUrl" => $this->coursePlanUrl, 
				"CourseDescription" => $this->courseDescription, 
				"LatestPostTitle" => $this->latestPostTitle,
				"LatestPostAuthor" => $this->latestPostAuthor,
				"LatestPostTimeStamp" => $this->latestPostTimestamp);

		return $arr;
	}
}




