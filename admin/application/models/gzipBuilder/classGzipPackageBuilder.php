<?php

class GzipPackageBuilder {

    public function __construct() {

    }

    public function buildGzipPackage($outputFile, $inputFiles, Compressor $compressor = NULL) {

        $this->buildPackage($outputFile, $inputFiles);

        if(isset($compressor)) {
            $compressor->compress($outputFile, $outputFile.'_min');
        }

        $this->buildGzip($outputFile, $outputFile.'_min');
    }

    public function buildPackage($outputFile, $inputFiles) {

        $packageStr = '';

        if(is_array($inputFiles)) {
            foreach($inputFiles as $file) {
                $packageStr .= file_get_contents($file);
            }
        } else {
            $packageStr = file_get_contents($inputFiles);
        }

        file_put_contents($outputFile, $packageStr);

    }

    public function buildGzip($outputFile, $inputFile) {

        $fileStr = file_get_contents($inputFile);
        file_put_contents($outputFile.'.gz', gzencode($fileStr));
    }
}

?>