jQuery(function($){
	
	 jQuery('._date').datepicker({
        dateFormat : 'yy-mm-dd'
    });
	//alert("a");
	// alert(ajax_object.ic_ajax_url);
	jQuery( "#frm_new_repeat_customer_analysis" ).submit(function( e ) {
		// alert(ajax_object.ic_ajax_url);
		//return false;
		$.ajax({
			
			url:ajax_object.ic_ajax_url,
			data:$("#frm_new_repeat_customer_analysis").serialize(),
			
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