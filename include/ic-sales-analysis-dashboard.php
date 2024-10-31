<?php
require_once('ic-function.php');  
class  ic_sales_analysis_dashboard extends ic_function{
	public function __construct(){
		
	}
	function init(){
	$this->get_not_sold_products();
	//$this->get_all_products();	
	$start_date = date_i18n("Y-m-d");
	$end_date = date_i18n("Y-m-d");	
	$sales_order = $this->get_sales_order($start_date,$end_date,array('wc-on-hold','wc-completed'));
	//echo $sales_order[0]->order_count;
	//echo $sales_order[0]->order_total;
	?>
        <div class="ic_analysis_wrap">
      	<div style="font-size:25px; margin-bottom:20px;"><?php esc_html_e('Woocommerce Sales Trend Analysis - Dashboard','icsalestrendsanalysis')?>  </div>
        <div class="ic_container-liquid">        
            <div class="row ic_summary">
                <div class="col-xs-2">
                    <div class="ic_block ic_block-green">
                        <i class="fa fa-bar-chart"></i>
                        <h4>Orders Placed</h4>
                        <span class="ic_value"><?php echo  $sales_order[0]->order_count; ?></span>
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="ic_block ic_block-pink">
                        <i class="fa fa-bar-chart"></i>
                        <h4>Gross Sales</h4>
                        <span class="ic_value"><?php echo wc_price( $sales_order[0]->order_total); ?></span>
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="ic_block ic_block-purple">
                        <i class="fa fa-bar-chart"></i>
                        <h4>Total Product</h4>
                        <span class="ic_value"><?php echo $this->get_all_products(); ?></span>
                    </div>
                </div>
                
                <div class="col-xs-2">
                    <div class="ic_block ic_block-purple">
                        <i class="fa fa-bar-chart"></i>
                        <h4>Product Not Sold </h4>
                        <span class="ic_value"><?php echo $this-> get_not_sold_products(); ?></span>
                    </div>
                </div>
            </div>
           
            <div class="row">
                <div class="col-md-6">
                    <div class="ic_postbox">
                        <h3>Top Products By Quantity</h3>
                        <div class="ic_overflow">
                        <?php $this->get_product_grid($start_date ,$end_date,"PRODUCT_ID","QTY","DESC","10"); ?> 
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ic_postbox ic_overflow">
                        <h3>Top Products By Total</h3>
                        <div class="ic_overflow">
                         <?php $this->get_product_grid($start_date ,$end_date,"PRODUCT_ID","TOTAL","DESC","10"); ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
	}
	function get_post_meta($post_id=NULL,$meta_key=NULL){
		global $wpdb;
		$query = "";
		$query= " SELECT postmeta.meta_value FROM  {$wpdb->prefix}postmeta as postmeta ";
		$query .= " WHERE 1=1 ";
		$query .= " AND postmeta.meta_key='{$meta_key}'";
		$query .= " AND postmeta.post_id={$post_id} ";
		$results = $wpdb->get_var( $query);	
		
		return $results;
		
	}
	function get_product_category($post_id=NULL){
		$terms = get_the_terms( $post_id, 'product_cat' );
		$product_cat_name  = "";
		if ($terms) {
			foreach ($terms as $term) {
				if (strlen($product_cat_name )==0)
					$product_cat_name = $term->name;
				else	
					$product_cat_name .= "," . $term->name;
			}
		}
		return $product_cat_name;
	}
	function get_sales_product($start_date=NULL, $end_date=NULL, $group_by="PRODUCT_ID", $order_by="QTY", $order="DESC", $limit ="ALL"){
		global $wpdb;
		$query = "";
		$query= " SELECT 
						
						order_item_name
						,product_id.meta_value as product_id
						,SUM(qty.meta_value) as total_qty
						,SUM(line_total.meta_value)  as line_total
						 FROM  {$wpdb->prefix}posts as posts ";
						 
		$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
		
		//$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as variation_id ON variation_id.order_item_id=order_items.order_item_id ";
		$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
		
		$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
		
		$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id ";
		
		
		$query .= " WHERE 1=1 ";
		$query .= " AND posts.post_type='shop_order'";
		$query .= " AND order_items.order_item_type='line_item'";
		$query .= " AND line_total.meta_key='_line_total'";
		
		$query .= " AND product_id.meta_key='_product_id'";
		
		$query .= " AND qty.meta_key='_qty' ";
		
		if ($start_date && $end_date) :
			$query .= "	AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}' ";
		endif;
		
		if ($group_by=="ITEM_NAME"):
			$query .= " GROUP By order_item_name  ";
		endif;
		
		if ($group_by=="PRODUCT_ID"):
			$query .= " GROUP By product_id  ";
		endif;
		
		if ($order_by=="QTY"):
			$query .= " Order By CAST(SUM(qty.meta_value) AS SIGNED) " . $order;
		endif;
		
		if ($order_by=="TOTAL"):
			$query .= " Order By CAST(SUM(line_total.meta_value) AS SIGNED) " . $order;
		endif;
		
		if ($limit !="ALL"):
			$query .= "   LIMIT {$limit}  ";
		endif;	
			
		$results = $wpdb->get_results( $query);	
		//$this->print_array($results);
		return $results;
	}
	function get_product_grid($start_date=NULL, $end_date=NULL, $group_by="PRODUCT_ID", $order_by="QTY", $order="DESC",$limit ="ALL"){
		//$start_date = date_i18n("Y-m-d")
		//echo $start_date;
		$order_item = $this->get_sales_product($start_date,$end_date,$group_by,$order_by,$order,$limit);
		
		?>
          <table class="widefat">
 			<thead>         
        	<tr>
            	<th style="display:none">Product ID</th>
                <th>Product SKU</th>
                <th>Product Name</th>
                 <th>Category</th>
                <th>Total Quantity</th>
                <th>Product Total</th>
            </tr>
            </thead>
            <tbody>
        <?php
		foreach($order_item as $key=>$value){
		?>
        	<tr>
            	<td style="display:none" ><?php echo $value->product_id; ?></td>
                <td><?php echo $this->get_post_meta($value->product_id,"_sku") ?></td>
                <td><?php echo $value->order_item_name; ?></td>
                <td><?php echo $this->get_product_category($value->product_id) ?></td>
                <td><?php echo $value->total_qty; ?></td>
                <td><?php echo wc_price($value->line_total); ?></td>
            </tr>    
        <?php	
			//echo $value->total_qty;
		}
		?>
        <tbody>
        </table>
        	<?php
	}
	function get_sales_order($start_date=NULL, $end_date=NULL,$post_status=array()){
		global $wpdb;
		$query = "";
		$query= " SELECT COUNT(*)as order_count ,SUM(postmeta.meta_value) as order_total   FROM  {$wpdb->prefix}posts as posts ";
		
		$query .= "	LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=posts.ID ";
		
		$query .= " WHERE 1=1 ";
		$query .= " AND posts.post_type='shop_order'";
		$query .= " AND postmeta.meta_key='_order_total'";
		
		if ($start_date && $end_date) :
			$query .= "	AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}' ";
		endif;
		
		if ($post_status) :
			$post_status =implode("','",$post_status);
			$query .= " AND posts.post_status IN ('{$post_status}')";
		endif;	
		 
		$results = $wpdb->get_results( $query);	
		//echo $results[0]->order_count;
		//$this->print_array($results);
		return $results;
	}
	function get_all_products(){
		global $wpdb;
		$query = "";
		$query= " SELECT count(*) as order_product  FROM  {$wpdb->prefix}posts as posts ";
	
		
		$query .= " WHERE 1=1 ";
		$query .= " AND posts.post_type='product'";
		
		$results = $wpdb->get_var( $query);
		//$this->print_array($results);	
		return $results;
	}
	function get_not_sold_products(){
		global $wpdb;
		$product_ids = "";
		$query = "";
		$query= " SELECT 
						product_id.meta_value as product_id
						
						 FROM  {$wpdb->prefix}posts as posts ";
						 
		$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
		$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id ";
		$query .= " WHERE 1=1 ";
		$query .= " AND posts.post_type='shop_order'";
		$query .= " AND order_items.order_item_type='line_item'";

		$query .= " AND product_id.meta_key='_product_id'";
		
		$query .= " Group By product_id ";
		
		$results = $wpdb->get_results( $query);	
		
		
		//echo implode(",",$results);
		//echo $output = implode(', ', array_map(function ($v, $k) { return $k . '=' . $v; }, $results, array_keys($results)));
		foreach($results as $key=>$value) {
			//echo $value->product_id;
			if(strlen($product_ids)==0){
			$product_ids =  $value->product_id;
			}else{
				$product_ids .= "," .$value->product_id;
			}
		}
		$query= " SELECT 
						count(posts.ID) as product_id
						
						 FROM  {$wpdb->prefix}posts as posts ";
	
		$query .= " WHERE 1=1 ";
		$query .= " AND posts.post_type='product'";
		$query .= " AND posts.post_status='publish'";
		$query .= " AND posts.ID NOT IN ({$product_ids})";
		
		
		 $results = $wpdb->get_var( $query);	
		//$this->print_array($results);	
		
		return $results;
		
	}
	 
}
?>

