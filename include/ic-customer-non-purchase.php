<?php 
require_once('ic-function.php');  
class ic_customer_non_purchase extends ic_function{
	public function __construct(){
		
	}
	
	function init(){		
		$start_date = date_i18n("Y-m-d");
		$end_date = date_i18n("Y-m-d");	
		$billing_name 	= isset($_REQUEST['billing_name']) ? $_REQUEST['billing_name'] : '';
		$billing_email 	= isset($_REQUEST['billing_email']) ? $_REQUEST['billing_email'] : '';
		$order_status  = "wc-" . implode( ",wc-", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) );
?>
		
		<div class="ic_analysis_wrap">
			<div style="font-size:25px; margin-bottom:20px;">Customer Who Has Not Purchased</div>
			<div class="ic_container-liquid">
				<div class="ic_navigation">
					<div class="ic_heading">Custom Search</div>
					<div class="ic_content">
						<div class="ic_search_report_form">
							<form name="frm_customer_in_price_point" id="frm_customer_in_price_point" method="post">
								<div class="ic_form-group">
									<div class="ic_FormRow ic_firstrow">
										<div class="ic_label-text"><label for="start_date"><?php _e("Avg. Calc From Date:",'icwoocommerce_textdomains'); ?></label></div>
										<div class="ic_input-text"><input type="text" value="<?php echo $start_date;?>" class="_date" id="start_date" name="start_date" maxlength="10" /></div>
									</div>
									<div class="ic_FormRow ic_secondrow">
										<div class="ic_label-text"><label for="end_date"><?php _e("Avg. Calc To Date:",'icwoocommerce_textdomains'); ?></label></div>
										<div class="ic_input-text"><input type="text" value="<?php echo $end_date;?>" class="_date" id="end_date" name="end_date" maxlength="10" /></div>
									</div>
								</div>
									
								 <div class="ic_form-group">
									<div class="ic_FormRow ic_firstrow">
										<div class="ic_label-text"><label for="billing_name"><?php _e("Billing Name:",'icwoocommerce_textdomains'); ?></label></div>
										<div class="ic_input-text"><input type="text" value="<?php echo $billing_name;?>" id="billing_name" name="billing_name" maxlength="100" /></div>
									</div>
									<div class="ic_FormRow ic_secondrow">
										<div class="ic_label-text"><label for="billing_email"><?php _e("Billing Email:",'icwoocommerce_textdomains'); ?></label></div>
										<div class="ic_input-text"><input type="text" value="<?php echo $billing_email;?>" id="billing_email" name="billing_email" maxlength="200" /></div>
									</div>
								</div>
								
								<input type="hidden" name="action" id="action" value="ic_sales_analysis_ajax" />
								<input type="hidden" name="sub_action" id="sub_action" value="ic-customer-non-purchase" />
								<input type="hidden" name="call" id="call" value="ic-customer-non-purchase" />
                                <input type="hidden" name="order_status" id="order_status" value="<?php echo $order_status;?>" />
								
								<div class="ic_form-group">
									<div class="ic_FormRow ic_Fullwidth">
										<span class="ic_submit_buttons">
											<input type="submit" class="ic_formbtn" value="Search" />
										</span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="ajax_content"></div>
			</div>
		</div>
<?php

	$output = '<style type="text/css">';
	$output .= $this->get_number_columns_css_style();
	$output .= '</style>';
	
