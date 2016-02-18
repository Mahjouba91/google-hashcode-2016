<?php
/**
 * Created by PhpStorm.
 * User: TFLOR
 * Date: 11/02/2016
 * Time: 20:23
 */

/**
 * Class FileParser
 *
 */
class File_Reader {

	var $first_line = array();
	var $p = 0; // number of product types
	var $weights = array(); // Weight of all products type ordered by product type ID

	var $w = 0; // number of warehouses
	var $warehouses = array();

	var $c = 0; // number of orders
	var $orders = array();

	function __construct( $file_path ) {
		$handle = @fopen( $file_path, "r" );
		if ( ! $handle ) {
			return false;
		}

		$i = 0;
		while ( ($buffer[] = fgets($handle, 4096) ) !== false ) {
			$buffer[$i] = explode(" ", $buffer[$i]);
			$i++;
		}

		$this->first_line['rows'] = (int) $buffer[0][0];
		$this->first_line['columns'] = (int) $buffer[0][1];
		$this->first_line['drones_nb'] = (int) $buffer[0][2];
		$this->first_line['deadline'] = (int) $buffer[0][3];
		$this->first_line['max_load'] = (int) $buffer[0][4];

		$this->p = (int) $buffer[1][0];
		$this->weights = (int) $buffer[2];
		$this->w = (int) $buffer[3][0];

		$i = 0;
		$last_index = 4;
		for ( $j = 0; $j < $this->w; $j++ ) {
			$this->warehouses[$j]['coords'] = $buffer[$i+$last_index];
			$this->warehouses[$j]['p'] = $buffer[$i+$last_index+1];
			$i += 2;
		}

		$last_index = ( $this->w * 2 ) + 4;
		$this->c = (int) $buffer[$last_index][0];
		$last_index ++;

		$i = 0;
		for ( $j = 0; $j < $this->c; $j++ ) {
			$this->orders[$j]['coords'] = $buffer[$i+$last_index];
			$this->orders[$j]['num_of_items'] = $buffer[$i+$last_index+1][0];
			$this->orders[$j]['p'] = $buffer[$i+$last_index+2];
			$i += 3;
		}

		if ( ! feof($handle) ) {
			echo "Erreur: fgets() a échoué\n";
		}
		fclose($handle);
	}

}