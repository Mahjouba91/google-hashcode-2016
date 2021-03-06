<?php

/**
 * Simple function to sort a multi dimensional array according to its values
 *
 * @param array $array
 * @param string $key
 * @param CONST int $order
 * @param CONST int $sort_flag
 *
 * @return array
 */
function bea_array_sort( $array = array(), $key = '', $order = SORT_ASC, $sort_flag = SORT_REGULAR )
{
	$new_array = array();
	$sortable_array = array();

	if ( count($array) > 0 ) {
		foreach ( $array as $k => $v ) {
			if ( is_array($v) ) {
				foreach ( $v as $k2 => $v2 ) {
					if ( $k2 == $key ) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ( $order ) {
			case SORT_ASC:
				asort($sortable_array, $sort_flag);
				break;
			case SORT_DESC:
				arsort($sortable_array, $sort_flag);
				break;
		}

		foreach ( $sortable_array as $k => $v ) {
			$new_array[$k] = $array[$k];
		}
	}

	return $new_array;
}

function get_drone_distance( $coords_a, $coords_b ) {
	$distance = sqrt ( pow( ( (int) $coords_a[0] - (int) $coords_b[0]), 2 ) + pow( ( (int) $coords_a[1] - (int) $coords_b[1]), 2 ) );
	$distance = ceil( $distance );
	return $distance;
}

function find_warehouse( $in_file, $product_id, $drone_position ) {
	/** @var File_Reader $in_file */
	$found_warehouses = array();
	$i = 0;
	foreach ( $in_file->warehouses as $warehouse_id => $warehouse ) {
		if ( (int) $warehouse['p'][$product_id] > 0 ) {
			$found_warehouses[$i]['id'] = $warehouse_id;
			$dist = get_drone_distance( $warehouse['coords'], $drone_position );
			$found_warehouses[$i]['dist'] = $dist;
			$i++;
		}
	}
	// Order by closest warehouses
	$found_warehouses = bea_array_sort( $found_warehouses, 'dist', SORT_ASC );
	return $found_warehouses[0];
}

// Deliver the orders from the closest warehouse
function sort_orders( $in_file, $warehouse_pos ) {
	/** @var File_Reader $in_file */

	$sorted_orders = $in_file->orders;
	foreach ( $in_file->orders as $order_id => $order ) {
		$sorted_orders[$order_id]['id'] = $order_id;
		$sorted_orders[$order_id]['num_of_items'] = (int) $order['num_of_items'];
		$dist = get_drone_distance( $order['coords'], $warehouse_pos );
		$sorted_orders[$order_id]['dist'] = $dist;
	}
	// Order by closest orders
	$sorted_orders = bea_array_sort( $sorted_orders, 'num_of_items', SORT_ASC );
	$sorted_orders = bea_array_sort( $sorted_orders, 'dist', SORT_ASC );
	return $sorted_orders;
}

function sort_order_products_by_weights( $in_file, $products_order ) {
	$products = array();

	/** @var File_Reader $in_file */
	$i=0;
	foreach ( $products_order as $product_id => $product_order ) {
		$products[$i]['id'] = $product_id;
		$products[$i]['weight'] = $in_file->weights[$product_id];
		$i++;
	}
	$products = bea_array_sort( $products, 'weight', SORT_ASC );
	return $products;
}

function deliver_orders( $in_path, $out_path ) {
	$readed_file = new File_Reader( $in_path );
	$writed_file = new File_Writer( $out_path );
	$drones = new Drone( $readed_file );
	$dead_line = $readed_file->first_line['deadline'];
	$d = 0; // current drone_id
	$w = 0; // current weight
	$t = 0; // current time
	$score = 0;

	$sorted_orders = sort_orders( $readed_file, $readed_file->warehouses[0]['coords'] );
	foreach ( $sorted_orders as $order ) {

		$products_order = sort_order_products_by_weights( $readed_file, $order['p'] );
		$drones_order = array(); // reset;

		foreach ( $products_order as $product_type ) {

			$t_weight = $w + $readed_file->weights[$product_type['id']]; // temporary weight

			// If this is the last product order, then delevery the order.
			// Or if if the maximum load size of the drone is crossed
			if( end($products_order) === $product_type || $t_weight > $readed_file->first_line['max_load'] ){
				$drones->deliver( $d, $order['id'] );
				$writed_file->deliver( $d, $order['id'], $product_type['id'], 1 );

				$drones_order[] = $d;

				$w = 0; // reset the weight
				$d++; // Change the drone;
				if ( $d >= $readed_file->first_line['drones_nb'] ) {
					$d = 0; // Restart from the first drone;
				}
			}
			// Elseif : load the drone
			elseif ( $t_weight <= $readed_file->first_line['max_load'] ) {
				$closest_warehouses = find_warehouse( $readed_file, $product_type['id'], $drones->drone_state[$d]['coords'] );

				$drones->load( $d, $closest_warehouses['id'] ); // Load the product on the drone
				$w += $readed_file->weights[$product_type['id']]; // Add product weight
				$t += $closest_warehouses['dist'];

				$writed_file->load( $d, $closest_warehouses['id'], $product_type['id'], 1 );
			}

		}

		// Know when the order is totaly delivered to the customer
		$biggest_drone_time = 0;
		foreach ( $drones_order as $drone_order ) {
			if ( $biggest_drone_time < $drones->drone_state[$drone_order]['time'] ) {
				$biggest_drone_time = $drones->drone_state[$drone_order]['time'];
			}
		}
		$order_score = ( ( $dead_line - $biggest_drone_time ) / $dead_line ) * 100;
		$score += ceil( $order_score );

	}

	$writed_file->write();
	return $score; // Score of the simulation

}

require 'file-reader.php';
require 'file-writer.php';
require 'drones.php';

$total_score = 0;
$files = array( 'busy_day', 'mother_of_all_warehouses', 'redundancy' );
foreach ( $files as $file ) {
	$score = deliver_orders( 'input/' . $file . '.in', 'output/' . $file . '.out' );
	echo 'Simulation terminée, score : ' . $score . "<br>";
	$total_score += $score;
}

?>

<h2>Score total : <?php echo $total_score; ?></h2>