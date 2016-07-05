<?php

class Filehandling{
	
	public $each_line;
	public $file_pointer;

	public function open_file($path){
		
		$this->file_pointer = fopen("$path","r") or die("Unable to open file!");
		$reading = fread($this->file_pointer,filesize("$path"));
		
		$this->each_line = explode(PHP_EOL,$reading);
		
		return $reading;
	}


}

$file = new Filehandling();

?>
