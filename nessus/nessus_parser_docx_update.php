<?php
// error_reporting(E_ALL ^ E_NOTICE);
// ini_set('memory_limit', '500M');


//--------------------------------------READING FILE---------------------------------------------------------

	require_once('..\includes\filehandling.php');

	$reading   = $file->open_file('..\includes\nessus_input_file\faisal_sc.nessus');
	$each_line = $file->each_line;

	
//---------------------------------------- PHPWORD ---------------------------------------------------

	require_once '..\lib\PHPWord-master\src\PhpWord\Autoloader.php';
	\PhpOffice\PhpWord\Autoloader::register();	
	$phpWord = new \PhpOffice\PhpWord\PhpWord();

	$section = $phpWord->createSection();

	// $section->addTitle('NSPR Report.', array('size'=>18, 'align'=> 'center'));
	$section->addText('NSPR Report.',array('name' => 'Tahoma', 'size' => 18, 'italic'=>true, 'color'=>'006699') );
	$section->addTextBreak(1);




//======================================TABLE 1 CODE =====================================================/

	// ---------------- XML to JSON -------------------------//

			$xml    = simplexml_load_file('..\includes\nessus_input_file\faisal_sc.nessus');
			$json   = json_encode($xml);
			$nessus = json_decode($json,TRUE);
				
			// Counting ReportHosts
			$count=0;
			foreach($nessus['Report']['ReportHost'] as $something){
				$count = $count+1;
			}


			// Making Array of counted vulnerabilities
			$array = [];
			for($i=0; $i<$count; $i++){

				if(isset($nessus['Report']['ReportHost'][$i]['ReportItem']['1'])){
					
					$jcount = count($nessus['Report']['ReportHost'][$i]['ReportItem']);
					$array[$i] = $jcount;

				}else{
					
					$jcount = 1;
					$array[$i] = $jcount;

				}
				
			}	


	//---------- Host Properties-----------------------------//

			preg_match_all('~(?<=<HostProperties>).*?(?=</HostProperties)~s', $reading, $host_properties);
		
			$count_out = 0;	
			$count_in  = 0;
			$array_hostproperties;
			foreach($host_properties[0] as $host_property){

				$result_host_ip = preg_match('#(?<=("host-ip">))((?:.|\n)*?)(?=</tag)#', $host_property, $host_ip);
				if($result_host_ip){

					$array_hostproperties[$count_out][$count_in] = $host_ip[0];
					$count_in = $count_in + 1; 

				}
				else{
					// echo " - ";
				}


				$result_mac = preg_match('#(?<=("mac-address">))((?:.|\n)*?)(?=</tag)#', $host_property, $mac);
				if($result_mac == 1){
					// var_dump(htmlspecialchars($mac[0]));
					$array_hostproperties[$count_out][$count_in] = $mac[0];
					$count_in = $count_in + 1; 					
				}
				elseif($result_mac == 0){
					$result_os = preg_match('#(?<=("os">))((?:.|\n)*?)(?=</tag)#', $host_property, $os);
					if($result_os){
						// echo "OS = ";
						// var_dump(htmlspecialchars($os[0]));
						$array_hostproperties[$count_out][$count_in] = $os[0];
						$count_in = $count_in + 1; 	
					}else{
						// echo " - ";
						$array_hostproperties[$count_out][$count_in] = " - ";
						$count_in = $count_in + 1; 	
					}
				}


				
				$result_system_type = preg_match('#(?<=("system-type">))((?:.|\n)*?)(?=</tag)#', $host_property, $system_type);
				if($result_system_type){
					// var_dump(htmlspecialchars($system_type[0]));
					$array_hostproperties[$count_out][$count_in] = $system_type[0];
					$count_in = $count_in + 1; 	
				}else{
					// echo " - ";
					$array_hostproperties[$count_out][$count_in] = " - ";
					$count_in = $count_in + 1; 	
				}


				$result_operating_system = preg_match('#(?<=("operating-system">))((?:.|\n)*?)(?=</tag)#', $host_property, $operating_system);
				if($result_operating_system){
					// var_dump(htmlspecialchars($operating_system[0]));
					$array_hostproperties[$count_out][$count_in] = $operating_system[0];
					$count_in = $count_in + 1; 	
				}else{
					// echo " - ";
					$array_hostproperties[$count_out][$count_in] = " - ";
					$count_in = $count_in + 1; 	
				}

				$array_hostproperties[$count_out][$count_in] = $array[$count_out];
				$count_in = 0;

				$count_out = $count_out + 1;
			}
			
	// ----------- Sorting array according to count of vulnerability -------------//

			$new_array = array();
			foreach ($array_hostproperties as $key => $value)
			{
			    $new_array[$key] = $value['4'];
			    		 
			}
			array_multisort($new_array, SORT_DESC, $array_hostproperties);


	// ------------------- table 1 --------------------------------------//

			$section->addText('TABLE 1: List of most vulnerable assets in the client’s network',array('name' => 'Tahoma', 'size' => 14, 'color'=>'red', 'italic'=>true) );
			$section->addTextBreak(1);
			$table =$section->addTable();
			$table->addRow(900);
			$table->addCell(1000)->addText('Sr.', array('color'=>'006699'));
			$table->addCell(2500)->addText('IP Address', array('color'=>'006699'));
			$table->addCell(3000)->addText('MAC Address', array('color'=>'006699'));
			$table->addCell(3500)->addText('Asset Type*', array('color'=>'006699'));
			$table->addCell(2500)->addText('Count', array('color'=>'006699'));
			
			$table_1 =$section->addTable();

			$count = 1;
			foreach($array_hostproperties as $host_property){
				$table_1->addRow(900);
				$table_1->addCell(1000)->addText($count);
				$table_1->addCell(2500)->addText($host_property[0]);
				$table_1->addCell(3000)->addText($host_property[1]);
				$table_1->addCell(3000)->addText($host_property[2].'                                        '.$host_property[3]);
				$table_1->addCell(2500)->addText('        '.$host_property[4]);


				$count = $count + 1;
			}	

