<?php

require 'file-reader.php';
require 'file-writer.php';

$readed_file = new File_Reader( "busy_day.in" );
//var_dump( $readed_file->first_line );
echo $readed_file->first_line['drones_nb'];

function get_drone_distance( $coords_a, $coords_b ) {
	$distance = sqrt ( pow( ($coords_a[0] - $coords_a[1]), 2 ) - pow( ($coords_b[0] - $coords_b[1]), 2 ) );
	$distance = ceil( $distance );
	return $distance;
}

class Drone {

	private $in_file;

	function __construct( $file_in ) {
		$this->in_file = $file_in;
	}
}

$writed_file = new File_Writer( "busy_day.out" );

