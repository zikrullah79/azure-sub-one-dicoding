<?php 
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=subzikstorage;AccountKey=ekPoRJ4XJ9v/47j+i0IIiRNDHINTzqukS1tNw8oibcbk/Rtr5r8YK3WyyaE+M2CqcwXAUqF19j4o6SbGc8wIhw==";

$containerName = "sub1pict";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST["upload"]) && isset($_FILES["browse"])) {
        $fileToUpload = strtolower($_FILES["browse"]["name"]);
          if ($fileToUpload != ''){
            try {
                // Create container.
                // $blobClient->createContainer($containerName, $createContainerOptions);
        
                // Getting local file so that we can upload it to Azure
                $myfile = fopen($fileToUpload, "w") or die("Unable to open file!");
                fclose($myfile);
                
                $content = fopen($_FILES["browse"]["tmp_name"], "r");
        
                $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
                ?><script>alert("Succesfully add Image")</script><?php
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
          }else {
            ?>
                <script>
                    alert("Check Your Form Again")
                </script>
            <?php
        }
    }
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>
<html>
 <head>
 <Title>Registration Form</Title>
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
 <style type="text/css">
 </style>
 </head>
 <body>
 <div class="container">
    <h1>Add Here !</h1>
    <p>Add Your Picture, Click <strong>upload</strong> to add.</p>
    <form class="form-inline" method="POST" action="sub2.php" enctype="multipart/form-data" >
        <div class="form-group">
            <div class="col-sm-10">
                <input type="file" name="browse" accept="image/x-png,image/jpeg" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <input class="btn btn-primary" type="submit" name="upload" />
            </div>
        </div>
    </form>
    <form  method='POST' action='analyze.php'>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                do{
                    foreach ($result->getBlobs() as $blob)
                    {
                        echo "<tr><td>".$blob->getName()."</td><td>
                        <input name='submit' type ='submit' value='analyze'></input><input type='hidden' name='img' value = '".$blob->getUrl()."'/></td></tr>";
                    }
                
                    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                } while($result->getContinuationToken());
            ?>
            </tbody>
        </table>
        </form>
 </div>
 </body>
</html>