//======================================TABLE 2 CODE =====================================================/

	// -----------Plugin name extraction from report items using xml json -------------//
			$count=0;
			foreach($nessus['Report']['ReportHost'] as $something){
				$count = $count+1;
			}


			// Making Array of counted vulnerabilities
			$report_items = [];

			$plugin_name_array = [];
			$counter = 0;


			for($i=0; $i<$count; $i++){

				if(isset($nessus['Report']['ReportHost'][$i]['ReportItem']['1'])){

					$jcount = count($nessus['Report']['ReportHost'][$i]['ReportItem']);
					
					for($j=0; $j<$jcount; $j++)
					{

						$plugin_name_array[$counter] = $nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['@attributes']['pluginName'];

						$counter = $counter + 1; 

					}
		
				}else{

					$jcount = 1;

					for($j=0; $j<$jcount; $j++)
					{

						$plugin_name_array[$counter] = $nessus['Report']['ReportHost'][$i]['ReportItem']['@attributes']['pluginName'];
						$counter = $counter + 1; 

					}

				}

			}	

			$plugin_name_array_count = array_count_values($plugin_name_array);

			$old_plugin_name_array_count = $plugin_name_array_count;
			rsort($plugin_name_array_count);
			$new_plugin_name_array_count = [];

			foreach($plugin_name_array_count as $plugin_name => $count){
				foreach($old_plugin_name_array_count as $old_plugin_name => $old_count){
					if($count == $old_count){
						$new_plugin_name_array_count[$old_plugin_name] = $count; 
					}
				}
			}


	//------------------- Table 2 --------------------------------------//

			$section->addTextBreak(1);
			$section->addText('TABLE 2: Top 10 Vulnerability names with its total count / occurence',array('name' => 'Tahoma', 'size' => 14, 'color'=>'red', 'italic'=>true) );
			$section->addTextBreak(1);
			$table2 = $section->addTable();
			$table2->addRow(100);
			$table2->addCell(15000)->addText('Exploit Category', array('color'=>'006699'));
			$table2->addCell(2500)->addText('Infected Assets', array('color'=>'006699'));

			$table_3 = $section->addTable();
			
			

			$new_plugin_array = [];
			$i=0;
			foreach($new_plugin_name_array_count as $key => $value){
				$new_plugin_array[$i] = [$key,$value];
				$i = $i + 1;
			}
			
			$counter=0;
			foreach($new_plugin_array as $new){
				if($counter<10){
					$table_3->addRow(100);
					$table_3->addCell(15000)->addText(htmlspecialchars($new[0]));
					$table_3->addCell(2500)->addText('          '.htmlspecialchars($new[1]));
				}
				$counter = $counter + 1;
			}

