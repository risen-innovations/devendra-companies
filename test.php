<?php

if(isset($_FILES['image'])){
	$file_name = $_FILES['image']['name'];   
	$temp_file_location = $_FILES['image']['tmp_name']; 

	require 'vendor/autoload.php';
		
	$s3 = new Aws\S3\S3Client([
        		'region'  => 'ap-southeast-1',
        		'version' => 'latest',
        		'credentials' => [
            			'key'    => "AKIAILFHEMIUXHACESVQ",
            			'secret' => "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",
        		]
		]);

	$result = $s3->putObject([
			//'Bucket' => 'arn:aws:s3:::ri-company-service',
			'Bucket' => 'ri-company-service',
			'Key'    => 'learners/'.$file_name,
			'SourceFile' => $temp_file_location			
		]);

	var_dump($s3);
}		

?>
<html>
<head></head>
<body>
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>  
</body>
</html>
