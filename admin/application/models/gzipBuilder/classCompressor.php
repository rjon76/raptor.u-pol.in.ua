<?php

class Compressor {

    private $compressorHandle;

    const JS = 'js';
    const CSS = 'css';

    public function __construct($compressorName = 'Yui', $charset = NULL) {

        $className = ucfirst(strtolower($compressorName)).'Compressor';

        try {
            $this->compressorHandle = new $className($charset);
        }catch (Exception $exception) {
            echo 'Error in line '.$exception->getLine();
            echo $exception->getMessage();
        }

    }

    public function __destruct() {

        $this->compressorHandle = NULL;

    }

    public function compress($inputFile ,
                             $outputFile = 'scripts.js',
                             $type = NULL,
                             $lineBreak = NULL,
                             $donotObfuscate = FALSE,
                             $preserveSemicolons = FALSE,
                             $disableOptimizations = FALSE) {

        $this->compressorHandle->compress($inputFile,
                                          $outputFile,
                                          $type,
                                          $lineBreak,
                                          $donotObfuscate,
                                          $preserveSemicolons,
                                          $disableOptimizations);

    }

}

?>