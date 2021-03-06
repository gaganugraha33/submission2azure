<?php
/**----------------------------------------------------------------------------------
* Microsoft Developer & Platform Evangelism
*
* Copyright (c) Microsoft Corporation. All rights reserved.
*
* THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, 
* EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES 
* OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
*----------------------------------------------------------------------------------
* The example companies, organizations, products, domain names,
* e-mail addresses, logos, people, places, and events depicted
* herein are fictitious.  No association with any real company,
* organization, product, domain name, email address, logo, person,
* places, or events is intended or should be inferred.
*----------------------------------------------------------------------------------
**/

/** -------------------------------------------------------------
# Azure Storage Blob Sample - Demonstrate how to use the Blob Storage service. 
# Blob storage stores unstructured data such as text, binary data, documents or media files. 
# Blobs can be accessed from anywhere in the world via HTTP or HTTPS. 
#
# Documentation References: 
#  - Associated Article - https://docs.microsoft.com/en-us/azure/storage/blobs/storage-quickstart-blobs-php 
#  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/ 
#  - Getting Started with Blobs - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-blobs/
#  - Blob Service Concepts - http://msdn.microsoft.com/en-us/library/dd179376.aspx 
#  - Blob Service REST API - http://msdn.microsoft.com/en-us/library/dd135733.aspx 
#  - Blob Service PHP API - https://github.com/Azure/azure-storage-php
#  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/ 
#
**/

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=submission2blobstorage;AccountKey=ECkAYjCPSuwBL1LF/8ZHoGVjD9Tk5QLZyrTEim+dEs7xpm5DL3sqLS4pPusLR+PFXAwO+MXRSI6S12bALd3ICA==;";

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "imagesblob";

//$fileToUpload = "HelloWorld.txt";

if (isset($_POST["submit"])) {
    
   
    try {
        // Create container.
		$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
        
        $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");

        //Upload blob
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
		header("Location: index.php");

        
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
} 

?>
<html>
<head>
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>Submission 2 azure</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
  </head>
  <body>
	  <main role="main" class="container">
		<div class="starter-template"> <br><br><br>
		</div>
			<div class="mt-4 mb-2">
						<form class="d-flex justify-content-lefr" action="index.php" method="post" enctype="multipart/form-data">
							<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
							<input type="submit" name="submit" value="Upload">
						</form>
			</div>
			<br>
			<br>
			<h4>File yang telah di upload</h4>
			<table class='table table-hover'>
				<thead>
					<tr>
						<th>File Name</th>
						<th>File URL</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					// List blobs.
					$listBlobsOptions = new ListBlobsOptions();
					$listBlobsOptions->setPrefix("");
					
					do {
				
						$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
						foreach ($result->getBlobs() as $blob)
						{
						
							?>
							<tr>
								<td><?php echo $blob->getName() ?></td>
								<td><?php echo $blob->getUrl() ?></td>
								<td>
									<form action="computervision.php" method="post">
										<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
										<input type="submit" name="submit" value="Analyze!" class="btn btn-primary">
									</form>
								</td>
							</tr>
							<?php
						}
						$listBlobsOptions->setContinuationToken($result->getContinuationToken());
					} while($result->getContinuationToken());
					
					 $blob = $blobClient->getBlob($containerName, $fileToUpload);
					fpassthru($blob->getContentStream());
					?>
				</tbody>
			</table>
	</div>	
	
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
  </body>
</html>
