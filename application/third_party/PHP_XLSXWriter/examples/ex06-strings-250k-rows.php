<?php
//~ set_include_path( get_include_path().PATH_SEPARATOR."..");
include_once("../xlsxwriter.class.php");

$chars = "abcdefghijklmnopqrstuvwxyz0123456789 ";
$s = '';
for($j=0; $j<16192;$j++)
	$s.= $chars[rand()%36];


$writer = new XLSXWriter();
$writer->writeSheetHeader('Sheet1', array('c1'=>'string','c2'=>'string','c3'=>'string','c4'=>'string') );//optional
for($i=0; $i<25000; $i++)
{
	$s1 = substr($s, rand()%4000, rand()%5+5);
	$s2 = substr($s, rand()%8000, rand()%5+5);
	$s3 = substr($s, rand()%12000, rand()%5+5);
	$s4 = substr($s, rand()%16000, rand()%5+5);
    $writer->writeSheetRow('Sheet1', array($s1, $s2, $s3, $s4) );
}
//~ $writer->writeToFile('xlsx-strings-250k.xlsx');
$filename = "example.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
//~ echo '#'.floor((memory_get_peak_usage())/1024/1024)."MB"."\n";

