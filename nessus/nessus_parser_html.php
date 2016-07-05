<?php


//--------------------------------------READING FILE---------------------------------------------------------

	require_once('..\includes\filehandling.php');

	$reading   = $file->open_file('..\includes\nessus_input_file\faisal_sc.nessus');
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
	
	$sql_foreign_key = "ALTER TABLE bridge_table
						 ADD CONSTRAINT fk_bridge_table_to_report_items 
						 foreign key(report_items_id) REFERENCES report_items(id); ";
	$sql_foreign_key_1 = "ALTER TABLE bridge_table
						   ADD CONSTRAINT fk_bridge_table_to_report_details
						   FOREIGN KEY(report_details_id) REFERENCES report_details(id); ";						 

	$db->insert_query($sql);
	$db->insert_query($sql_1);
	$db->insert_query($sql_2);
	$db->insert_query($sql_foreign_key);
	$db->insert_query($sql_foreign_key_1);


	
//-----------------------Policy Name--------------------------------
	// Regex here
	require_once('..\includes\regex_nessus.php');
	$policy_name = $regex->policy_name($reading);

	//Db insert
	$query_policy_name = "INSERT INTO report_details (policy_name) values('$policy_name');";
	$db->insert_query($query_policy_name);

	//Db select
	$lastInsertedId = $db->connection->insert_id;
	$query_policy_name_1 = "SELECT policy_name FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_policy_name_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_policy_name = $row["policy_name"];
		}
	}


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
	$result = $db->insert_query($query_host_end_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_end = $row["host_end"];
		}
	}


//-----------------------PATCH SUMMARY TOTAL CVES --------------------------------
	// Regex here
	$patch_summary_total_cves = $regex->patch_summary_total_cves($reading);


	//Db insert
	$query_patch_summary_total_cves = "UPDATE report_details SET patch_summary_total_cves = '$patch_summary_total_cves' WHERE id=$lastInsertedId;";
	$db->insert_query($query_patch_summary_total_cves);

	//Db select
	$query_patch_summary_total_cves_1 = "SELECT patch_summary_total_cves FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_patch_summary_total_cves_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_patch_summary_total_cves = $row["patch_summary_total_cves"];
		}
	}


//-----------------------CPE--------------------------------
	// Regex here
	$cpe = $regex->cpe($reading);

	//Db insert
	$query_cpe = "UPDATE report_details SET cpe = '$cpe' WHERE id=$lastInsertedId;";
	$db->insert_query($query_cpe);	

	//Db select
	$query_cpe_1 = "SELECT cpe FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_cpe_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_cpe = $row["cpe"];
		}
	}


//-----------------------OS--------------------------------
	// Regex here
	$os = $regex->os($reading);


	//Db insert
	$query_os = "UPDATE report_details SET os = '$os' WHERE id=$lastInsertedId;";
	$db->insert_query($query_os);

	//Db select
	$query_os_1 = "SELECT os FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_os_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_os = $row["os"];
		}
	}


//-----------------------HOST IP--------------------------------
	// Regex here
	$host_ip = $regex->host_ip($reading);

	
	//Db insert
	$query_host_ip = "UPDATE report_details SET host_ip = '$host_ip' WHERE id=$lastInsertedId;";
	$db->insert_query($query_host_ip);

	//Db select
	$query_host_ip_1 = "SELECT host_ip FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_host_ip_1);
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


//-----------------------NETBIOS NAME--------------------------------
	// Regex here
	$netbios_name = $regex->netbios_name($reading);

	//Db insert
	$query_netbios_name = "UPDATE report_details SET netbios_name = '$netbios_name' WHERE id=$lastInsertedId;";
	$db->insert_query($query_netbios_name);	

	//Db select
	$query_netbios_name_1 = "SELECT netbios_name FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_netbios_name_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_netbios_name = $row["netbios_name"];
		}
	}


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
	$result = $db->insert_query($query_host_start_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_start = $row["host_start"];
		}
	}


//-----------------------HOST FQDN--------------------------------
	// Regex here
	$host_fqdn = $regex->host_fqdn($reading);

	//Db insert
	$query_host_fqdn = "UPDATE report_details SET host_fqdn = '$host_fqdn' WHERE id=$lastInsertedId;";
	$db->insert_query($query_host_fqdn);

	//Db select
	$query_host_fqdn_1 = "SELECT host_fqdn FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_host_fqdn_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_host_fqdn = $row["host_fqdn"];
		}
	}


