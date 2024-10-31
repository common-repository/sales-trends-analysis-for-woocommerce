<?php 
require_once('ic-function.php');  
class ic_sta_lite_init extends ic_function{
	public function __construct(){
		
		if (isset($_REQUEST["page"])) {
			$page = $_REQUEST["page"];
			if ($page == "sales-analysis" 
				||  $page == "customer-non-purchase"
			   ||  $page == "product-analysis"
			   ||  $page == "variable-product-analysis"
			   ||  $page == "ic-invoice-setting"
			   ||  $page == "ic-analysis-addon"){
				add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			}				
		}
		
		add_action( 'admin_menu',  array(&$this,'admin_menu' ));
		add_action( 'wp_ajax_ic_sales_analysis_ajax',  array(&$this,'ic_sales_analysis_ajax' ));
		
		$this->add_setting_page();
		$this->add_addon_page();
	}
	
	function admin_menu(){		
		add_menu_page(esc_html__('Sales Analysis','icsalestrendsanalysis'), esc_html__('Sales Trend Analysis','icsalestrendsanalysis'), 'manage_options', 'sales-analysis',  array(&$this,'add_page'),  plugins_url( '../images/icon.png', __FILE__ ),70.2613);
		add_submenu_page( 'sales-analysis', esc_html__('Simple Prod. Order Qty. Analysis','icsalestrendsanalysis'), esc_html__('Simple Prod. Order Qty. Analysis','icsalestrendsanalysis'),'manage_options','product-analysis',  array(&$this,'add_page'));
		add_submenu_page( 'sales-analysis', esc_html__('Variable Prod. Order Qty. Analysis','icsalestrendsanalysis'), esc_html__('Variable Prod. Order Qty. Analysis','icsalestrendsanalysis'),'manage_options','variable-product-analysis',  array(&$this,'add_page'));
		add_submenu_page( 'sales-analysis', esc_html__('Customer/Non Purchase','icsalestrendsanalysis'), esc_html__('Customer/Non Purchase','icsalestrendsanalysis'),'manage_options','customer-non-purchase',  array(&$this,'add_page'));		
	}
	
	function admin_enqueue_scripts(){
	
		wp_enqueue_script( 'ajax-script', plugins_url( '../js/script.js',__FILE__ ), array('jquery') );		
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ic_ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
		
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('normalize-style', plugins_url( '../css/ic-normalize.css', __FILE__ ) );
		wp_enqueue_style('admin-style', plugins_url( '../css/ic-admin.css', __FILE__ ) );
		wp_enqueue_style('fontawesome-style', plugins_url( '../css/font-awesome.min.css', __FILE__ ) );
		
		if (isset($_REQUEST["page"])) {
			$page = isset($_REQUEST["page"]);
			if ($page=="product-analysis")
				wp_enqueue_script('ic-simple-product', plugins_url( '../js/ic-single-product.js', __FILE__ ) , array( 'jquery' ) );
			if ($page=="product-analysis")
				wp_enqueue_script('ic-variable-product', plugins_url( '../js/ic-variable-product.js', __FILE__ ) , array( 'jquery' ) );
			 if($page=="group-simple-product-analysis")
				wp_enqueue_script('ic-group-simple-product-analysis', plugins_url( '../js/ic-group-simple-product-analysis.js', __FILE__ ) , array( 'jquery' ) );
			if ($page=="customer-in-price-point")	
				wp_enqueue_script('ic-customer-in-price-point', plugins_url( '../js/ic-customer-in-price-point.js', __FILE__ ) , array( 'jquery' ) );	
			if ($page=="customer-non-purchase")	
				wp_enqueue_script('ic-customer-non-purchase', plugins_url( '../js/ic-customer-non-purchase.js', __FILE__ ) , array( 'jquery' ) );			
		}
		
	}
	
	function ic_sales_analysis_ajax(){
		//echo json_encode($_REQUEST);
		//die;	
		 $sub_action	= $this->get_request('sub_action');
		 switch ($sub_action) {
				case "product-analysis":
					include_once("ic-product-analysis.php");
					$obj =  new ic_product_analysis();
					$obj->ajax();
					break;
				case "group-product-analysis":
					include_once("ic-group-product-analysis.php");
					$obj = new ic_group_product_analysis();
					$obj->ajax();
					break;
				case "variable-analysis":
					include_once("ic-variable-product-analysis.php");
					$obj = new ic_variable_product_analysis();
					$obj->ajax();
					break;
				case "ic-customer-non-purchase":
					include_once("ic-customer-non-purchase.php");
					$obj = new ic_customer_non_purchase();
					$obj->ajax();
					break;						
				case "green":
					echo "Your favorite color is green!";
					break;
				default:
					echo "Your favorite color is neither red, blue, nor green! AJAX 123";
			}
			
			
			//return $this->print_array($_REQUEST);
			
			//die;
	}
	
	function add_page(){
		if (isset($_REQUEST["page"])) {
		 	$page = $_REQUEST["page"];
			switch ($page) {
				case "sales-analysis":
					include_once("ic-sales-analysis-dashboard.php");
					$obj =  new ic_sales_analysis_dashboard();
					$obj->init();
					break;
				case "product-analysis":
					include_once("ic-product-analysis.php");
					$obj =  new ic_product_analysis();
					$obj->init();
					break;
				case "variable-product-analysis":
					include_once("ic-variable-product-analysis.php");
					$obj = new ic_variable_product_analysis();
					$obj->init();
					break;
				case "customer-non-purchase":
					include_once("ic-customer-non-purchase.php");
					$obj = new ic_customer_non_purchase();
					$obj->init();
					break;						
				case "blue":
					echo "Your favorite color is blue!";
					break;
				case "green":
					echo "Your favorite color is green!";
					break;
				default:
					echo "Your favorite color is neither red, blue, nor green! " .__FUNCTION__;
			}
		}
		
	}
	
	function add_setting_page(){
		include_once("ic-analysis-setting.php");	
		$obj = new ic_analysis_setting();
	}
	
	function add_addon_page(){
		include_once("ic-analysis-addons.php");	
		$obj = new ic_analysis_addon();
	}
	
	/*Cron Start From Here*/
	function wp_cron_init() {	
	//die;	
		$enable 	= "yes";
		$schedule 	= "every_fifteen_minute";
		
		if($enable == "yes"){	
			if (!wp_next_scheduled('prefix_do_this_in_every_fifteen_minute')) {
					wp_schedule_event(time(), $schedule, 'prefix_do_this_in_every_fifteen_minute');
				}
				
			}else{
				wp_unschedule_event( time(), 'prefix_do_this_in_every_fifteen_minute', array() );
				wp_clear_scheduled_hook( 'prefix_do_this_in_every_fifteen_minute', array() );
			}
	}
	
	function add_custom_cron_schedule( $schedules){
		$schedules['every_fifteen_minute'] = array(
			'interval' => (300),
			'display' => __( 'Every 15 Minute', 'cr' )
	    );
	  return $schedules;
	}
	
	function prefix_do_this_hourly_name() {}
}
?>