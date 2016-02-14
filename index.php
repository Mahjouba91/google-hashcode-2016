<?php
/**
 * Created by PhpStorm.
 * User: TFLOR
 * Date: 11/02/2016
 * Time: 20:23
 */

require 'file-reader.php';
require 'file-writer.php';

$readed_file = new File_Reader( "busy_day.in" );
var_dump( $readed_file->first_line );
echo $readed_file->first_line['rows'];

$writed_file = new File_Writer( "busy_day.out" );