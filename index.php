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
		if ( $warehouse['p'][$product_id] > 0 ) {
			$found_warehouses[$i]['id'] = $warehouse_id;
			$dist = get_drone_distance( $warehouse['coords'], $drone_position );
			$found_warehouses[$i]['dist'] = $dist;
			$i++;
		}
	}
	// Order by closest warehouses
	$found_warehouses = bea_array_sort( $found_warehouses, 'dist', SORT_ASC );
	return $found_warehouses;
}

// Deliver the orders from the closest warehouse
function sort_orders( $in_file, $warehouse_pos ) {
	/** @var File_Reader $in_file */

	$sorted_orders = $in_file->orders;
	foreach ( $in_file->orders as $order_id => $order ) {
		$sorted_orders[$order_id]['id'] = $order_id;
		$dist = get_drone_distance( $order['coords'], $warehouse_pos );
		$sorted_orders[$order_id]['dist'] = $dist;
	}
	// Order by closest orders
	$sorted_orders = bea_array_sort( $sorted_orders, 'dist', SORT_ASC );
	var_dump($sorted_orders);
	return $sorted_orders;
}
