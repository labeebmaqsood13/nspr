<?php

//--------------------------------------READING FILE---------------------------------------------------------

	require_once('../includes/filehandling.php');

	$reading   = $file->open_file("../includes/nmap_input_file/scan.txt");
	$each_line = $file->each_line;

//-------------------------------------DATABASE CLASS----------------------------------------------------------

	require_once('../includes/database.php');
	$db = new Database('nspr_nmap');


	//Connection Testing
	// if($db->connection){
	// 	echo "True";
	// }

	//Dropping tables
	$db->insert_query("DROP TABLE bridge_table;");
	$db->insert_query("DROP TABLE report_table;");
	$db->insert_query("DROP TABLE report_details;");

	//Creating Tables
	$sql = "CREATE TABLE report_table(
				id int not null auto_increment primary key,
				port varchar(255),
				port_status varchar(255),
				service varchar(255));";

	$sql_1 = "CREATE TABLE report_details(
				id int not null auto_increment primary key,
				ip varchar(255),
				date_time varchar(255),
				host_status varchar(255), 
				country varchar(255),
				os varchar(255),
				mac varchar(255));";	

	$sql_2 = "CREATE TABLE bridge_table(
				id int not null auto_increment primary key,
				report_details_id int(11), 
				report_table_id int(11));";
	$sql_foreign_key = " ALTER TABLE bridge_table
						 ADD CONSTRAINT fk_bridge_table_to_report_table 
						 foreign key(report_table_id) REFERENCES report_table(id); ";
	$sql_foreign_key_1 = " ALTER TABLE bridge_table
						   ADD CONSTRAINT fk_bridge_table_to_report_details
						   FOREIGN KEY(report_details_id) REFERENCES report_details(id); ";					

	$db->insert_query($sql);
	$db->insert_query($sql_1);
	$db->insert_query($sql_2);
	$db->insert_query($sql_foreign_key);				
	$db->insert_query($sql_foreign_key_1);	

	
//---------------------------------------- PHPWORD ---------------------------------------------------

	require_once '..\lib\PHPWord-master\src\PhpWord\Autoloader.php';
	\PhpOffice\PhpWord\Autoloader::register();	
	$phpWord = new \PhpOffice\PhpWord\PhpWord();

	$section = $phpWord->createSection();

	// $section->addTitle('NSPR Report.', array('size'=>18, 'align'=> 'center'));
	$section->addText('NSPR Report.',array('name' => 'Tahoma', 'size' => 18, 'italic'=>true, 'color'=>'006699') );
	$section->addTextBreak(1);


