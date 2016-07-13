<?php
if(isset($_POST['nmap'])){

    $file_name  = $_FILES['nmap_fileToUpload']['name'];
    $file_temp = $_FILES['nmap_fileToUpload']['tmp_name'];
    $file_size = $_FILES['nmap_fileToUpload']['size'];
    // mkdir('includes/nmap_input_file', 0777, 'TRUE');
    $store = "includes/nmap_input_file/".$file_name;
    $fileType = pathinfo($store,PATHINFO_EXTENSION);
    // Allow certain file formats
    if($fileType != "txt"){
        echo "Sorry, only TXT files are allowed.";
    }else{
    move_uploaded_file($file_temp, $store);
    }

}elseif(isset($_POST['nessus'])){

    $file_name  = $_FILES['nessus_fileToUpload']['name'];
    $file_temp = $_FILES['nessus_fileToUpload']['tmp_name'];
    $file_size = $_FILES['nessus_fileToUpload']['size'];
    // mkdir('includes/nessus_input_file', 0777, 'TRUE');
    $store = "includes/nessus_input_file/".$file_name;
    $fileType = pathinfo($store,PATHINFO_EXTENSION);
    // Allow certain file formats
    if($fileType != "nessus"){
        echo "Sorry, only XML files are allowed.";
    }else{
    move_uploaded_file($file_temp, $store);
    }

}
?>