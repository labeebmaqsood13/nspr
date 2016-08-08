<?php
require_once 'includes/upload.php';  
?>

<!DOCTYPE html>
<html>
<head>
	<!-- Bootstrap -->
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet"> 
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->
    <script src="bootstrap-3.3.6-dist/js/jquery-1.12.4.min.js"></script>
    <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
	<title>NSPR.</title>
</head>



<body>

<!--========================================HEADER STARTS HERE ===================================================================-->

<nav class="navbar navbar-default navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">NSPR.</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Nessus Parser <span class="caret"></span></a>
          <ul class="dropdown-menu">
            
            <li><a href="nessus/nessus_parser_pdf.php">Pdf Report</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="nessus/nessus_parser_docx.php">Word Document</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="nessus/nessus_parser_docx_update.php">Updated Word Document</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="nessus/nessus_parser_excel.php">Excel Spreadsheet</a></li> 
            <li role="separator" class="divider"></li>
            <li><a href="nessus/nessus_parser_html.php">Webpage Report</a></li>  

          </ul>
        </li>

                <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Nmap Parser <span class="caret"></span></a>
          <ul class="dropdown-menu">
            
            <li><a href="nmap/nmap_parser_pdf.php">Pdf Report</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="nmap/nmap_parser_docx.php">Word Document</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="nmap/nmap_parser_excel.php">Excel Spreadsheet</a></li> 
            <li role="separator" class="divider"></li>
            <li><a href="nmap/nmap_parser_html.php">Webpage Report</a></li>         

          </ul>
        </li>


      </ul>

      <ul class="nav navbar-nav navbar-right">
	      <form class="navbar-form navbar-left" role="search">
	        <div class="form-group">
	          <input type="text" class="form-control" placeholder="Search Nspr">
	        </div>
	        <button type="submit" class="btn btn-default">Submit</button>
	      </form>
	  </ul>


    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<!--========================================BODY STARTS HERE ===================================================================-->

<div class="container container-fluid">
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
    <div class="col-md-3 pull-left">
         <div class="form-group">
            <label for="exampleInputFile">Nmap File input</label>
            <input type="file" name="nmap_fileToUpload" id="nmap_fileToUpload">
            <p class="help-block">Choose .txt file</p>
         </div>
         <button type="submit" class="btn btn-default" name="nmap">Upload File</button>
    </div>
</form>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">          
    <div class="col-md-3 pull-left">
       <div class="form-group">
          <label for="exampleInputFile">Nessus File input</label>
          <input type="file" name="nessus_fileToUpload" id="nessus_fileToUpload">
          <p class="help-block">Choose .nessus file</p>
       </div>
       <button type="submit" class="btn btn-default" name="nessus">Upload File</button>
    </div>    
</form>
</div>

</body>
</html>