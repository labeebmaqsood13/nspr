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



//-----------------COUNTRY--------------------------------------------------------
	//Regex Here
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


//-----------------MAC--------------------------------------------------------

	//Regex Here
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



//-----------------OS--------------------------------------------------------

	//Regex Here
	foreach($each_line as $line){
		if($os = $regex->os($line) ){
			$os=$os[0];

			//Db insert
			$query_os = "UPDATE report_details SET os = '$os' WHERE id=$lastInsertedId";
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


//------------------------------REPORT_TABLE---------------------------------

	$report_table= [];
	foreach($each_line as $line){
		if($table = $regex->table($line) ){
			$table_components = preg_split('/\s+/', $table[0]);
			$port = $table_components[0];
			$port_status = $table_components[1];
			$service = $table_components[2];

			//Db insert
				$query_port = "INSERT INTO report_table (port, port_status, service) values('$port', '$port_status', '$service');";
				if($db->insert_query($query_port) == FALSE){
					echo "Error creating this record ". $db->error;
				}	

	 		// Saving an array of lastInserted and Port
			$lastInsertedId = $db->connection->insert_id;
			$report_table[$lastInsertedId] = $port;

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


?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>NSPR Report.</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>
    <body>
    	<div class="container">
	        <table class="table table-hover" border="1">
	            <tr>
		            <th>IP Address</th>
					<td  colspan="10"><?php echo $new_ip; ?></td>
	            </tr>
	            <tr>
		            <th>Date and Time</th>
					<td  colspan="10"><?php echo $new_date_time; ?></td>
	            </tr>
	            <tr>
		            <th>Host Status</th>
					<td colspan="10"><?php echo $new_host; ?></td>
	            </tr>
	            <tr>
		            <th>Country</th>
					<td  colspan="10"><?php echo $new_country; ?></td>
	            </tr>
	            <tr>
		            <th>MAC Address</th>
					<td  colspan="10"><?php echo $new_mac; ?></td>
	            </tr>
	            <tr>
		            <th>Operating System</th>
					<td  colspan="10"><?php echo $new_os; ?></td>
	            </tr>
	<!--             <tr>
	            	<th class="text-center">Complete Running Services</th>
	            </tr> -->
	            <tr>
		            <th>Port</th>
					<th>Port Status</th>
					<th>Service</th>
				</tr>
							
					<?php   

						$query_report_table = "SELECT port,port_status,service FROM report_table";
						$result = $db->insert_query($query_report_table);

						if($result->num_rows >0){
							while($row = $result->fetch_assoc()){


								$new_port = $row["port"];
								echo "<tr><td>{$new_port}</td>";
								$new_port_status = $row["port_status"];
								echo "<td>{$new_port_status}</td>";
								$new_service = $row["service"];
								echo "<td>{$new_service}</td></tr>";
							}
						}
			
						$db->connection->close();
						fclose($file->file_pointer);
					?>
	            
	        </table>
	    </div>    
    </body>
</html>
