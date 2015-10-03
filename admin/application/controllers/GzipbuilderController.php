<?php

include_once('models/gzipBuilder/classGzipPackageBuilder.php');
include_once('models/gzipBuilder/classYuiCompressor.php');
include_once('models/gzipBuilder/classCompressor.php');

class GzipbuilderController extends MainApplicationController {

    #PIVATE VARIABLES

    #PUBLIC VARIABLES

    public function init() {
        parent::init();
    }

    public function __destruct() {
        $this->display();
    }

    public function indexAction() {

        $this->_redirect('/gzipbuilder/build/');
    }

    public function buildAction() {

        

        $compressor = new Compressor();
        $gzipBuilder = new GzipPackageBuilder();


        $inputFile = '/home/www/test/buynow.js';
        $outputFile  = '/home/www/test/buynow_res.js';

        $gzipBuilder->buildGzipPackage($outputFile, $inputFile, $compressor);

    }
}

?>