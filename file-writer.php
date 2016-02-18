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
class File_Writer {

	private $path;
	private $buffer;

	function __construct( $file_path ) {
		$this->path = $file_path;
		$this->buffer = '';
	}

	public function write( ) {
		$myfile = fopen($this->path, "w" ) or die("Unable to open file!");
		fwrite( $myfile, $this->buffer );
		fclose($myfile);
	}

	public function load( $drone_id, $warehouse_id, $product_type_id, $num_of_items ) {
		$this->buffer .= sprintf( "%d L %d %d %d \n", $drone_id, $warehouse_id, $product_type_id, $num_of_items );
	}

	public function unload( $drone_id, $warehouse_id, $product_type_id, $num_of_items ) {
		$this->buffer .= sprintf( "%d U %d %d %d \n", $drone_id, $warehouse_id, $product_type_id, $num_of_items );
	}

	public function deliver( $drone_id, $order_id, $product_type_id, $num_of_items ) {
		$this->buffer .= sprintf( "%d D %d %d %d \n", $drone_id, $order_id, $product_type_id, $num_of_items );
	}

	public function wait( $drone_id, $num_of_turns ) {
		$this->buffer .= sprintf( "%d W %d %d %d \n", $drone_id, $num_of_turns );
	}

}