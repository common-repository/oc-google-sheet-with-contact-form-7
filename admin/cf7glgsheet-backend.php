<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('cf7glgsheet_backend')) {

    class cf7glgsheet_backend {

        protected static $instance;
        protected $gs_uploads   = array();

         function cf7glgsheet_my_menu_pages(){

            add_submenu_page( 'wpcf7', __( 'Google Sheet', CF7GLGSHEETPREFIX), __( 'Google Sheet', CF7GLGSHEETPREFIX),'manage_options', PAGE_SLUG, array($this, 'cf7glgsheet_list_table_page') );
        }

        	function cf7glgsheet_list_table_page(){ ?>

   			<div class="wrap cf7glgsheet-form"> 
		         <h1><?php echo esc_html( __( 'Elegant Google Sheet Contact Form 7', 'cf7glgsheet' ) ); ?></h1>
		         <div class="cf7glgsheet-parts">
		            <div class="cf7glgsheet-card" id="googlesheet">
		               <div class="inside">
		                  <p class="cf7glgsheet-alert"> <?php echo esc_html( __( 'Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox. ', 'cf7glgsheet' ) ); ?></p>
		                  <p>
			                  <label><?php echo esc_html( __( 'Google Access Code', 'cf7glgsheet' ) ); ?></label>
					            <?php if (!empty(get_option('cf7glgsheet_token')) && get_option('cf7glgsheet_token') !== "") { ?>
						               <input type="text" name="cf7glgsheet-code" id="cf7glgsheet-code" value="" disabled placeholder="<?php echo esc_html(__('Currently Active', 'cf7glgsheet')); ?>"/>
						               <input type="button" name="deactivate-log" id="deactivate-log" value="<?php _e('Deactivate', 'cf7glgsheet'); ?>" class="button button-primary" />
						               <span class="tooltip"> <img src="" class="help-icon"> <span class="tooltiptext tooltip-right">On deactivation, all your data saved with authentication will be removed and you need to reauthenticate with your google account.</span></span>
						               <span class="loading-sign-deactive"></span>
					            <?php } else { ?>
					               	<input type="text" name="cf7glgsheet-code" id="cf7glgsheet-code" value="" placeholder="<?php echo esc_html(__('Enter Code', 'cf7glgsheet')); ?>"/>
					                  <a href="https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=537975197823-pb2q2djlo5915appk5d000u0km677ohc.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&response_type=code&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F+https://www.googleapis.com/auth/userinfo.email+https://www.googleapis.com/auth/drive.metadata.readonly" target="_blank" class="button">Get Code</a>
					            <?php } ?>
		                  </p>
				            <?php if (empty(get_option('cf7glgsheet_token'))) { ?>
				                  <p> 
				                     <input type="button" name="save-cf7glgsheet-code" id="save-cf7glgsheet-code" value="<?php _e( 'Save','cf7glgsheet');?>"
				                            class="button button-primary" />
				            <?php } ?>
		                     <span class="loading-sign"></span>
		                  </p>
						  		<?php
								$token = get_option('cf7glgsheet_token');
								if ( ! empty( $token ) && $token !== "") {

									$google_sheet = new CF7GSC_googlesheet();	
									
									$email_account = $google_sheet->gsheet_print_google_account_email(); 
								
									if( $email_account ) {?>
										<p class="connected-account"><?php printf(__( 'Connected email account: %s','gsheetconnector-gravityforms'),$email_account); ?><p>
									<?php }else{?>
			                      <p style="color:red" ><?php echo esc_html(__('Something wrong ! Your Auth Code may be wrong or expired. Please deactivate and do Re-Authentication again. ', 'cf7glgsheet')); ?></p>
			                  <?php 
			                  } 
								}?>
		               	<p>
		                     <!-- <label><a href= "<?php echo plugins_url( '/logs/log.txt', __FILE__ ); ?>" target="_blank" class="debug-view">View</a></label> -->
		                     <!-- <label><a class="debug-clear">Clear</a></label>
		                     <span class="clear-loading-sign"></span> -->
		                  </p>
		                  <p id="cf7glgsheet-validation-message"></p>
		                  <span id="deactivate-message"></span>
		                  <input type="hidden" name="cf7glgsheet-ajax-nonce" id="cf7glgsheet-ajax-nonce" value="<?php echo wp_create_nonce( 'cf7glgsheet-ajax-nonce' ); ?>" />
		               </div>
		            </div>
		         </div>
		         <div>
		            <a href="https://www.gsheetconnector.com/" target="_blank"></a>
		         </div>
		      </div>
     		<?php 
       	}



	   function cf7glgsheet_recursive_sanitize_text_field($array) {

	        foreach ( $array as $key => &$value ) {

	            if ( is_array( $value ) ) {
	                $value = $this->cf7glgsheet_recursive_sanitize_text_field($value);
	            }else{
	                $value = sanitize_text_field( $value );
	            }

	        }
	        return $array;
	   }


		function cf7glgsheet_editor_panels( $panels ) { 

            $paypal = array(
                	'googlesheet-panel' => array(
                  'title' => __( 'Google Sheets', 'contact-form-7' ),
                  'callback' => array( $this, 'cf7glgsheet_editor_panel_popup'),
                ),
            );

            $panels = array_merge($panels,$paypal);

            return $panels; 
      }


    	function cf7glgsheet_editor_panel_popup() { 
			$formid= "";
    		$wpcf7_contact_form = WPCF7_ContactForm::get_current();
			$contact_form_tags = $wpcf7_contact_form->scan_form_tags();
			if(isset($_REQUEST['post'])){
				$formid = sanitize_text_field($_REQUEST['post']);
			}
			
			$cf7_googlesheet_all_tag = get_post_meta( $formid, 'cf7_googlesheet_all_tag'  );?>

			<h3 class="glg_out_tag"><?php echo __('Google Output Tag', CF7GLGSHEETPREFIX); ?></h3>

			<table class="Googlesheet_main">
	            <tbody>
	            	<tr>
	                    <th scope="row">
	                        <label><?php echo __('Enable' , CF7GLGSHEETPREFIX); ?></label>
	                    </th>
	                    <td>
	                        <input type="checkbox" name="cf7_googlesheet_form_enable" <?php if(get_post_meta(  $formid ,'cf7_googlesheet_form_enable' , true ) == "on"){ echo "checked"; } ?>>
	                    </td>
	                </tr>
	                <tr>
	                    <th scope="row">
	                        <label>Google Sheet Name</label>
	                    </th>
	                    <td>
	                        <input type="text" name="cf7_googlesheet_name" value="<?php echo get_post_meta( $formid , 'cf7_googlesheet_name', true );?>">
	                    </td>
	                </tr>
	                <tr>
	                    <th scope="row">
	                        <label>Google Sheet Id</label>
	                    </th>
	                    <td>
	                        <input type="text" name="cf7_googlesheet_ID" value="<?php echo get_post_meta( $formid , 'cf7_googlesheet_ID', true );?>">
	                    </td>
	                </tr>
	                 <tr>
	                    <th scope="row">
	                        <label>Google Sheet Tab Name</label>
	                    </th>
	                    <td>
	                        <input type="text" name="cf7_tab_name" value="<?php echo get_post_meta( $formid , 'cf7_tab_name', true );?>">
	                    </td>
	                </tr>
	                <tr>
	                    <th scope="row">
	                        <label>Google Sheet Tab Id</label>
	                    </th>
	                    <td>
	                        <input type="text" name="cf7_tab_id" value="<?php echo get_post_meta( $formid, 'cf7_tab_id', true );?>">
	                    </td>
	                </tr>
	            </tbody>
	      </table>

			<table class="glg_table_tag">
				
				<?php

				$str_flag = "checked";

				foreach ($contact_form_tags as $contact_form_tag) {
					
					if( $contact_form_tag['name'] != "submit" && $contact_form_tag['name'] != ""){ 
							if(!empty($cf7_googlesheet_all_tag[0])){
							 	if( array_key_exists( $contact_form_tag['name'], $cf7_googlesheet_all_tag[0] ) ){
									$str_flag = "checked";
							 	}else{
							 		$str_flag = "";
							 	}
							}?>
		               <tr>
		                	<td>
                           <input type="checkbox" name='cf7_googlesheet_all_tag[<?php echo $contact_form_tag['name']; ?>]'  <?php echo  $str_flag; ?>>
                        </td>
                        <th scope="row">
                           <label><?php echo $contact_form_tag['name']; ?></label>
                        </th>
			            </tr>

        			<?php } ?>

           	<?php  } ?>

         </table>

             <?php   	    	
		}


		function cf7glgsheet_after_save( $instance ) { 

			  	$formid = $instance->id;
        		$cf7_googlesheet_all_tag =$this->cf7glgsheet_recursive_sanitize_text_field($_POST['cf7_googlesheet_all_tag']);
        		$cf7_googlesheet_form_enable =sanitize_text_field($_REQUEST['cf7_googlesheet_form_enable']);
        		$cf7_googlesheet_name =sanitize_text_field($_REQUEST['cf7_googlesheet_name']);
        		$cf7_tab_id =sanitize_text_field($_REQUEST['cf7_tab_id']);
        		$cf7_googlesheet_ID =sanitize_text_field($_REQUEST['cf7_googlesheet_ID']);
        		$cf7_tab_name =sanitize_text_field($_REQUEST['cf7_tab_name']);

				update_post_meta( $formid,'cf7_googlesheet_all_tag', $cf7_googlesheet_all_tag );
				update_post_meta( $formid,'cf7_googlesheet_form_enable', $cf7_googlesheet_form_enable );
				update_post_meta( $formid,'cf7_googlesheet_name', $cf7_googlesheet_name );
				update_post_meta( $formid,'cf7_tab_id', $cf7_tab_id );
				update_post_meta( $formid,'cf7_googlesheet_ID', $cf7_googlesheet_ID );
				update_post_meta( $formid,'cf7_tab_name', $cf7_tab_name );
		}


		function cf7glgsheet_support_and_rating_notice() {

			$screen = get_current_screen();
                 // print_r($screen);
                if( 'contact_page_elgent_cf7glgsheet_googlesheet' == $screen->base) {
                    ?>
                    <div class="cf7costcaloc_ratess_open">
                        <div class="cf7costcaloc_rateus_notice">
                            <div class="cf7costcaloc_rtusnoti_left">
                                <h3>Rate Us</h3>
                                <label>If you like our plugin,</label>
                                <a target="_blank" href="#">
                                    <label>Please vote us</label>
                                </a>
                                <label>,so we can contribute more features for you.</label>
                            </div>
                            <div class="cf7costcaloc_rtusnoti_right">
                                <img src="<?php echo CF7GLGSHEET_PLUGIN_DIR;?>/includes/images/review.png" class="cf7costcaloc_review_icon">
                            </div>
                        </div>
                        <div class="cf7costcaloc_support_notice">
                            <div class="cf7costcaloc_rtusnoti_left">
                                <h3>Having Issues?</h3>
                                <label>You can contact us at</label>
                                <a target="_blank" href="https://xthemeshop.com/contact/">
                                    <label>Our Support Forum</label>
                                </a>
                            </div>
                            <div class="cf7costcaloc_rtusnoti_right">
                                <img src="<?php echo CF7GLGSHEET_PLUGIN_DIR;?>/includes/images/support.png" class="cf7costcaloc_review_icon">
                            </div>
                            
                        </div>
                    </div>
                    <div class="cf7costcaloc_donate_main">
                       <img src="<?php echo CF7GLGSHEET_PLUGIN_DIR;?>/includes/images/coffee.svg">
                       <h3>Buy me a Coffee !</h3>
                       <p>If you like this plugin, buy me a coffee and help support this plugin !</p>
                       <div class="cf7costcaloc_donate_form">
                            <a class="button button-primary ocwg_donate_btn" href="https://www.paypal.com/paypalme/shayona163/" data-link="https://www.paypal.com/paypalme/shayona163/" target="_blank">Buy me a coffee !</a>
                       </div>
                    </div>
                    <?php
                
        	}
		}

		public function cf7glgsheet_verify_gs_integation() {
	     
	      check_ajax_referer( 'cf7glgsheet-ajax-nonce', 'security' );

	      $Code = sanitize_text_field( $_POST["code"] );

	      update_option( 'cf7glgsheet_access_code', $Code );

	      if ( get_option( 'cf7glgsheet_access_code' ) != '' ) {
	     		require_once CF7GLGSHEET_PLUGIN_DIR_PATH. '/lib/google-sheets.php' ;
	         cf7gsc_googlesheet::preauth( get_option( 'cf7glgsheet_access_code' ) );
	         update_option( 'cf7glgsheet_verify', 'valid' );
	         wp_send_json_success();
	      } else {
	         update_option( 'cf7glgsheet_verify', 'invalid' );
	         wp_send_json_error();
	      }

	   }


	   public function cf7glgsheet_deactivate_gs_integation() {
	      check_ajax_referer('cf7glgsheet-ajax-nonce', 'security');
	      if ( get_option('cf7glgsheet_token') !== '' ) {
	         delete_option('cf7glgsheet_token');
	         delete_option('cf7glgsheet_access_code');
	         delete_option('cf7glgsheet_verify');

	         wp_send_json_success();
	      } else {
	         wp_send_json_error();
	      }
	   }


	   public function cf7_save_to_google_sheets( $form ) {
	      
	      $submission = WPCF7_Submission::get_instance();
	      $form_id = $form->id();
	      $cf7_googlesheet_name = get_post_meta( $form_id, 'cf7_googlesheet_name' );
	     	$cf7_tab_id = get_post_meta( $form_id, 'cf7_tab_id' );
	      $cf7_googlesheet_ID = get_post_meta( $form_id, 'cf7_googlesheet_ID' );
	      $cf7_tab_name = get_post_meta( $form_id, 'cf7_tab_name' );
			$data = array();

	      if ( $submission && (! empty(  $cf7_googlesheet_name ) ) && (! empty( $cf7_tab_name ) ) ) {
				$posted_data = $submission->get_posted_data();
				$cf7_googlesheet_all_tag = get_post_meta( $form_id, 'cf7_googlesheet_all_tag'  );

	         try {

	            require_once CF7GLGSHEET_PLUGIN_DIR_PATH. '/lib/google-sheets.php' ;
	            $doc = new cf7gsc_googlesheet();
	            $doc->auth();
	            $doc->setSpreadsheetId( $cf7_googlesheet_ID );
	            $doc->setWorkTabId( $cf7_tab_id );
	            $meta = array();
	            $special_mail_tags = array( 'serial_number', 'remote_ip', 'user_agent', 'url', 'date', 'time', 'post_id', 'post_name', 'post_title', 'post_url', 'post_author', 'post_author_email', 'site_title', 'site_description', 'site_url', 'site_admin_email', 'user_login', 'user_email', 'user_url', 'user_first_name', 'user_last_name', 'user_nickname', 'user_display_name' );

	            foreach ( $special_mail_tags as $smt ) {
	               $tagname = sprintf( '_%s', $smt );
	         		$mail_tag = new WPCF7_MailTag(
	         			sprintf( '[%s]', $tagname ),
	         			$tagname,
	         			''
	         		);
	               $meta[$smt] = apply_filters( 'wpcf7_special_mail_tags', '', $tagname, false, $mail_tag );
	            }

	            if ( ! empty( $meta ) ) {
	               $data["date"] = $meta["date"];
	               $data["time"] = $meta["time"];
	               $data["serial-number"] = $meta["serial_number"];
	               $data["remote-ip"] = $meta["remote_ip"];
	               $data["user-agent"] = $meta["user_agent"];
	               $data["url"] = $meta["url"];
	               $data["post-id"] = $meta["post_id"];
	               $data["post-name"] = $meta["post_name"];
	               $data["post-title"] = $meta["post_title"];
	               $data["post-url"] = $meta["post_url"];
	               $data["post-author"] = $meta["post_author"];
	               $data["post-author-email"] = $meta["post_author_email"];
	               $data["site-title"] = $meta["site_title"];
	               $data["site-description"] = $meta["site_description"];
	               $data["site-url"] = $meta["site_url"];
	               $data["site-admin-email"] = $meta["site_admin_email"];
	               $data["user-login"] = $meta["user_login"];
	               $data["user-email"] = $meta["user_email"];
	               $data["user-url"] = $meta["user_url"];
	               $data["user-first-name"] = $meta["user_first_name"];
	               $data["user-last-name"] = $meta["user_last_name"];
	               $data["user-nickname"] = $meta["user_nickname"];
	               $data["user-display-name"] = $meta["user_display_name"];
	            }
	            $fggf = array();
	            $cf7_googlesheet_all_tag = get_post_meta( $form_id, 'cf7_googlesheet_all_tag'  );
	            $cf7_googlesheet_all_tag;
	            foreach ( $cf7_googlesheet_all_tag[0] as $kewy => $valuew ) {
	            	 $fggf[] = $kewy;
	            }

	            foreach ( $posted_data as $key => $value ) {

	            	if(in_array($key ,$fggf ) && !empty($fggf)){

	            		if ( strpos( $key, '_wpcf7' ) !== false || strpos( $key, '_wpnonce' ) !== false ) {

							}else{
						   
						    	$uploaded_file = $this->gs_uploads;
						    	if ( array_key_exists( $key, $uploaded_file ) || isset( $uploaded_file[ $key ] ) ) {
									$data[ $key ] = sanitize_file_name($uploaded_file[ $key ]);
									continue;
								}
						  
							   if ( is_array( $value ) ) {
										$data[ $key ] = sanitize_text_field( implode( ', ', $value ) );
								} else {
										$data[ $key ] = sanitize_text_field( stripcslashes( $value ) );
								}

							}

	            	}
	            
						

					}


				           
	            $data = apply_filters( 'gsc_filter_form_data', $data, $form );
	            if( ! empty( $data[0] ) && is_array( $data[0] ) ) {
	              	$doc->add_multiple_row( $data );
	            } else {
	             	$doc->add_row( $data );
	            }     
				}catch(Exception $e){
	            $data['ERROR_MSG'] = $e->getMessage();
	            $data['TRACE_STK'] = $e->getTraceAsString();
	            Gs_Connector_Utility::gs_debug_log( $data );
				}
			}  
		}

    
      function init() { 

        	add_action('wpcf7_after_save', array( $this, 'cf7glgsheet_after_save'), 10, 1 ); 
         add_action('admin_menu',array($this, 'cf7glgsheet_my_menu_pages'));
         add_filter('wpcf7_editor_panels', array( $this, 'cf7glgsheet_editor_panels'), 10, 1 ); 
         add_action('admin_notices', array($this, 'cf7glgsheet_support_and_rating_notice' ));
         add_action('wp_ajax_cf7glgsheet_verify_gs_integation', array( $this, 'cf7glgsheet_verify_gs_integation' ) );
      	//add_action('wp_ajax_gs_clear_log', array( $this, 'gs_clear_logs' ) );
      	add_action('wp_ajax_cf7glgsheet_deactivate_gs_integation', array( $this, 'cf7glgsheet_deactivate_gs_integation' ) );
      	add_action('wpcf7_mail_sent', array( $this, 'cf7_save_to_google_sheets' ) );

      }

        	public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        	}
   }

   cf7glgsheet_backend::instance();
}
