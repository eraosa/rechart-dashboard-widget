<?php
/**
 * Plugin Name: Rechart Dashboard Widget
 * Description: A WordPress plugin to add a Dashboard Widget using ReactJS and WP REST API.
 * Version: 1.0
 * Author: eraosa
 * @package RechartDashboardWidget
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function rechart_dashboard_widget_scripts() {
	wp_enqueue_script( 'rechart-dashboard-widget', plugins_url( 'build/index.js', __FILE__ ), array( 'wp-element' ), '1.0', true );
	wp_localize_script( 'rechart-dashboard-widget', 'rechartAPI', array(
		'root'  => esc_url_raw( rest_url() ),
		'nonce' => wp_create_nonce( 'wp_rest' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'rechart_dashboard_widget_scripts' );

/**
 * Register dashboard widget.
 *
 * @return void
 */
function rechart_dashboard_widget_register() {
	wp_add_dashboard_widget( 'rechart_dashboard_widget', 'Rechart Dashboard Widget', 'rechart_dashboard_widget_display' );
}
add_action( 'wp_dashboard_setup', 'rechart_dashboard_widget_register' );

/**
 * Display callback function.
 *
 * @return void
 */
function rechart_dashboard_widget_display() {
	echo '<div id="rechart-dashboard-widget-root"></div>';
}

/**
 * Create REST API endpoint to fetch chart data.
 */
add_action( 'rest_api_init', function() {
	register_rest_route( 'rechart/v1', '/data/(?P<period>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'rechart_get_chart_data',
		'permission_callback' => '__return_true',
		'args'                => array(
			'period' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					// Validate that the parameter is numeric.
					return is_numeric( $param );
				}
			),
		),
	));
});

/**
 * Fetch chart data callback function.
 *
 * @param WP_REST_Request $data The request data.
 * @return WP_REST_Response The response containing the chart data.
 */
function rechart_get_chart_data( $data ) {
	$period = $data['period'];
	// Fetch static data from the database and filter based on the period.
	$all_data = get_option( 'rechart_data' ); 

	// Assuming 'rechart_data' is an array of data points with 'date' and 'value' keys.

	// Filter the data based on the period.
	$filtered_data = array_slice( $all_data, -intval( $period ) ); // Use intval to ensure it's an integer.

	return new WP_REST_Response( $filtered_data, 200 );
}

// Example data for 'rechart_data' option.
$rechart_data = array(
	array( 'date' => '2024-04-25', 'value' => 10 ),
	array( 'date' => '2024-04-26', 'value' => 12 ),
	array( 'date' => '2024-04-27', 'value' => 11 ),
	array( 'date' => '2024-04-28', 'value' => 13 ),
	array( 'date' => '2024-04-29', 'value' => 14 ),
	array( 'date' => '2024-04-30', 'value' => 15 ),
	array( 'date' => '2024-05-01', 'value' => 16 ),
	array( 'date' => '2024-05-02', 'value' => 17 ),
);

// Save the data to the 'rechart_data' option.
update_option( 'rechart_data', $rechart_data );
