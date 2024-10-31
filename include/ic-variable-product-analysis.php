<?php
require_once('ic-function.php');  
class ic_variable_product_analysis extends ic_function{
	public function __construct(){
		
	}
	function init(){
	//$this->prepare_query();
		$start_date 			=  $this->get_request ("start_date",date_i18n("Y-m-d"),true);
		$end_date 				=  $this->get_request ("end_date",date_i18n("Y-m-d"),true);
		?>
        <div class="ic_analysis_wrap">
			<div style="font-size:25px; margin-bottom:20px;">Variable Product Order Quantity Analysis</div>
            <div class="ic_container-liquid">
                <div class="ic_navigation">
                    <div class="ic_heading">Custom Search</div>
                    <div class="ic_content">
                        <div class="ic_search_report_form">
                            <form name="frm_variable_product" id="frm_variable_product" method="post">
                                <div class="ic_form-group">
                                    <div class="ic_FormRow ic_firstrow">
                                        <div class="ic_label-text"><label for="start_date">Start Date:</label></div>
                                        <div class="ic_input-text"><input type="text" name="start_date" class="_date" id="start_date" value="<?php echo $start_date ; ?>" /></div>
                                    </div>
                                    <div class="ic_FormRow ic_secondrow">
                                        <div class="ic_label-text"><label for="end_date">End Date:</label></div>
                                        <div class="ic_input-text"><input type="text" name="end_date" 	 class="_date" id="end_date" value="<?php echo $end_date ; ?>"  /></div>
                                    </div>
                                </div>
                                
                                <div class="ic_form-group">
                                    <div class="ic_FormRow ic_firstrow">
                                        <div class="ic_label-text"><label for="start_date">Product:</label></div>
                                        <div class="ic_input-text">
                                            <?php $data=  $this->get_product("VARIABLE",NULL)?>
                                            <?php $this->create_dropdown($data,"product_id[]","product_id","Select All","product_id",'-1', 'object', true, 5);?>
                                        </div>
                                    </div>
                                    <div class="ic_FormRow ic_secondrow">
                                        <div class="ic_label-text"><label for="end_date">Order By:</label></div>
                                        <div class="ic_input-text">
                                            <select name="order" id="order" class="ic_sort_by">
                                                <option value="quantity">quantity</option>
                                                <option value="product_name">product_name</option>
                                            </select>
                                            <select name="order_by" id="order_by" class="ic_sort_by">
                                                <option value="desc">DESC</option>
                                                <option value="asc">asc</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="action" id="action" value="ic_sales_analysis_ajax" />
                                <input type="hidden" name="sub_action" id="sub_action" value="variable-analysis" />
                                <input type="hidden" name="call" id="call" value="variable-product" />
                                  
                                <div class="ic_form-group">
                                    <div class="ic_FormRow ic_Fullwidth">
                                        <span class="ic_submit_buttons">
                                            <input type="submit" value="Search" class="ic_formbtn" />
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
	}
	function prepare_query(){
		
		
		
		global $wpdb;
		
		$start_date 			=  $this->get_request ("start_date",date_i18n("Y-m-d"),true);
		$end_date 				=  $this->get_request ("end_date",date_i18n("Y-m-d"),true);
		$product_id 			=  $this->get_request ("product_id",0,true);
		
		$order 					=  $this->get_request ("order",'quantity',true);
		$order_by 				=  $this->get_request ("order_by",'desc',true);
		
		$query					= "";
		if ($product_id!="-1")
			$products = $this->get_product("VARIABLE",$product_id,"ARRAY_A");
		else
			$products = $this->get_product("VARIABLE",NULL,"ARRAY_A");
			
	
		foreach($products  as $key=>$value):
			$product_id =$value["id"]; 
			if (strlen($query)==0) {
				$query .= "SELECT 
							qty.meta_value as qty, count(*) as order_count,
							(count(*) * line_total.meta_value) as line_total,
							order_items.order_item_name as order_item_name,
							order_items.order_item_id as order_item_id,
							product_id.meta_value  as product_id,
							variation_id.meta_value as variation_id
							 FROM  {$wpdb->prefix}posts as posts ";
			
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id ";
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
			
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as variation_id ON variation_id.order_item_id=order_items.order_item_id ";
				
				
				$query .= " WHERE 1=1 ";
				$query .= " AND posts.post_type='shop_order'";
				$query .= " AND order_items.order_item_type='line_item'";
				$query .= " AND product_id.meta_key='_product_id'";
				$query .= " AND qty.meta_key='_qty'";
				$query .= " AND line_total.meta_key='_line_total'";
				$query .= " AND variation_id.meta_value>'0'";
				$query .= " AND variation_id.meta_key='_variation_id'";
				$query .= " AND product_id.meta_value={$product_id}";
			
				$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}' ";
			
				$query .= " GROUP By qty.meta_value,variation_id.meta_value  ";
				//$query .= " order By CAST(variation_id.meta_value AS SIGNED) ";
			}
			else{
				$query .= " UNION ";
				$query .= "SELECT 
							qty.meta_value as qty, count(*) as order_count,
							(count(*) * line_total.meta_value) as line_total,
							order_items.order_item_name as order_item_name,
							order_items.order_item_id as order_item_id,
							product_id.meta_value  as product_id,
							variation_id.meta_value as variation_id
							 FROM  {$wpdb->prefix}posts as posts ";
			
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id ";
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
			
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
				$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as variation_id ON variation_id.order_item_id=order_items.order_item_id ";
				
				
				$query .= " WHERE 1=1 ";
				$query .= " AND posts.post_type='shop_order'";
				$query .= " AND order_items.order_item_type='line_item'";
				$query .= " AND product_id.meta_key='_product_id'";
				$query .= " AND qty.meta_key='_qty'";
				$query .= " AND line_total.meta_key='_line_total'";
				$query .= " AND variation_id.meta_value>'0'";
				$query .= " AND variation_id.meta_key='_variation_id'";
				$query .= " AND product_id.meta_value={$product_id}";
			
				$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}' ";
			
