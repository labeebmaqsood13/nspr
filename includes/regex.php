<?php

class Regex{

	public $ip 		  = "/(for.*?([0-9]+){3}(\.[0-9]+){3})/";
	public $date_time = "/(?<=at)\s\d{4}\-\d{2}\-\d{2}\s\d{2}\:\d{2}/";
	public $host 	  = "/(H|h)ost is\s+(up|down)/";
	public $country   = "/(?<=at)\s\d{4}\-\d{2}\-\d{2}\s\d{2}\:\d{2}\s.*?$/";
	public $mac 	  = "~^(MAC|mac|Mac)\s(address|Address|ADDRESS)\s*\:\s+.*?:.*?:.*?:.*?:.*?:.*?\s+~";
	public $os 		  = "%^(OS|os|Os|oS)\s(details|Details|DETAILS|dETAILS)\:.*?$%";
	public $table	  = "#\d*/\w+\s+(open|closed|unknown|close)\s+\w+#";

	public function ip($reading){

		preg_match("$this->ip",$reading,$ip);
		$ip = preg_split('/\s/',$ip[1]);
		return $ip[1];
	}


	public function date_time($reading){

		preg_match("$this->date_time",$reading,$date_time);
		return $date_time[0];
	}	


	public function host($reading){

		preg_match("$this->host",$reading,$host);
		return $host[2];
	}		

	public function country($each_line){

		preg_match("$this->country",$each_line,$country);
		// $country = preg_split('/\s/', $country[0]);
		// $country = $country[3];

		return $country;
	}

	public function mac($each_line){

		preg_match("$this->mac",$each_line,$mac);

		return $mac;
	}

	public function os($each_line){

		preg_match("$this->os",$each_line,$os);

		return $os;
	}

	public function table($each_line){

		preg_match("$this->table",$each_line,$table);

		return $table;
	}

}

$regex = new Regex();

?>
