jQuery(function($){
	
	 jQuery('._date').datepicker({
        dateFormat : 'yy-mm-dd'
    });
	//alert("a");
	// alert(ajax_object.ic_ajax_url);
	jQuery( "#frm_customer_non_purchase" ).submit(function( e ) {
		// alert(ajax_object.ic_ajax_url);
		//return false;
		$.ajax({
			
			url:ajax_object.ic_ajax_url,
			data:$("#frm_customer_non_purchase").serialize(),
			
			success:function(data) {
				// This outputs the result of the ajax request
				console.log(data);
				//alert("s");
				$(".ajax_content").html(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
				alert("e");
			}
		}); 
		
		
		return false;
	});
});