//-----------------------MAC--------------------------------
	// Regex here
	$mac = $regex->mac($reading);

	//Db insert
	$query_mac = "UPDATE report_details SET mac = '$mac' WHERE id=$lastInsertedId;";
	$db->insert_query($query_mac);

	//Db select
	$query_mac_1 = "SELECT mac FROM report_details where id=$lastInsertedId";
	$result = $db->insert_query($query_mac_1);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			$new_mac = $row["mac"];
		}
	}


//------------------------------REPORT_TABLE---------------------------------

	$report_items= [];

	//Regex Here
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
					$query_port = "INSERT INTO report_items (port, service, protocol, severity, plugin_id, plugin_name, plugin_family) values('$port', '$service', '$protocol', '$severity', '$plugin_id', '$plugin_name', '$plugin_family');";
					$db->insert_query($query_port);

					//Saving an array of lastInserted and Port
					$lastInsertedId = $db->connection->insert_id;
					$report_items[$lastInsertedId] = $port;
			
			}	


//--------------------------INSERTING DATA IN BRIDGE TABLE---------------------------------------------------------------------	
	

	$keys_report_details = array_keys($report_details);


	$keys_report_items = array_keys($report_items);



	foreach($keys_report_details as $something){
		foreach ($keys_report_items as $something_2) {
	 		$db->insert_query(" INSERT INTO bridge_table(report_details_id, report_items_id) VALUES ($something,$something_2);");
		}
	}	

						

//-----------------------------------CLOSING FILE----------------------------------------------------


?>

<!DOCTYPE html>
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
		            <th>Policy Name</th>
					<td  colspan="10"><?php echo $new_policy_name; ?></td>
	            </tr>
	            <tr>
		            <th>Host End</th>
					<td  colspan="10"><?php echo $new_host_end; ?></td>
	            </tr>
	            <tr>
		            <th>Patch Summary Total CVES</th>
					<td colspan="10"><?php echo $new_patch_summary_total_cves; ?></td>
	            </tr>
	            <tr>
		            <th>CPE</th>
					<td  colspan="10"><?php echo $new_cpe; ?></td>
	            </tr>
	            <tr>
		            <th>Operating System</th>
					<td  colspan="10"><?php echo $new_os; ?></td>
	            </tr>
	            <tr>
		            <th>Host IP</th>
					<td  colspan="10"><?php echo $new_host_ip; ?></td>
	            </tr>
	            <tr>
		            <th>Netbios Name</th>
					<td  colspan="10"><?php echo $new_netbios_name; ?></td>
	            </tr>	            
	            <tr>
		            <th>Host Start</th>
					<td  colspan="10"><?php echo $new_host_start; ?></td>
	            </tr>
	            <tr>
		            <th>Host FQDN</th>
					<td  colspan="10"><?php echo $new_host_fqdn; ?></td>
	            </tr>	            	            
	            <tr>
		            <th>MAC Address</th>
					<td  colspan="10"><?php echo $new_mac; ?></td>
	            </tr>	 
	            <tr>
		            <th>Port</th>
					<th>Service</th>
					<th>Protocol</th>
					<th>Severity</th>
					<th>Plugin ID</th>
					<th>Plugin Name</th>
					<th>Plugin Family</th>
				</tr>
							
					<?php   

						$query_report_items = "SELECT port, service, protocol, severity, plugin_id, plugin_name, plugin_family FROM report_items";
						$result = $db->insert_query($query_report_items);

						if($result->num_rows >0){
							while($row = $result->fetch_assoc()){


								$new_port = $row["port"];
								echo "<tr><td>{$new_port}</td>";
								$new_service = $row["service"];
								echo "<td>{$new_service}</td>";
								$new_protocol = $row["protocol"];
								echo "<td>{$new_protocol}</td>";

								$new_severity = $row["severity"];
								echo "<td>{$new_severity}</td>";
								$new_plugin_id = $row["plugin_id"];
								echo "<td>{$new_plugin_id}</td>";
								$new_plugin_name = $row["plugin_name"];
								echo "<td>{$new_plugin_name}</td>";
								$new_plugin_family = $row["plugin_family"];
								echo "<td>{$new_plugin_family}</td></tr>";
								

							}
						}
			
						$db->connection->close();
						fclose($file->file_pointer);
					?>
	            
	        </table>
	    </div>   

</body>
</html>