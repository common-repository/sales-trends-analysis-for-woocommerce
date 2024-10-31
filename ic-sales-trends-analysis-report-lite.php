<?php 
/*
Plugin Name: Sales Trends Analysis for WooCommerce
Description: Sales Analysis Report Lite is report plugin to help you more insights on how your sales taking place.
Version: 2.0
Author: Infosoft Consultants
Author URI: http://plugins.infosofttech.com
Plugin URI: https://wordpress.org/plugins/sales-trends-analysis-for-woocommerce/

Tested Wordpress Version: 6.1.x
WC requires at least: 3.5.x
WC tested up to: 7.4.x
Requires at least: 5.7
Requires PHP: 5.6

Text Domain: icsalestrendsanalysis
Domain Path: /languages/

Last Update Date: March 15, 2019
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'IC_Sales_Trend_Analysis_Report_Lite' ) ) { 
	class IC_Sales_Trend_Analysis_Report_Lite{
		function __construct() {
			add_filter( 'plugin_action_links_sales-trends-analysis-for-woocommerce/ic-sales-trends-analysis-report-lite.php', array( $this, 'plugin_action_links' ), 9, 2 );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ));
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ));
		}
		
		function plugins_loaded(){
			include_once('include/init.php'); 
			$obj = new ic_sta_lite_init();  
		}
		
		function plugin_action_links($plugin_links, $file = ''){
			$plugin_links[] = '<a target="_blank" href="'.admin_url('admin.php?page=sales-analysis').'">' . esc_html__( 'Dashboard', 'icsalestrendsanalysis' ) . '</a>';
			return $plugin_links;
		}
		
		function load_plugin_textdomain(){
			load_plugin_textdomain( 'icsalestrendsanalysis', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		}
		
		function myplugin_deactivate(){
				wp_clear_scheduled_hook( 'prefix_do_this_in_every_fifteen_minute', array() );
				wp_clear_scheduled_hook( 'my_hourly_event_anzar', array() );
		}
		
		function myplugin_activation(){
			wp_schedule_event(time(), 'every_fifteen_minute', 'prefix_do_this_in_every_fifteen_minute');
		}
	}
	$obj = new  IC_Sales_Trend_Analysis_Report_Lite();
}
register_deactivation_hook( __FILE__, array('IC_Sales_Trend_Analysis_Report_Lite','myplugin_deactivate') );
register_activation_hook( __FILE__, array('IC_Sales_Trend_Analysis_Report_Lite','myplugin_activation') );