<?php
/**
 * Created by PhpStorm.
 * User: TFLOR
 * Date: 11/02/2016
 * Time: 20:23
 */

class FileParser {

	var $elements = array();
	var $rows = array();
	var $columns = array();
	var $drones_nb = array();
	var $deadline = array();
	var $max_load = array();

	function __construct( $file_path ) {
		$handle = @fopen( $file_path, "r" );
		if ( $handle ) {
			$i = 0;
			$buffer = array();
			while ( ($buffer[] = fgets($handle, 4096) ) !== false ) {
				echo $buffer[$i] . '<br>';

				$first_line = $buffer[0];
	            $this->rows = $first_line[0];
	            $this->columns = $first_line[1];
	            $this->drones_nb = $first_line[2];
	            $this->deadline = $first_line[3];
	            $this->max_load = $first_line[4];

				$i++;
			}
			if ( ! feof($handle) ) {
				echo "Erreur: fgets() a échoué\n";
			}
			fclose($handle);
		} else {
			echo 'fichier non trouvé';
		}
	}

}

$parsed_file = new FileParser( "busy_day.in" );