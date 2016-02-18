<?php
/**
 * Created by PhpStorm.
 * User: TFLOR
 * Date: 11/02/2016
 * Time: 20:23
 */

/**
 * Class Drones
 *
 */

class Drone {

	public $in_file;
	public $drone_state = array();

	function __construct( $file_in ) {
		/** @var File_Reader $file_in */
		$this->in_file = $file_in; // Read entry file
		for ( $i=0; $i < $file_in->first_line['drones_nb']; $i++ ) {
			$this->drone_state[] = array(
				'coords' => $file_in->warehouses[0]['coords'], // Drones begin to the first warehouse
				'time'   => 0,
			);
		}

	}

	public function load( $drone_id, $warehouse_id ) {
		$pos = $this->drone_state[$drone_id]['coords'];
		$dest = $this->in_file->warehouses[$warehouse_id]['coords'];

		$time = get_drone_distance( $pos, $dest ) + 1;

		$this->drone_state[$drone_id]['coords'] = $dest; // Put the destination coords
		$this->drone_state[$drone_id]['time'] += $time; // Add the load time

		if ( $this->drone_state[$drone_id]['time'] > $this->in_file->first_line['deadline'] ) {
			return false;
		}

		return true;

	}

	public function deliver( $drone_id, $order_id ) {
		$pos = $this->drone_state[$drone_id]['coords'];
		$dest = $this->in_file->orders[$order_id]['coords'];

		$time = get_drone_distance( $pos, $dest ) + 1;

		$this->drone_state[$drone_id]['coords'] = $dest; // Put the destination coords
		$this->drone_state[$drone_id]['time'] += $time; // Add the load time

		if ( $this->drone_state[$drone_id]['time'] > $this->in_file->first_line['deadline'] ) {
			return false;
		}

		return true;

	}

}