//======================================TABLE 3 CODE =====================================================/
	
	//--------- Retriveing all the vulnerabilities and its description + solution------//
		$count=0;
		foreach($nessus['Report']['ReportHost'] as $something){
			$count = $count+1;
		}


		$plugin_name_array = [];
		$counter = 0;
		for($i=0; $i<$count; $i++){
				
				$jcount = count($nessus['Report']['ReportHost'][$i]['ReportItem']);
				for($j=0; $j<$jcount; $j++)
				{
					$plugin_name_array[$counter] = [$nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name'], $nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['description'], $nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['solution']];
					$counter = $counter + 1;	
				}

		}


		//------------------------Retrieving all the ips and mac that are common in a vulnerability------------//
		
		$array_ip = [];
		$array_plugin_name = [];

		for($i=0; $i<$count; $i++){

				$match_mac="-";
				foreach($nessus['Report']['ReportHost'][$i]['HostProperties']['tag'] as $each){
						

						if(preg_match('~^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$~', $each, $mac)){
							
							$match_mac = $mac[0];
													
						}


				}

				$jcount = count($nessus['Report']['ReportHost'][$i]['ReportItem']);
				for($j=0; $j<$jcount; $j++)
				{	

					if(array_key_exists($nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name'] ,$array_plugin_name )){


							if(!in_array($nessus['Report']['ReportHost'][$i]['@attributes']['name'], $array_plugin_name[$nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name']]) ){		

								array_push($array_plugin_name[$nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name']], $nessus['Report']['ReportHost'][$i]['@attributes']['name'], $match_mac);
						
							}	

					}

					else{	
						
						$array_plugin_name[$nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name']] = [$nessus['Report']['ReportHost'][$i]['@attributes']['name'], $match_mac];

					}

					// if(array_key_exists($nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name'] ,$array_plugin_name )){

					// 	array_push($array_plugin_name[$nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name']], 'IP'->$nessus['Report']['ReportHost'][$i]['@attributes']['name'], 'MAC'->$match_mac);

					// }

					// else{	
						
					// 	$array_plugin_name[$nessus['Report']['ReportHost'][$i]['ReportItem'][$j]['plugin_name']] = ['IP'->$nessus['Report']['ReportHost'][$i]['@attributes']['name'], 'MAC'->$match_mac];

					// }


				}


		}

	//------------------- Table 3 --------------------------------------//

			$section->addTextBreak(1);
			$section->addText('TABLE 3: Vulnerability detail with description, remedy(fix), occrances',array('name' => 'Tahoma', 'size' => 14, 'color'=>'red', 'italic'=>true) );
			$section->addTextBreak(1);
			
			$counter = 1;
			foreach($plugin_name_array as $each){
				
				$section->addText($counter.'. '.htmlspecialchars($each[0]),array('name' => 'Tahoma', 'size' => 12, 'color'=>'black', 'bold'=>true) );
				$section->addTextBreak(1);



				$table4 = $section->addTable();
				$table4->addRow(100);
				$table4->addCell(3000)->addText('           Description', array('color'=>'black', 'bold'=>true));
				$table4->addCell(13000)->addText($each[1], array('color'=>'006699'));

				$table4->addRow(100);
				$table4->addCell(3000)->addText('           Remediation', array('color'=>'black', 'bold'=>true));
				$table4->addCell(13000)->addText($each[2], array('color'=>'006699'));



				$section->addTextBreak(1);
				$section->addText('Following is the list of assets with this vulnerability:',array('name' => 'Tahoma', 'size' => 12, 'color'=>'black') );
				$section->addTextBreak(1);



				$table5 = $section->addTable();
				$table5->addRow(100);
				$table5->addCell(2000)->addText('S. No.', array('color'=>'black', 'italic'=>true, 'bold'=>true));
				$table5->addCell(8000)->addText('IP', array('color'=>'black', 'italic'=>true, 'bold'=>true));
				$table5->addCell(6000)->addText('MAC', array('color'=>'black', 'italic'=>true, 'bold'=>true));
				$section->addTextBreak(1);


				
				$ip = [];
				$ip_count = 0;

				$mac = [];
				$mac_count = 0;

				$jcount = count($array_plugin_name[$each[0]]);
				$count = 1;
				
				$i = 0;
				while($i<$jcount){

					$ip[$ip_count] = $array_plugin_name[$each[0]][$i];							
					$ip_count = $ip_count + 1;
					$i = $i +2;

				}		

				$i = 1;
				while($i<$jcount){

					$mac[$mac_count] = $array_plugin_name[$each[0]][$i];							
					$mac_count = $mac_count + 1;
					$i = $i +2;

				}		
				


				for($i=0; $i<count($ip); $i++){

					$table5->addRow(100);
					$table5->addCell(2000)->addText($count.' ', array('color'=>'006699'));
					$table5->addCell(8000)->addText($ip[$i], array('color'=>'006699'));
					$table5->addCell(6000)->addText($mac[$i], array('color'=>'006699'));
											
					$count = $count + 1;

				}				

				$counter = $counter + 1; 
				$section->addTextBreak(1);

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

	fclose($file->file_pointer);	


































?>