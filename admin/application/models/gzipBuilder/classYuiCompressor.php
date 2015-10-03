<?php

class YuiCompressor {

    private $charset;
    private $showWarnings;

    public function __construct($charset = NULL, $showWarnings = FALSE) {

        $this->charset = $charset;
        $this->showWornings = $showWarnings;

    }

    public function __destruct() {

        $this->charset = NULL;
        $this->showWornings = NULL;

    }

    public function compress($inputFile,
                             $outputFile,
                             $type = NULL,
                             $lineBreak = FALSE,
                             $donotObfuscate = FALSE,
                             $preserveSemicolons = FALSE,
                             $disableOptimizations = FALSE) {

        $command = $inputFile.' -o '.$outputFile;

        if(isset($type)) {
            $command .= ' --type  '.$type;
        }

        if(isset($this->charset)) {
            $command .= ' --charset '.$this->charset;
        }

        if(isset($lineBreak)) {
            $command .= ' --line-break';
        }

        if($donotObfuscate) {
            $command .= ' --nomunge';
        }

        if($preserveSemicolons) {
            $command .= ' --preserve-semi';
        }

        if($disableOptimizations) {
            $command .= ' --disable-optimizations';
        }

        if($this->showWarnings) {
            $command .= ' --verbose';
        }

        exec('java -jar /home/www/venginse_admin/application/utilities/yuicompressor.jar '.$command);


    }
}

?>