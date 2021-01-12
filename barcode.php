<?php
// include liberary
include('./lib/class.barcode.php');


// get main barcode data
$barcodeCaption = isset($_GET['caption']) ? $_GET['caption'] : '';
$barcodePrefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
$barcodeString = isset($_GET['text']) ? $_GET['text'] : '';
$barcodeCopyright = isset($_GET['footer']) ? $_GET['footer'] : '';

// get foreground color
$barcodeCopyrightTextcolor = isset($_GET['f_textcolor']) ? $_GET['f_textcolor'] : '000000';
if (strlen($barcodeCopyrightTextcolor) == 3)
	$barcodeCopyrightTextcolor .= $barcodeCopyrightTextcolor;

// get background color
$barcodeCopyrightBgcolor = isset($_GET['f_bgcolor']) ? $_GET['f_bgcolor'] : 'ffffff';
if (strlen($barcodeCopyrightBgcolor) == 3)
	$barcodeCopyrightBgcolor .= $barcodeCopyrightBgcolor;

// get scale & code type
$barcodeWidthScale = isset($_GET['wscale']) ? (is_numeric($_GET['wscale']) ? $_GET['wscale'] : 1) : 1;
$codeType = isset($_GET['type']) ? $_GET['type'] : 'code128';


// create barcode
$barcode = new barcode($barcodeString, $barcodePrefix, $barcodeCaption, $barcodeCopyright, $barcodeCopyrightTextcolor, $barcodeCopyrightBgcolor, $barcodeWidthScale, $codeType);
$barcode->createBarcode();
// $barcode->saveBarcode();
$barcode->showBarcode();
