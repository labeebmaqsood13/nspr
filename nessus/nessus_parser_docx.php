<?php

//--------------------------------------READING FILE---------------------------------------------------------

	require_once('../includes/filehandling.php');

	$reading   = $file->open_file('../includes/nessus_input_file/faisal_sc.nessus');
	$each_line = $file->each_line;


//--------------------Database Class----------------------------------------------------------------------

	require_once('../includes/database.php');
	$db = new Database('nspr_nessus');


	$db->insert_query("DROP TABLE bridge_table;");
	$db->insert_query("DROP TABLE report_items;");
	$db->insert_query("DROP TABLE report_details;");


	
	$sql = "CREATE TABLE report_items(
				id int not null auto_increment primary key,
				port int(11),
				service varchar(255),
				protocol varchar(255),
				severity int(11),
				plugin_id int(11),
				plugin_name varchar(255),
				plugin_family varchar(255));";

	$sql_1 = "CREATE TABLE report_details(
				id int not null auto_increment primary key,
				policy_name varchar(255),
				host_end varchar(255),
				patch_summary_total_cves varchar(255), 
				cpe varchar(255),
				os varchar(255),
				host_ip varchar(255),
				netbios_name varchar(255),
				host_start varchar(255),
				host_fqdn varchar(255),
				mac varchar(255));";	

	$sql_2 = "CREATE TABLE bridge_table(
				id int not null auto_increment primary key,
				report_details_id int(11), 
				report_items_id int(11));";
	
	$sql_foreign_key = " ALTER TABLE bridge_table
						 ADD CONSTRAINT fk_bridge_table_to_report_items 
						 foreign key(report_items_id) REFERENCES report_items(id); ";
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


	
//-----------------------Policy Name--------------------------------
	// Regex here
	require_once('../includes/regex_nessus.php');
	$policy_name = $regex->policy_name($reading);


	//Db insert
	$query_policy_name = "INSERT INTO report_details (policy_name) values('$policy_name');";
	$db->insert_query($query_policy_name);	

	//Db select
	$lastInsertedId = $db->connection->insert_id;
	$query_policy_name_1 = "SELECT policy_name FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_policy_name_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_policy_name = $row["policy_name"];
		}
	}

	//Docx here
	$section->addText('Policy Name: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_policy_name,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------HOST END--------------------------------
	// Regex here
	$host_end = $regex->host_end($reading);
	$host_end = explode(' ',$host_end);
	$host_end = $host_end[0]. " ".$host_end[1]. " ".$host_end[4]. "  ".$host_end[3]; 

	//Db insert
	$query_host_end = "UPDATE report_details SET host_end = '$host_end' WHERE id=$lastInsertedId;";
	$db->insert_query($query_host_end);

	//Db select
	$query_host_end_1 = "SELECT host_end FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_host_end_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_end = $row["host_end"];
		}
	}

	//Docx here
	$section->addText('Host End: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_host_end,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------PATCH SUMMARY TOTAL CVES --------------------------------
	// Regex here
	$patch_summary_total_cves = $regex->patch_summary_total_cves($reading);

	//Db insert
	$query_patch_summary_total_cves = "UPDATE report_details SET patch_summary_total_cves = '$patch_summary_total_cves' WHERE id=$lastInsertedId;";
	$db->insert_query($query_patch_summary_total_cves);	

	//Db select
	$query_patch_summary_total_cves_1 = "SELECT patch_summary_total_cves FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_patch_summary_total_cves_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_patch_summary_total_cves = $row["patch_summary_total_cves"];
		}
	}

	//Docx here
	$section->addText('Patch Summary Total CVES: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_patch_summary_total_cves,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------CPE--------------------------------
	// Regex here
	$cpe = $regex->cpe($reading);

	//Db insert
	$query_cpe = "UPDATE report_details SET cpe = '$cpe' WHERE id=$lastInsertedId;";
	$db->insert_query($query_cpe);	

	//Db select
	$query_cpe_1 = "SELECT cpe FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_cpe_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_cpe = $row["cpe"];
		}
	}

	//Docx here
	$section->addText('CPE: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_cpe,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------OS--------------------------------
	// Regex here
	$os = $regex->os($reading);

	//Db insert
	$query_os = "UPDATE report_details SET os = '$os' WHERE id=$lastInsertedId;";
	$db->insert_query($query_os);	

	//Db select
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

//-----------------------HOST IP--------------------------------
	// Regex here
	$host_ip = $regex->host_ip($reading);
	
	//Db insert
	$query_host_ip = "UPDATE report_details SET host_ip = '$host_ip' WHERE id=$lastInsertedId;";
	$db->insert_query($query_host_ip);	

	//Db select
	$query_host_ip_1 = "SELECT host_ip FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_host_ip_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_ip = $row["host_ip"];
		}
	}

	//Saving an array of lastInserted and Ip
	$report_details = array($lastInsertedId => $host_ip);
		// echo "<br><pre>";
		// print_r($first_array);
		// echo "</pre> <br>";

	//Docx here
	$section->addText('Host IP: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_host_ip,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------NETBIOS NAME--------------------------------
	// Regex here
	$netbios_name = $regex->netbios_name($reading);

	//Db insert
	$query_netbios_name = "UPDATE report_details SET netbios_name = '$netbios_name' WHERE id=$lastInsertedId;";
	$db->insert_query($query_netbios_name);	

	//Db select
	$query_netbios_name_1 = "SELECT netbios_name FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_netbios_name_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_netbios_name = $row["netbios_name"];
		}
	}

	//Docx here
	$section->addText('Netbios Name: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_netbios_name,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------HOST START--------------------------------
	// Regex here
	$host_start = $regex->host_start($reading);
	$host_start = explode(' ',$host_start);
	$host_start = $host_start[0]. " ".$host_start[1]. " ".$host_start[4]. "  ".$host_start[3]; 

	//Db insert
	$query_host_start = "UPDATE report_details SET host_start = '$host_start' WHERE id=$lastInsertedId;";
	$db->insert_query($query_host_start);	

	//Db select
	$query_host_start_1 = "SELECT host_start FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_host_start_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_start = $row["host_start"];
		}
	}

	//Docx here
	$section->addText('Host Start: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_host_start,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------HOST FQDN--------------------------------
	// Regex here
	$host_fqdn = $regex->host_fqdn($reading);

	//Db insert
	$query_host_fqdn = "UPDATE report_details SET host_fqdn = '$host_fqdn' WHERE id=$lastInsertedId;";
	$db->insert_query($query_host_fqdn);	

	//Db select
	$query_host_fqdn_1 = "SELECT host_fqdn FROM report_details where id=$lastInsertedId";
	$result = $db -> insert_query($query_host_fqdn_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_fqdn = $row["host_fqdn"];
		}
	}

	//Docx here
	$section->addText('Host FQDN: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addText($new_host_fqdn,array('name' => 'Tahoma', 'size' => 12) );	
	$section->addTextBreak(1);

//-----------------------MAC--------------------------------
	// Regex here
	$mac = $regex->mac($reading);


	//Db insert
	$query_mac = "UPDATE report_details SET mac = '$mac' WHERE id=$lastInsertedId;";
	$db->insert_query($query_mac);

	//Db select
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


//------------------------------REPORT_TABLE---------------------------------

	$section->addText('Complete Report Items: ',array('name' => 'Tahoma', 'size' => 14, 'color'=>'006699', 'italic'=>true) );
	$section->addTextBreak(1);
	$table =$section->addTable();
	$table->addRow(900);
	$table->addCell(1000)->addText('Port', array('color'=>'006699'));
	$table->addCell(1000)->addText('Service', array('color'=>'006699'));
	$table->addCell(1000)->addText('Protocol', array('color'=>'006699'));
	$table->addCell(1000)->addText('severity', array('color'=>'006699'));
	$table->addCell(1000)->addText('Plugin ID', array('color'=>'006699'));
	$table->addCell(6000)->addText('Plugin Name', array('color'=>'006699'));
	$table->addCell(3000)->addText('Plugin Family', array('color'=>'006699'));

	$table_1 =$section->addTable();

	$report_items= [];

			$table = $regex->table($reading);

			foreach($table as $each){

					$table_components = explode('"', $each);
					
					$port          = $table_components[1];
		            $service       = $table_components[3];
			        $protocol      = $table_components[5];
			        $severity      = $table_components[7];
			        $plugin_id     = $table_components[9];
			        $plugin_name   = $table_components[11];
			        $plugin_family = $table_components[13];


					//Db insert
					$query_port = "INSERT INTO report_items (port, service, protocol, severity, plugin_id, plugin_name, plugin_family) values('$port', '$service', '$protocol', '$severity', '$plugin_id', '$plugin_name', 'plugin_family');";
					$db->insert_query($query_port);

				 	//Db select
					$lastInsertedId = $db->connection->insert_id;
					$query_report_items = "SELECT port, service, protocol, severity, plugin_id, plugin_name, plugin_family FROM report_items where id=$lastInsertedId";
					$result = $db -> insert_query($query_report_items);
					if($result->num_rows >0){
						while($row = $result->fetch_assoc()){

							$new_port          = $row["port"];
							$new_service       = $row["service"];
							$new_protocol      = $row["protocol"];
							$new_severity      = $row["severity"];
							$new_plugin_id     = $row["plugin_id"];
							$new_plugin_name   = $row["plugin_name"];
							$new_plugin_family = $row["plugin_family"];
							
							//Saving an array of lastInserted and Port
							$report_items[$lastInsertedId] = $new_port;

						}
					}


					//Docx Here
					$table_1->addRow(900);
					$table_1->addCell(1000)->addText($new_port);
					$table_1->addCell(1000)->addText($new_service);
					$table_1->addCell(1000)->addText($new_protocol);
					$table_1->addCell(1000)->addText($new_severity);
					$table_1->addCell(1000)->addText($new_plugin_id);
					$table_1->addCell(6000)->addText($new_plugin_name);
					$table_1->addCell(3000)->addText($new_plugin_family);
			}			


//--------------------------INSERTING DATA IN BRIDGE TABLE---------------------------------------------------------------------	
	

	$keys_report_details = array_keys($report_details);


	$keys_report_items = array_keys($report_items);



	foreach($keys_report_details as $something){
		foreach ($keys_report_items as $something_2) {
	 		if($db->insert_query(" INSERT INTO bridge_table(report_details_id, report_items_id) VALUES ($something,$something_2);") == FALSE){
	 			echo "Error while inserting into bridge table: " . $db->connection->error;
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

	$db->connection->close();
	fclose($file->file_pointer);	

?>