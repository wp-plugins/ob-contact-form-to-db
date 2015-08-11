<?php 
/*
Plugin Name: OB Contact Form to DB
Plugin URI: http://owebest.com
Description: OB Contact Form to DB is an addon plugin which enables users to save all entries submitted through OB Contact Form plugin into Database and show it to admin in Admin panel under OB Contact form menu.
Version: 1.0
Author: Owebest
Author URI: http://Owebest.com
*/

$db_version = '1.0';

add_action('admin_enqueue_scripts', 'dbdb_style_admin_data');
	function dbdb_style_admin_data(){
		if(isset($_GET['page']) && $_GET['page'] == 'entries'){
			wp_enqueue_script('datatable', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/media/js/jquery.dataTables.js', array('jquery'));
			wp_enqueue_script('tabletools', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/extensions/TableTools/js/dataTables.tableTools.js', array('jquery'));
			wp_enqueue_script('shCore', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/syntax/shCore.js', array('jquery'));
			wp_enqueue_script('demo', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/js/demo.js', array('jquery'));
			
			//Load Data table style;
			wp_enqueue_style( 'datatable_css', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/media/css/jquery.dataTables.css'); 
			wp_enqueue_style( 'tabletools_css', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/extensions/TableTools/css/dataTables.tableTools.css'); 
			wp_enqueue_style( 'shCore_css', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/syntax/shCore.css'); 
			wp_enqueue_style( 'demo_css', trailingslashit(plugin_dir_url(__FILE__)).'DataTables/css/demo.css'); 	 		
		}	
	}
	
	function dbdb_show_submitted_entries(){
		global $wpdb;
		?>
		<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
		<style type="text/css" class="init">
			tfoot input
			{
				width: 100%;
				padding: 3px;
				box-sizing: border-box;
			}
			h1 a{
				color:#2C2C2C;
				text-decoration:none;
			}
			h1 a:hover{
				text-decoration:underline;
			}
			a{
				color: #069FDF;
				cursor:pointer;
			}
			.success{
				color:#009900;
				font-weight:bold;
			}
			.error{
				color:#F33C21;
				font-weight:bold;
			}
			
		</style>
		<script type="text/javascript" language="javascript" class="init">
			jQuery(document).ready(function() 
			{
				// Setup - add a text input to each footer cell
				
					jQuery('#example1 tfoot th').each( function () 
					{
						var title = jQuery('#example1 thead th').eq( jQuery(this).index() ).text();
						jQuery(this).html( '<input type="text" placeholder="Search '+title+'" />' );
					} );
					
				// DataTable
				
					var table = jQuery('#example1').DataTable(
					{
						"order": [[ 0, "desc" ]],
						dom: 'T<"clear">lfrtip',
						tableTools: 
						{
								"sSwfPath": "<?php echo trailingslashit(plugin_dir_url(__FILE__)); ?>/swf/copy_csv_xls_pdf.swf",
								"aButtons": [{ "sExtends": "csv", "mColumns": "visible", "oSelectorOpts": { page: "current" } }]
						}    
					});
					
				// Apply the search
				
					table.columns().eq( 0 ).each( function ( colIdx ) 
					{
						jQuery( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () 
						{
							table
								.column( colIdx )
								.search( this.value )
								.draw();
						} );
					} );
					
			} );
		</script>
			<div class="head">
				<h2>OB Contact Form</h2>
			</div>
				<?php
					$leads_table = $wpdb->prefix.'obcf_contact_form';
					$entries = $wpdb->get_results("SELECT * FROM $leads_table");
				?>
			<table id="example1" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>ID</th> 
						<th>Name</th> 
						<th>Last Name</th> 
						<th>Phone/Skype</th>
						<th>Email</th>
						<th>Comment</th>
						<th>Date Submitted(Y-m-d)</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>ID</th> 
						<th>Name</th> 
						<th>Last Name</th> 
						<th>Phone/Skype</th>
						<th>Email</th>
						<th>comment</th>
						<th>Date Submitted(Y-m-d)</th>
					</tr>
				</tfoot>
				<tbody>
					<?php 		
						if($entries):
							foreach($entries as $lead)
							{
								$id = $lead->id;
								$name = $lead->first_name;
								$last_name = $lead->last_name;
								$phone = $lead->phone;
								$email = $lead->email;
								$comment = $lead->comment;
								
								$ip = $lead->ip;
								$date = date('Y-m-d',$lead->date_submitted);
								?>
									<tr>
										<th><?php echo $id;?></th> 
										<th><?php echo $name;?></th> 
										<th><?php echo $last_name;?></th> 
										<th><?php echo $phone;?></th>
										<th><?php echo $email;?></th>
										<th><?php echo $comment;?></th>
										<th><?php echo $date;?></th>
									</tr>
								<?php 
							}
						 else:				 
							echo 'No entries Found.';				 
						 endif; 
					?>
				</tbody>
			</table>            
		<?php 
	}
	register_activation_hook( __FILE__, 'obdb_form_activ_func' );
	/* register_deactivation_hook( __FILE__, 'form_deactiv_func' );
	 */
	function obdb_form_activ_func(){
		if(function_exists('obfc_form')){
		
			global $wpdb;
				$leads_table = $wpdb->prefix . 'obcf_contact_form';
				$charset = ( defined( 'DB_CHARSET' && '' !== DB_CHARSET ) ) ? DB_CHARSET : 'utf8';
				$collate = ( defined( 'DB_COLLATE' && '' !== DB_COLLATE ) ) ? DB_COLLATE : 'utf8_general_ci';
				$query ="CREATE TABLE IF NOT EXISTS $leads_table (
						id INT NOT NULL AUTO_INCREMENT ,
						first_name TEXT NOT NULL ,
						last_name TEXT NOT NULL ,
						phone TEXT NOT NULL ,
						email VARCHAR( 255 ) NOT NULL ,
						comment VARCHAR( 255 ) NOT NULL ,
						ip VARCHAR( 255 ) NOT NULL ,
						date_submitted VARCHAR( 255 ) NOT NULL,
						PRIMARY KEY  (id)
						) DEFAULT CHARACTER SET $charset COLLATE $collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $query );
			update_option("ob_contact_form_version", $db_version);
		}
	}
	
	
	// function for inserting submitted data into database
	
	add_action('save_obcf','dbdb_insert_into_db',5,1);
	function dbdb_insert_into_db($post_data)
	{			
		
		global $wpdb;
			$leads_table = $wpdb->prefix . 'obcf_contact_form';
			$data = array();
			$data['first_name'] = sanitize_text_field(trim($post_data['name']));
			$data['last_name'] = sanitize_text_field(trim($post_data['last_name']));
			$data['phone'] = intval(trim($post_data['phone']));
			$data['email'] = sanitize_email(trim($post_data['email']));
			$data['date_submitted'] = time();
			if(isset($post_data['ip'])){
				if($post_data['ip'] === filter_var($post_data['ip'], FILTER_VALIDATE_IP))
				{
					$ip = $post_data['ip'];
				}
			}
			$data['ip'] = $ip;
			$data['comment'] = esc_attr($post_data['comment']);
			$result = $wpdb->query($wpdb->prepare( 
	"INSERT INTO $leads_table	( first_name, last_name, phone, email, date_submitted, ip, comment ) VALUES ( %s, %s, %d, %s,%d,%s,%s )", 
        $data['first_name'], 
	$data['last_name'], 
	$data['phone'],
	$data['email'],
	$data['date_submitted'],
	$data['ip'],
	$data['comment']));
			if($result){
				return true;
			}
			else{
				return false;
			}
	}
add_action( 'admin_menu', 'obdb_submenu_page' );	
	function obdb_submenu_page() {
		add_submenu_page('OB-contact-us-form', 'Entries - OB Contact Form', 'Entries', 'administrator', 'entries', 'dbdb_show_submitted_entries'); 
	}