<?php

require 'file-reader.php';
require 'file-writer.php';

$readed_file = new File_Reader( "busy_day.in" );
//var_dump( $readed_file->first_line );
//echo $readed_file->first_line['drones_nb'];

function get_drone_distance( $coords_a, $coords_b ) {
	$distance = sqrt ( pow( ($coords_a[0] - $coords_a[1]), 2 ) - pow( ($coords_b[0] - $coords_b[1]), 2 ) );
	$distance = ceil( $distance );
	return $distance;
}

function find_warehouse( $in_file, $product_id, $drone_position ) {
	/** @var File_Reader $in_file */
	$found_warehouses = array();
	$i = 0;
	foreach ( $in_file->warehouses as $warehouse_id => $warehouse ) {
		if ( $warehouse['p'][$product_id] > 0 ) {
			$found_warehouses[$i]['id'] = $warehouse_id;
			$dist = get_drone_distance( $warehouse['coords'], $drone_position );
			$found_warehouses[$i]['dist'] = $dist;
			$i++;
		}
	}

	var_dump( $found_warehouses );
//	die();
//	sort( $found_warehouses, 0 );

	return false;
}

find_warehouse( $readed_file, 6, array( 0, 0 ) );

//$writed_file = new File_Writer( "busy_day.out" );