//-----------------------IP--------------------------------

	//Regex
	require_once('../includes/regex.php');	
	$ip = $regex->ip($reading);
	
	//Db insert
	$query_ip = "INSERT INTO report_details (ip) values('$ip');";
	$db->insert_query($query_ip);

	//Db select
	$lastInsertedId = $db->connection->insert_id;
	$query_ip_1 = "SELECT ip FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_ip_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_ip = $row["ip"];
		}
	}

	//Saving an array of lastInserted and Ip
	$report_details = array($lastInsertedId => $ip);
		// echo "<br><pre>";
		// print_r($first_array);
		// echo "</pre> <br>";

	//Docx here
	$section->addText('IP: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_ip,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-------------------Date time----------------------------------

	//Regex here
	$date_time = $regex->date_time($reading);

	//Db insert
	$query_date_time = " UPDATE report_details SET date_time = '$date_time' WHERE id=$lastInsertedId";
	$db -> insert_query($query_date_time);

	//Db Select
	$query_date_time_1 = "SELECT date_time FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_date_time_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_date_time = $row["date_time"];
		}	
	}

	//Docx here
	$section->addText('Date and Time: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_date_time,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------HOST STATUS---------------------------------------------

	//Regex here
	$host = $regex->host($reading);


	//Db insert
	$query_host = " UPDATE report_details SET host_status = '$host' WHERE id=$lastInsertedId";
	$db -> insert_query($query_host);

	//Db Select
	$query_host_1 = "SELECT host_status FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_host_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host = $row["host_status"];
		}	
	}

	// Docx here
	$section->addText('Host Status: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_host,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------COUNTRY--------------------------------------------------------
	//Regex Here
	// $Country= "";
	foreach($each_line as $line){
		if($country = $regex->country($line)){
			$country = preg_split('/\s/', $country[0]);
			$country = $country[3];
			  
			//Db insert
			$query_country="UPDATE report_details SET country = '$country' WHERE id=$lastInsertedId";
			$db -> insert_query($query_country);
		}	
	}

	//Db Select
	$query_country_1 = "SELECT country FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_country_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_country = $row["country"];
		}	
	}

	//Docx here
	$section->addText('Country: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_country,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);	

//-----------------MAC--------------------------------------------------------

	//Regex Here
	// $Mac="";
	foreach($each_line as $line){
		if($mac = $regex->mac($line) ){
			$mac = $mac[0];
			$mac = explode(' ',$mac);
			$mac = $mac[2];

			//Db insert
			$query_mac = "UPDATE report_details SET mac = '$mac' WHERE id=$lastInsertedId";
			$db -> insert_query($query_mac);
		}	
	}	




	//Db Select
	$query_mac_1 = "SELECT mac FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_mac_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_mac = $row["mac"];
		}	
	}

	//Docx here
	$section->addText('MAC Address: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_mac,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------OS--------------------------------------------------------

	//Regex Here
	foreach($each_line as $line){
		if($os = $regex->os($line) ){
			$os=$os[0];

			//Db insert
			$query_os = "UPDATE report_details SET os = '$os[0]' WHERE id=$lastInsertedId";
			$db -> insert_query($query_os);

		}	
	}


	//Db Select
	$query_os_1 = "SELECT os FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_os_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_os = $row["os"];
		}	
	}

	//Docx here
	$section->addText('Operating System: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_os,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);


//------------------------------REPORT_TABLE---------------------------------

	$section->addText('Complete Running Services: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );

	$section->addTextBreak(1);

	$table =$section->addTable();
	$table->addRow(900);
	$table->addCell(2000)->addText('Port', array('color'=>'006699'));
	$table->addCell(2000)->addText('Port Status', array('color'=>'006699'));
	$table->addCell(2000)->addText('Service', array('color'=>'006699'));

	$report_table= [];
	foreach($each_line as $line){
		if($table = $regex->table($line) ){
			$table_components = preg_split('/\s+/', $table[0]);
			$port = $table_components[0];
			$port_status = $table_components[1];
			$service = $table_components[2];

			//Db insert
				$query_port = "INSERT INTO report_table (port, port_status, service) values('$port', '$port_status', '$service');";
				$db->insert_query($query_port);

		 	//Db select
			$lastInsertedId = $db->connection->insert_id;
			$query_report_table = "SELECT port,port_status,service FROM report_table where id=$lastInsertedId";
			$result = $db -> insert_query($query_report_table);
			if($result->num_rows >0){
				while($row = $result->fetch_assoc()){
					$new_port = $row["port"];
					$new_port_status = $row["port_status"];
					$new_service = $row["service"];

					//Saving an array of lastInserted and Port
					$report_table[$lastInsertedId] = $new_port;
				}
			}


			//Docx Here

			// Add table style		
			$table =$section->addTable();
			$table->addRow(900);
			$table->addCell(2000)->addText($new_port);
			$table->addCell(2000)->addText($new_port_status);
			$table->addCell(2000)->addText($new_service);

		}
	}



//--------------------------INSERTING DATA IN BRIDGE TABLE---------------------------------------------------------------------	
	
	$keys_report_details = array_keys($report_details);

	$keys_report_table = array_keys($report_table);

	foreach($keys_report_details as $report_details_id){
		foreach ($keys_report_table as $report_table_id) {
	 		if($db->insert_query(" INSERT INTO bridge_table(report_details_id,report_table_id) VALUES ($report_details_id,$report_table_id)") == FALSE){
	 			echo "Error while inserting into bridge table: " . $db->error;
	 		}
		}
	}	


//---------------------------------------- PHPWORD WRITER ------------------------------------------

	$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	$file = 'HelloWorld.docx';
	$objWriter->save($file);
	
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$file);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	flush();
	readfile($file);
	unlink($file); // deletes the temporary file
	exit;

//-----------------------------------CLOSING FILE----------------------------------------------------

	$db->close();
	fclose($scan_file);	

?>