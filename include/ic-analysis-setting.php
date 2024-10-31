<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'ni_invoice_setting' ) ) {
	class ic_analysis_setting{
		
	
		
		function __construct() {
			
			 add_action( 'admin_menu', array( $this, 'add_setting_page' ) );
			 add_action( 'admin_init', array( $this, 'admin_init' ) );
		}
		function add_setting_page(){
			add_submenu_page( 'sales-analysis', 'Setting', 'Setting', 'manage_options', 'ic-invoice-setting', array( $this, 'setting_page' ) );
		}
		function setting_page(){
			$this->options = get_option( 'ni_invoice_option' );			
			?>
			<div class="wrap">
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'ni_invoice_option_group' );   
					do_settings_sections( 'my-setting-admin' );
					submit_button(); 
				?>
				</form>
			</div>
			<?php
		}
		function admin_init(){
			register_setting(
				'ni_invoice_option_group', // Option group
				'ni_invoice_option', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);
			
			add_settings_section(
				'setting_section_id', // ID
				'Invoice Settings', // Title
				array( $this, 'print_section_info' ), // Callback
				'my-setting-admin' // Page
			);
			
			/*Add Store Name*/ 
			add_settings_field(
				'store_name', 
				'Store Name', 
				array( $this, 'add_store_name' ), 
				'my-setting-admin', 
				'setting_section_id'
			);    
			/*Add Footer Notes*/ 
			add_settings_field(
				'footer_notes', 
				'Footer Notes', 
				array( $this, 'add_footer_notes' ), 
				'my-setting-admin', 
				'setting_section_id'
			);     
			 
		}
		function add_store_name(){
			printf(
				'<input type="text" id="store_name" name="ni_invoice_option[store_name]" value="%s" />',
				isset( $this->options['store_name'] ) ? esc_attr( $this->options['store_name']) : ''
				//esc_attr( $this->options['name'])
			);
		}
		function add_footer_notes(){
			
			printf('<textarea rows="6" name="ni_invoice_option[footer_notes]" id="footer_notes"  cols="50">%s</textarea>',	
				isset( $this->options['footer_notes'] ) ? esc_attr( $this->options['footer_notes']) : ''
			);
		}
		function print_section_info(){
    		print 'Enter your settings below:';
		}
		function sanitize( $input ){
			if( !is_numeric( $input['id_number'] ) )
				$input['id_number'] = '';  
		
			if( !empty( $input['title'] ) )
				$input['title'] = sanitize_text_field( $input['title'] );
				
			if( !empty( $input['color'] ) )
				$input['color'] = sanitize_text_field( $input['color'] );
			return $input;
		}
	}
}
?>