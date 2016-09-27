<?php
require '../autoload.php';
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
echo $generator->getBarcode('081231723897', $generator::TYPE_CODE_128);