				$query .= " GROUP By qty.meta_value,variation_id.meta_value  ";
			}
		endforeach;
		
		if ($order == "quantity")
			$query .= " order By CAST(qty AS SIGNED) " . $order_by;
		if ($order == "product_name")
			$query .= " order By order_item_name " . $order_by;	
		
				
	
		
			
			$results = $wpdb->get_results( $query);	
			foreach(	$results as $key=>$value){
				$results[$key]->variation = $this->get_product_variation($value->order_item_id); 
			}
			//$this->print_array($results);
			
		return $results;		
	}
	
	function get_variable_product(){
		$results = $this->prepare_query();
		?>
        <div class="ic_overflow">
        <table class="widefat">
        	<thead>
                <tr>
                    <th style="display:none">ID</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Variation</th>
                    <th style="text-align:right">No of Order</th>
                    <th style="text-align:right">Quantity</th>
                    <th style="text-align:right">Price</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
			<?php foreach($results as $key => $value): ?>
                <tr>
                    <td style="display:none" ><?php echo $value->product_id; ?></td>
                    <td><?php echo get_post_meta($value->variation_id, '_sku',true); ?></td>
                    <td><?php echo $this->get_product_cat($value->product_id) ?></td>
                    <td><?php echo $value->order_item_name;  ?></td>
                    <td><?php echo $value->variation;  ?></td>
                    <td style="text-align:right"><?php echo $value->order_count; ?></td>
                    <td style="text-align:right"><?php echo $value->qty; ?></td>
                    <td style="text-align:right"><?php echo wc_price($value->line_total/ ( $value->order_count *  $value->qty)); ?></td>
                    <td style="text-align:right"><?php echo wc_price($value->line_total); ?></td>
                </tr>
            <?php endforeach; ?>
        	</tbody>
         </table>
         </div>
        <?php
		
		
	}
	function get_product_variation($order_item_id){
		global $wpdb;
		$all_variation = $this->get_all_variation(); 
		$product_variation = "";
		//$this->print_array($all_variation);
		$query = "";
		$query = " SELECT * FROM  {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta "; 
		$query .= " WHERE 1=1 ";
		$query .= " AND order_itemmeta.order_item_id='{$order_item_id}'";
		$results = $wpdb->get_results( $query);	
		
		
		
		foreach($results as $key=>$value){
			//echo $value->meta_key;
			if (in_array($value->meta_key, $all_variation)){
				if (strlen($product_variation)==0)
				$product_variation = $value->meta_value;
				else
				$product_variation .= ", ".$value->meta_value;
			}
		}
		//$this->print_array($results);
		//	$product_variation;
		return $product_variation;	
	}
	function ajax(){
		
	//echo json_encode($_REQUEST);
	//die;	
	 $call	= $this->get_request('call');
	 switch ($call) {
			case "variable-product":
				$this->get_variable_product();
				break;			
			case "green":
				echo "Your favorite color is green!";
				break;
			default:
				echo "Your favorite color is neither red, blue, nor green! AJAX";
		}
		die;
	}	
}
?>