	echo $output;
				
	}
	
	function admin_footer(){}
	
	function customer_non_purchase_query(){
		echo $this->get_grid();
	}
	
		
	function get_columns(){
		$columns 	= array(
			"billing_first_name"	=> __("Billing First Name", 	'icwoocommerce_textdomains')
			,"billing_last_name"	=> __("Billing Last Name", 		'icwoocommerce_textdomains')
			,"billing_email"		=> __("Billing Email", 			'icwoocommerce_textdomains')
			,"order_count"			=> __("Order Count", 			'icwoocommerce_textdomains')
			,"total_amount"			=> __("Amount", 				'icwoocommerce_textdomains')
			,"order_date"			=> __("Last Order Date", 		'icwoocommerce_textdomains')
		);
		return $columns;
	}
	
	function result_columns($total_columns = array(), $report_name = ''){
		$columns = array(
			"total_row_count"			=> __("Customer Count", 	'icwoocommerce_textdomains')
			,"order_count"				=> __("Order Count", 		'icwoocommerce_textdomains')
			,"total_amount"				=> __("Total Amount", 		'icwoocommerce_textdomains')
		);
		return $columns;
		}
	
	function get_grid($type = 'limit_row'){
			
			$items 			= $this->get_items($type);
			$summary 		= $this->get_items('total_row');
			$columns 		= $this->get_columns($type, $type);
			$row_count		= 0;
			
			$admin_url		= admin_url('admin.php').'?page=ic-purchase&action=edit&ID=%s';
			$edit_label 	= "<a href=\"$admin_url\">".__('Edit')."</a>";
			
			$deletelabel 	= __('Delete');
			$delete_label 	= "<a class=\"delete_item\" data-delete_id=\"%s\" href=\"$admin_url&action=delete\">".$deletelabel."</a>";
			
			$total_pages	= isset($summary['total_row_count']) ? $summary['total_row_count'] : 0;
			$total_count	= 0;
			$total_amount	= 0;			
			$output 		= "";
			
			$price_columns = $this->get_price_columns();
			
			//$this->print_array($price_columns);
			
			//$this->print_header($type, $columns );	
			/*
			if($type != 'all_row'){
				$output .= '<div class="top_buttons">';
				$output .=$this->export_to_csv_button('top', $total_pages, $summary);
				$output .= '<div class="clearfix"></div></div>';
			}else{
				unset($columns['edit']);
				$output .=$this->back_print_botton('top');
			}
			*/
			$output .= '<div class="ic_table-responsive">';
			$output .= '<table style="width:100%" class="widefat">';
            $output .= '    <thead>';
            $output .= '        <tr class="first">';					
								foreach($columns as $column_key => $value):
									$td_class		= $column_key;
									$td_value 		= $value;
									switch($column_key):
										case "total_sales_amount":
										case "new_customer_sales_amount":
										case "repeat_customer_sales_amount":
										case "total_order_count":
											$td_class .= ' right_align';									
											break;	
										case "edit":
											$td_class .= ' right_align';
											break;							
									endswitch;/*End Columns Switch*/
									$output .= "\n<th class=\"{$td_class}\">{$td_value}</th>\n";
								endforeach;/*End Columns Foreach*/					
            $output .= '      </tr>';
            $output .= ' </thead>';
            $output .= ' <tbody>';

               	foreach ( $items as $key => $item ) {
					if($row_count%2 == 0){$alternate = "alternate ";}else{$alternate = "";};
						$output .= '<tr class="'.$alternate."row_".$key.'">';
						foreach($columns as $column_key => $value):
							$td_class		= $column_key;
							$td_value 		= isset($item->$column_key) ? $item->$column_key : '';
							switch($column_key):
								case "total_sales_amount":
								case "new_customer_sales_amount":
								case "repeat_customer_sales_amount":
									$td_class .= ' right_align';
									$td_value = wc_price($td_value );
									break;
								case "total_order_count":
									$td_class .= ' right_align';									
									break;
								case "edit":
									$td_class .= ' right_align';
									$id 		= isset($item->purchase_header_id) ? $item->purchase_header_id : '';									
									$td_value 	= sprintf($edit_label, $id);
									break;
								case "delete":
									$id 		= isset($item->purchase_header_id) ? $item->purchase_header_id : '';									
									$td_value 	= "<a class=\"delete_item\" data-delete_id=\"{$id}\" href=\"$admin_url&action=delete\">".$deletelabel."</a>";;
									break;
								default:
									foreach($price_columns as $price_column_key => $price_column){
										if($price_column == $column_key){
											$td_class .= ' right_align';
											$td_value = wc_price($td_value );
										}
									}
									break;
							endswitch;/*End Columns Switch*/
							$output .= "\n<td class=\"{$td_class}\">{$td_value}</td>\n";
						endforeach;/*End Columns Foreach*/
				}/*End Items Foreach*/
            $output .= ' </tbody>';
            $output .= '</table>';
			$output .= '</div>';
			
			$output .= '<style type="text/css">';
			$output .= $this->get_number_columns_css_style();
			$output .= '</style>';
			
			if($type != 'all_row'){
				$output .= $this->total_count($total_count, $total_amount, $total_pages,$summary);
			}else{
				//$output .= $this->back_print_botton('bottom');
			}
			return $output;
            
		}
	function total_count($TotalOrderCount = 0, $TotalAmount = 0, $total_pages = 0, $summary = array()){
			global $request;
			
			$admin_page 		= $this->get_request('page');
			$limit	 			= $this->get_request('limit',15, true);
			$adjacents			= $this->get_request('adjacents',3);
			$detail_view		= $this->get_request('detail_view',"no");
			$targetpage 		= "admin.php?page=".$admin_page;
			
			$create_pagination 	= $this->get_pagination($total_pages,$limit,$adjacents,$targetpage,$request);
			$output 			= "";
			$output .= '<table style="width:100%" class="detail_summary">';
			$output .= '	<tr>';
			$output .= '		<td>';	
			$output .= '       	  	<div class="clearfix"></div>';
			$output .= '          	<div>';			
			$output .= 					$create_pagination;
			$output .= '     	  	</div>';
			$output .= '       	  	<div class="clearfix"></div>';
			$output .= '          	<div>';			
			$output .= 			  		$this->export_to_csv_button('bottom',$total_pages, $summary);			
			$output .= '     	  	</div>';
			$output .= '    	  	<div class="clearfix"></div>';
			$output .= ' 		</td>';
			$output .= '</tr>';
			$output .= '</table>';
			$output .= '<script type="text/javascript">';
			$output .= " jQuery(document).ready(function() { ";
			$output .= " jQuery('.pagination a').removeAttr('href');";
			$output .= " }); ";
			$output .= '</script>';
			
			return $output;
		}
		
		function export_to_csv_button($position = 'bottom', $total_pages = 0, $summary = array()){
			global $request;
			
			$admin_page 					= $this->get_request('admin_page');
			$admin_page_url 				= admin_url('admin.php');
			$mngpg 							= $admin_page_url.'?page='.$admin_page ;
			$request						= $this->get_all_request();			
			$request['total_pages'] 		= $total_pages;				
			$request['count_generated']		=	1;
			
			foreach($summary as $key => $value):
				$request[$key]		=	$value;
			endforeach;
			
			//$this->print_array($request);
			
			$request['delete_id'] =	0;
					
			$request_			=	$request;
			$action				= $request['action'];
			unset($request['action']);			
			unset($request['p']);
			
			/*$output = '<div id="'.$admin_page.'Export" class="RegisterDetailExport">';*/
				$output = '<div id="'.$admin_page.'Export" class="ic_RegisterDetailExport">';
                /*
				$output .= '<form id='. $admin_page.'_'.$position.'_form" class='. $admin_page.'_form ic_export_'. $position.'_form" action="'. $mngpg.'" method="post">';
                    
					$output .= $this->create_hidden_fields($request);
                    $output .= '<input type="hidden" name="export_file_name" value="'. $admin_page.'" />';
                    $output .= '<input type="hidden" name="export_file_format" value="csv" />';
					$output .= '<input type="hidden" name="sub_action" value="'.$this->constants['plugin_key'].'_export" />';
                    $output .= '<input type="submit" name="'. $admin_page.'_export_csv" class="onformprocess open_popup ic_csvicon" value="'. __("Export to CSV",'icwoocommerce_textdomains').'" data-format="csv" data-popupid="export_csv_popup" data-hiddenbox="popup_csv_hidden_fields" data-popupbutton="'. __("Export to CSV",'icwoocommerce_textdomains').'" data-title="'. __("Export to CSV - Additional Information",'icwoocommerce_textdomains').'" />';
                    $output .= '<input type="submit" name="'. $admin_page.'_export_xls" class="onformprocess open_popup ic_excelicon" value="'. __("Export to Excel",'icwoocommerce_textdomains').'" data-format="xls" data-popupid="export_csv_popup" data-hiddenbox="popup_csv_hidden_fields" data-popupbutton="'. __("Export to Excel",'icwoocommerce_textdomains').'" data-title="'. __("Export to Excel - Additional Information",'icwoocommerce_textdomains').'" />';
                    $output .= '<input type="submit" name="'. $admin_page.'_export_pdf" class="onformprocess open_popup ic_pdficon" value="'. __("Export to PDF",'icwoocommerce_textdomains').'" data-format="pdf" data-popupid="export_pdf_popup" data-hiddenbox="popup_pdf_hidden_fields" data-popupbutton="'. __("Export to PDF",'icwoocommerce_textdomains').'" data-title="'. __("Export to PDF",'icwoocommerce_textdomains').'" />';
                    $output .= '<input type="button" name="'. $admin_page.'_export_print" class="onformprocess open_popup ic_printicon button_search_for_print" value="'. __("Print",'icwoocommerce_textdomains').'"  data-format="print" data-popupid="export_print_popup" data-hiddenbox="popup_print_hidden_fields" data-popupbutton="'. __("Print",'icwoocommerce_textdomains').'" data-title="'. __("Print",'icwoocommerce_textdomains').'" data-form="form" />';
                   
                $output .= '</form>';
				*/
                if($position == "bottom"):
				
				
					
					$output .= '<form id="search_order_pagination" class="search_order_pagination" action='. $mngpg.'" method="post">';
					   $output .= $this->create_hidden_fields($request_);
					$output .= '</form>';
				
					$request_['sub_action'] = 'ic_purchase_print';
					$output .= '<form id="search_order_pagination" class="form_search_for_print" action='. $mngpg.'" method="post">';
					   $output .= $this->create_hidden_fields($request_);
					   
					   $output .= '<input type="hidden" name="export_file_format" value="print" />';
					$output .= '</form>';
					
				endif;
               $output .= '</div>';
			   
			   return $output;
		}
		
		function back_print_botton($position  = "bottom"){
			 $output = '';			 
			 $output = '<div class="back_print_botton noPrint">';
            		 $output .= '<input type="button" name="backtoprevious" value="'. __("Back to Previous",'icwoocommerce_textdomains').'"  class="onformprocess" onClick="back_to_detail();" />';
                    $output .=  '<input type="button" name="backtoprevious" value="'. __("Print",'icwoocommerce_textdomains').'"  class="onformprocess" onClick="print_report();" />';
                 $output .= '</div> ';
            return $output;
		}
		
		
			
		function get_number_columns(){
			$number_columns		= array('order_count','order_date','total_amount');
			$number_columns		= apply_filters("ic_commerce_number_columns",$number_columns);			
			return $number_columns;
		}
		
		function get_price_columns(){
			$number_columns		= array('total_amount');
			
			$number_columns		= apply_filters("ic_commerce_price_columns",$number_columns);			
			return $number_columns;
		}
		
		function get_number_columns_css_style(){
			$number_columns = $this->get_number_columns('total_amount');
			$tds = implode(', td.',$number_columns);
			$ths = implode(', th.',$number_columns);					
			$style = 'th.'.$ths.', td.'.$tds.'{ text-align:right}';
			return $style;
		}
	
	function prepare_query($start_date ,$end_date){}
	
	function get_items($type = ''){
			$rows 			= array();
			$columns 		= array();
			$report_name 	= array();
			$that 			= $this;			
			$order_items 	= $this->get_query_results($rows, $type, $columns, $report_name, $that);
			return $order_items;
	}
	
	function get_query_results($rows = '', $type = '', $columns = '', $report_name = '', $parent_this = ''){
			global $wpdb;
				if(!isset($parent_this->items_query)){
					$request = $parent_this->get_all_request();extract($request);
					
					$order_status			= $parent_this->get_string_multi_request('order_status',$order_status, "-1");
					$hide_order_status		= $parent_this->get_string_multi_request('hide_order_status',$hide_order_status, "-1");
					
					$customers 				= $this->ic_commerce_custom_all_customer_purchased_query($parent_this);
					
					$customer_ids = array();
					$customer_emails = array();
					foreach($customers as $key => $values){
						if(!empty($values->customer_id)){
							$customer_ids[] = $values->customer_id;
						}
						if(!empty($values->billing_email)){
							$customer_emails[] = $values->billing_email;
						}
						
					}
					
					
											
					$sql = " SELECT ";
					$sql .= " SUM(postmeta1.meta_value) 		AS 'total_amount'";					
					$sql .= ", postmeta2.meta_value 			AS 'billing_email'";					
					$sql .= ", postmeta3.meta_value 			AS 'billing_first_name'";					
					$sql .= ", COUNT(postmeta2.meta_value) 		AS 'order_count'";					
					$sql .= ", postmeta4.meta_value 			AS  customer_id";
					$sql .= ", postmeta5.meta_value 			AS  billing_last_name";
					$sql .= ", MAX(posts.post_date)				AS  order_date";
					$sql .= ", CONCAT(postmeta3.meta_value, ' ',postmeta5.meta_value) AS billing_name";
					
					$sql .= " FROM {$wpdb->prefix}posts as posts
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta1 ON postmeta1.post_id=posts.ID
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta2 ON postmeta2.post_id=posts.ID
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta3 ON postmeta3.post_id=posts.ID";
					
					$sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta4 ON postmeta4.post_id=posts.ID";
					
					$sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta5 ON postmeta5.post_id=posts.ID";
					
					if(strlen($order_status_id)>0 && $order_status_id != "-1" && $order_status_id != "no" && $order_status_id != "all"){
							$sql .= " 
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
							LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";
					}
					
					$sql .= " WHERE 1*1";
					$sql .= " AND posts.post_type		= 'shop_order' ";
					$sql .= " AND postmeta1.meta_key	= '_order_total' ";
					$sql .= " AND postmeta2.meta_key	= '_billing_email'";
					$sql .= " AND postmeta3.meta_key	= '_billing_first_name'";					
					$sql .= " AND postmeta4.meta_key	= '_customer_user'";
					$sql .= " AND postmeta5.meta_key	= '_billing_last_name'";
					
					if(strlen($order_status_id)>0 && $order_status_id != "-1" && $order_status_id != "no" && $order_status_id != "all"){
						$sql .= " AND  term_taxonomy.term_id IN ({$order_status_id})";
					}
					
					if(strlen($publish_order)>0 && $publish_order != "-1" && $publish_order != "no" && $publish_order != "all"){
						$in_post_status		= str_replace(",","','",$publish_order);
						$sql .= " AND  posts.post_status IN ('{$in_post_status}')";
					}
					
					if($order_status  && $order_status != '-1' and $order_status != "'-1'")$sql .= " AND posts.post_status IN (".$order_status.")";
					
					if($hide_order_status  && $hide_order_status != '-1' and $hide_order_status != "'-1'")$sql .= " AND posts.post_status NOT IN (".$hide_order_status.")";
					
					if(count($customer_emails)>0){
						$in_customer_emails		= implode("','",$customer_emails);
						$sql .= " AND  postmeta2.meta_value NOT IN ('{$in_customer_emails}')";
					}
					
					if(count($billing_email)>0 and $billing_email != NULL){
						$sql .= " AND  postmeta2.meta_value LIKE '%{$billing_email}%'";
					}
					
					if($billing_name and $billing_name != '-1'){
						$sql .= " AND (lower(concat_ws(' ', postmeta3.meta_value, postmeta5.meta_value)) like lower('%".$billing_name."%') OR lower(concat_ws(' ', postmeta5.meta_value, postmeta3.meta_value)) like lower('%".$billing_name."%'))";
					}
					
					$sql .= " GROUP BY  postmeta2.meta_value Order By billing_first_name ASC, billing_last_name ASC";
					
					//echo $sql;
					
					$parent_this->items_query = $sql;
				}else{
					$sql = $parent_this->items_query;
				}
				
				$order_items = $parent_this->get_query_items($type,$sql);
				
				//$this->print_array($wpdb);
				
				return $order_items;
	}
	
	function ic_commerce_custom_all_customer_purchased_query($parent_this = NULL){
			global $wpdb;
			
			$request = $parent_this->get_all_request();extract($request);
			
			$order_status			= $parent_this->get_string_multi_request('order_status',$order_status, "-1");
			
			$hide_order_status		= $parent_this->get_string_multi_request('hide_order_status',$hide_order_status, "-1");
									
			$sql = " SELECT ";
			$sql .= " postmeta2.meta_value 		AS 	billing_email";
			$sql .= ", postmeta4.meta_value 	AS  customer_id";
			
			
			
			$sql .= " FROM {$wpdb->prefix}posts as posts					
			LEFT JOIN  {$wpdb->prefix}postmeta as postmeta2 ON postmeta2.post_id=posts.ID";
			
			$sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta4 ON postmeta4.post_id=posts.ID";
			if(strlen($order_status_id)>0 && $order_status_id != "-1" && $order_status_id != "no" && $order_status_id != "all"){
					$sql .= " 
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";
			}
			$sql .= "
			
			WHERE  
			posts.post_type='shop_order'  
			AND postmeta2.meta_key='_billing_email'";
			
			$sql .= " AND postmeta4.meta_key='_customer_user'";
			
			
			if(strlen($order_status_id)>0 && $order_status_id != "-1" && $order_status_id != "no" && $order_status_id != "all"){
				$sql .= " AND  term_taxonomy.term_id IN ({$order_status_id})";
			}
			
			if($order_date_field_key == "post_date" || $order_date_field_key == "post_modified"){
				if ($start_date != NULL &&  $end_date !=NULL){
					$sql .= " AND DATE(posts.{$order_date_field_key}) BETWEEN '".$start_date."' AND '". $end_date ."'";
				}
			}
			
			/*if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '".$start_date."' AND '". $end_date ."'";
			}*/
			if(strlen($publish_order)>0 && $publish_order != "-1" && $publish_order != "no" && $publish_order != "all"){
				$in_post_status		= str_replace(",","','",$publish_order);
				$sql .= " AND  posts.post_status IN ('{$in_post_status}')";
			}
								
			if($order_status  && $order_status != '-1' and $order_status != "'-1'")$sql .= " AND posts.post_status IN (".$order_status.")";
			
			if($hide_order_status  && $hide_order_status != '-1' and $hide_order_status != "'-1'")$sql .= " AND posts.post_status NOT IN (".$hide_order_status.")";
			
			$sql .= "  GROUP BY  postmeta2.meta_value Order By billing_email ASC";
			
			$customer = $wpdb->get_results($sql);
			
			//$this->print_array($wpdb);
			
			return $customer;
		}
	
	var $request = false;
		
	function get_all_request(){
		global $request;
		if(!$this->request){
			
			do_action("ic_commerce_detail_page_before_default_request");				
			$request 									= array();		
			$default_request							= array();
			$default_request['start_date'] 				=  NULL;
			$default_request['end_date'] 				=  NULL;
			$default_request['sort_by'] 				=  '';
			$default_request['order_by'] 				=  '';
			$default_request['limit'] 					=  '5';
			$default_request['p'] 						=  '1';
			$default_request['action'] 					=  '';
			$default_request['admin_page'] 				=  '10';
			$default_request['ic_admin_page'] 			=  '';
			$default_request['adjacents'] 				=  '3';
			$default_request['do_action_type'] 			=  '';
			$default_request['page_title'] 				=  '';
			$default_request['total_pages'] 			=  '0';
			$default_request['date_format'] 			=  'F j, Y';
			$default_request['page_name'] 				=  'all_detail';
			$default_request['onload_search'] 			=  'yes';
			$default_request['count_generated'] 		=  0;
			$default_request['total_row_count'] 		=  0;
			$default_request['warehouse_id'] 			=  '-1';
			$default_request['order_status'] 			=  '-1';
			$default_request['hide_order_status'] 		=  '-1';
			$default_request['order_date_field_key'] 	=  'post_date';
			$default_request['page'] 					=  '';
			$default_request['report_name'] 			=  '';
			$default_request['order_status_id']			=  '-1';
			
			$default_request['publish_order'] 			=  '';
			$default_request['report_name'] 			=  '';
			
			 
			
			$_REQUEST 									= array_merge((array)$default_request, (array)$_REQUEST);
			
			$limit										= $_REQUEST['limit'];
			$p											= $_REQUEST['p'];
			
			$_REQUEST['start']							= ($p > 1) ? (($p - 1) * $limit) 	: 0;				
			
			if(isset($_REQUEST)){
				$REQUEST = $_REQUEST;
				$REQUEST = apply_filters("ic_commerce_before_request_creation", $REQUEST);
				foreach($REQUEST as $key => $value ):						
					$request[$key] =  $this->get_request($key,NULL);
				endforeach;
				$request = apply_filters("ic_commerce_after_request_creation", $request);
			}
			
			$this->request = $request;				
		}
		return $this->request;
	}
	
	var $request_string = array();
	function get_string_multi_request($id=1,$string, $default = NULL){
		
		if(isset($this->request_string[$id])){
			$string = $this->request_string[$id];
		}else{
			if($string == "'-1'" || $string == "\'-1\'"  || $string == "-1" ||$string == "''" || strlen($string) <= 0)$string = $default;
			if(strlen($string) > 0 and $string != $default){ $string  		= "'".str_replace(",","','",$string)."'";}
			$this->request_string[$id] = $string;			
		}
		
		return $string;
	}
	
	function get_final_order_items($type,$order_items,$report_name){
		return $order_items;			
	}
	
	var $all_row_result;
	function get_query_items($type,$sql,$total_amount = 'total_amount'){
			global  $wpdb;
			$request = $this->get_all_request();extract($request);
			$wpdb->flush(); 				
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			if($type == 'total_row'){
				
				if($this->all_row_result){
					if($count_generated == 1){
						$order_items = $this->create_summary($request);
						//$this->print_array($order_items);
						//echo "1";
					}else{
						$order_items = $this->all_row_result;
						$summary = $this->get_count_total($order_items,$total_amount);				
						$order_items = $summary;
						//echo "2";
					}
					
				}else{					
					if($count_generated == 1 || ($p > 1)){
						$order_items = $this->create_summary($request);
						//echo "3";
					}else{
						$order_items = $wpdb->get_results($sql);
						if($wpdb->last_error){
							echo $wpdb->last_error;
						}
						$order_items = $this->get_final_order_items($type,$order_items,$report_name);
						
						$order_items 	= apply_filters("ic_commerce_report_page_data_items",  $order_items, $request, $type, $page, $report_name);
						
						//echo mysql_error();
						$summary = $this->get_count_total($order_items,$total_amount);				
						$order_items = $summary;
						//echo "4";
						
					}					
				}
				return $order_items;
			}
			
			if($type == 'limit_row'){					
				$sql .= " LIMIT $start, $limit";
				$order_items = $wpdb->get_results($sql);
				if($wpdb->last_error){
					echo $wpdb->last_error;
				}
				$order_items = $this->get_final_order_items($type,$order_items,$report_name);
				$wpdb->flush(); 
			}
			
			if($type == 'all_row' or $type == 'all_row_total'){
				$order_items = $wpdb->get_results($sql);
				if($wpdb->last_error){
					echo $wpdb->last_error;
				}
				$order_items = $this->get_final_order_items($type,$order_items,$report_name);
				$this->all_row_result = $order_items;
				$wpdb->flush(); 
			}
			
			$order_items 	= apply_filters("ic_commerce_report_page_data_items",  $order_items, $request, $type, $page, $report_name);
			
			return $order_items;
		}
		
		function get_count_total($data,$amt = 'total_amount'){
			$total = 0;
			$return = array();
			$report_name 		= $this->get_request('report_name');
			$total_columns 		= $this->result_columns($report_name);
			//$this->print_array($total_columns);
			$order_status		= array();
			if(count($total_columns) > 0){
				//$this->print_array($data);
				
				foreach($data as $key => $value){
					$total = $total + (isset($value->$amt) ? $value->$amt : 0);

					foreach($total_columns as $ckey => $label):
						$v = isset($value->$ckey) ? trim($value->$ckey) : 0;
						$v = empty($v) ? 0 : $v;						
						$return[$ckey] 	= isset($return[$ckey])	? ($return[$ckey] + $v): $v;
					endforeach;
				}
			}else{
				foreach($data as $key => $value){
					$total = $total + (isset($value->$amt) ? $value->$amt : 0);
				}
			}
			$return['total_row_amount'] = $total;
			$return['total_row_count'] = count($data);			
			//$this->print_array($return);
			return $return;
		}

	
	function ajax(){
		//echo json_encode($_REQUEST);
		//die;	
		$call	= $this->get_request('call');
		switch ($call) {
			case "ic-customer-non-purchase":
				$this->customer_non_purchase_query();
				break;			
			case "green":
				echo "Your favorite color is green!";
				break;
			default:
				echo "Your favorite color is neither red, blue, nor green! AJAX 456";
		}
		die;
	}
}