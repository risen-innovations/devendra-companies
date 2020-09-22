<?php 
//application/libraries/AWSS3.php
require './vendor/autoload.php';

    class AWSS3
    {
        //S3 credentials
        private $region = 'ap-southeast-1';
        private $key = "AKIAQAUZKAGO5UVGEST5";
        private $secret = "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";
        public function uploadS3($file, $bucket)
        {          
            $file_name = $_FILES['image']['name'];   
	        $temp_file_location = $_FILES['image']['tmp_name']; 
            $s3 = new Aws\S3\S3Client([
                'region'  => $this->region,
                'version' => 'latest',
                'credentials' => [
                        'key'    => $this->key,
                        'secret' => $this->secret,
                ]
            ]);

            $result = $s3->putObject([
                'Bucket' => $bucket,//'ri-company-service',
                'Key'    => $file_name,
                'Body' => $temp_file_location,
                'ACL' => 'public-read'
                //'SourceFile' => $temp_file_location			
            ]);

        }
    }
