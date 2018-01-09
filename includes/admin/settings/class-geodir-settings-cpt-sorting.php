<?php
/**
 * GeoDirectory CPT Sorting Settings
 *
 * @author      AyeCode
 * @category    Admin
 * @package     GeoDirectory/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'GD_Settings_Cpt', false ) ) :

	/**
	 * GeoDir_Admin_Settings_General.
	 */
	class GeoDir_Settings_Cpt_Sorting extends GeoDir_Settings_Page {

		/**
		 * Post type.
		 *
		 * @var string
		 */
		private static $post_type = '';

		/**
		 * Sub tab.
		 *
		 * @var string
		 */
		private static $sub_tab = '';

		/**
		 * Constructor.
		 */
		public function __construct() {

			self::$post_type = ! empty( $_REQUEST['post_type'] ) ? sanitize_title( $_REQUEST['post_type'] ) : 'gd_place';
			self::$sub_tab   = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'general';


			$this->id    = 'cpt-sorting';
			$this->label = __( 'Sorting options', 'woocommerce' );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			//add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_toggle_advanced' ) );
			//add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			//add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );


		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {

			$sections = array(
				'' => __( 'Custom Fields', 'woocommerce' ),
				//	'location'       => __( 'Custom fields', 'woocommerce' ),
				//	'pages' 	=> __( 'Sorting options', 'woocommerce' ),
				//'dummy_data' 	=> __( 'Dummy Data', 'woocommerce' ),
				//'uninstall' 	=> __( 'Uninstall', 'woocommerce' ),
			);

			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}

		/**
		 * Output the settings.
		 */
		public function output() {
//		global $current_section, $hide_save_button;
//
//		$settings = $this->get_settings( $current_section );
//
//		GeoDir_Admin_Settings::output_fields( $settings );
//
//		// hide save button on dummy data page
//		if ( 'dummy_data' == $current_section ) {
//			$hide_save_button = true;
//		}

			//geodir_custom_post_type_form();

			$listing_type = self::$post_type;

			$sub_tab = self::$sub_tab;

			include( dirname( __FILE__ ) . '/../views/html-admin-settings-cpt-cf.php' );


		}


		/**
		 * Returns heading for the CPT settings left panel.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The page heading.
		 */
		public static function left_panel_title() {
			return sprintf( __( 'Available sorting options for %s listing and search results', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );

		}

		/**
		 * Returns description for given sub tab - available fields box.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The box description.
		 */
		public function left_panel_note() {
			return sprintf( __( 'Click on any box below to make it appear in the sorting option dropdown on %s listing and search results.<br />To make a field available here, go to custom fields tab and expand any field from selected fields panel and tick the checkbox saying \'Include this field in sort option\'.', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );
		}

		/**
		 * Output the admin settings cpt sorting left panel content.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 */
		public function left_panel_content() {
			?>
			<div class="inside">

				<div id="gd-form-builder-tab" class="gd-tabs-panel">
					<ul>
						<?php
						$sort_options = self::custom_sort_options( self::$post_type );

						if(!empty($sort_options)){
							foreach ( $sort_options as $key => $val ) {
								$val = stripslashes_deep( $val ); // strip slashes

								$check_html_variable = self::field_exists( $val['htmlvar_name'], self::$post_type );
								$display             = $check_html_variable ? ' style="display:none;"' : '';
								?>

								<li class="gd-cf-tooltip-wrap" <?php echo $display; ?>>
									<a id="gd-<?php echo $val['field_type']; ?>-_-<?php echo $val['htmlvar_name']; ?>"
									   data-field-type-key="<?php echo sanitize_text_field( $val['htmlvar_name'] ); ?>"
									   data-field-type="<?php echo sanitize_text_field( $val['field_type'] ); ?>"
									   class="gd-draggable-form-items  gd-<?php echo sanitize_text_field( $val['field_type'] ); ?> geodir-sort-<?php echo sanitize_text_field( $val['htmlvar_name'] ); ?>"
									   href="javascript:void(0);">
										<?php if ( isset( $val['field_icon'] ) && strpos( $val['field_icon'], 'fa fa-' ) !== false ) {
											echo '<i class="' . sanitize_text_field( $val['field_icon'] ) . '" aria-hidden="true"></i>';
										} elseif ( isset( $val['field_icon'] ) && $val['field_icon'] ) {
											echo '<b style="background-image: url("' . sanitize_text_field( $val['field_icon'] ) . '")"></b>';
										} else {
											echo '<i class="fa fa-cog" aria-hidden="true"></i>';
										} ?>
										<?php echo sanitize_text_field( $val['frontend_title'] ); ?>
										<span class="gd-help-tip gd-help-tip-no-margin dashicons dashicons-editor-help"
										      title="<?php echo sanitize_text_field( $val['description'] ); ?>">
								</span>
									</a>
								</li>

								<?php
							}
						}
						?>
					</ul>
					<div style="clear:both"></div>

				</div>
			</div>
			<?php

		}


		/**
		 * Returns heading for the CPT settings left panel.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The page heading.
		 */
		public static function right_panel_title() {
			return sprintf( __( 'List of fields that will appear in %s listing and search results sorting option dropdown box.', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );
		}

		/**
		 * Returns description for given sub tab - available fields box.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 * @return string The box description.
		 */
		public function right_panel_note() {
			return sprintf( __( 'Click to expand and view field related settings. You may drag and drop to arrange fields order in sorting option dropdown box on %s listing and search results page.', 'geodirectory' ), get_post_type_singular_label( self::$post_type, false, true ) );
		}

		/**
		 * Output the admin cpt settings fields left panel content.
		 *
		 * @since 2.0.0
		 * @package GeoDirectory
		 */
		public function right_panel_content() {
			?>
			<div class="inside">

				<div id="gd-form-builder-tab" class="gd-tabs-panel">
					<div class="field_row_main">
						<ul class="core">
							<?php
							global $wpdb;

							$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " WHERE post_type = %s AND field_type != 'address' ORDER BY sort_order ASC", array( self::$post_type ) ) );

							if ( ! empty( $fields ) ) {
								foreach ( $fields as $field ) {
									//$result_str = $field->id;
									$result_str    = $field;
									$field_type    = $field->field_type;
									$field_ins_upd = 'display';

									$default = false;
									self::output_custom_field_setting_item( $field_type, $result_str, $field_ins_upd, $default );

									//geodir_custom_sort_field_adminhtml( $field_type, $result_str, $field_ins_upd, $default );
								}
							}else{
								_e("Select fields from the left to be able to add new sort options.","geodirectory");
							}
							?>
						</ul>
					</div>
					<div style="clear:both"></div>
				</div>

			</div>
			<?php
		}


		/**
		 * Get sort options based on post type.
		 *
		 * @since 1.0.0
		 * @package GeoDirectory
		 * @global object $wpdb WordPress Database object.
		 *
		 * @param string $post_type The post type.
		 *
		 * @return bool|mixed|void Returns sort options when post type available. Otherwise returns false.
		 */
		public static function custom_sort_options( $post_type = '' ) {

			global $wpdb;

			if ( $post_type != '' ) {

				$all_postypes = geodir_get_posttypes();

				if ( ! in_array( $post_type, $all_postypes ) ) {
					return false;
				}

				$fields = array();

				$fields['random'] = array(
					'post_type'      => $post_type,
					'data_type'      => '',
					'field_type'     => 'random',
					'frontend_title' => 'Random',
					'htmlvar_name'   => 'post_title',
					'field_icon'     => 'fa fa-random',
					'description'    => __( 'Random sort (not recommended for large sites)', 'geodirectory' )
				);

				$fields['datetime'] = array(
					'post_type'      => $post_type,
					'data_type'      => '',
					'field_type'     => 'datetime',
					'frontend_title' => __( 'Add date', 'geodirectory' ),
					'htmlvar_name'   => 'post_date',
					'field_icon'     => 'fa fa-calendar',
					'description'    => __( 'Sort by date added', 'geodirectory' )
				);
				$fields['bigint'] = array(
					'post_type'      => $post_type,
					'data_type'      => '',
					'field_type'     => 'bigint',
					'frontend_title' => __( 'Review', 'geodirectory' ),
					'htmlvar_name'   => 'comment_count',
					'field_icon'     => 'fa fa-commenting-o',
					'description'    => __( 'Sort by the number of reviews', 'geodirectory' )
				);
				$fields['float'] = array(
					'post_type'      => $post_type,
					'data_type'      => '',
					'field_type'     => 'float',
					'frontend_title' => __( 'Rating', 'geodirectory' ),
					'htmlvar_name'   => 'overall_rating',
					'field_icon'     => 'fa fa-star-o',
					'description'    => __( 'Sort by the overall rating value', 'geodirectory' )
				);
				$fields['text'] = array(
					'post_type'      => $post_type,
					'data_type'      => '',
					'field_type'     => 'text',
					'frontend_title' => __( 'Title', 'geodirectory' ),
					'htmlvar_name'   => 'post_title',
					'field_icon'     => 'fa fa-sort-alpha-desc',
					'description'    => __( 'Sort alphabetically by title', 'geodirectory' )
				);

				/**
				 * Hook to add custom sort options.
				 *
				 * @since 1.0.0
				 *
				 * @param array $fields Unmodified sort options array.
				 * @param string $post_type Post type.
				 */
				return $fields = apply_filters( 'geodir_add_custom_sort_options', $fields, $post_type );

			}

			return false;
		}

		/**
		 * Check if the field already exists.
		 *
		 * @param $field
		 *
		 * @return WP_Error
		 */
		public static function field_exists( $htmlvar_name, $post_type ) {
			global $wpdb;

			$check_html_variable = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT htmlvar_name FROM " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " WHERE htmlvar_name = %s AND post_type = %s",
					array( $htmlvar_name, $post_type )
				)
			);

			return $check_html_variable;

		}

		/**
		 * Adds admin html for custom sorting fields.
		 *
		 * @since 1.0.0
		 * @package GeoDirectory
		 * @global object $wpdb WordPress Database object.
		 * @param string $field_type The form field type.
		 * @param object|int $result_str The custom field results object or row id.
		 * @param string $field_ins_upd When set to "submit" displays form.
		 * @param string $field_type_key The key of the custom field.
		 */
		function output_custom_field_setting_item($field_id = '',$field = '',$cf = array())
		{
			//$field_type, $result_str, $field_ins_upd = '', $field_type_key=''
			//$field_id = '',$field = '',$cf = array()


			// if field not provided get it
			if (!is_object($field) && $field_id) {
				global $wpdb;
				$field = $wpdb->get_row($wpdb->prepare("select * from " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " where id= %d", array($field_id)));
			}

			// if field template not provided get it
			if(empty($cf)){
				$cf_arr  = self::custom_sort_options($field->post_type);
				$cf = (isset($cf_arr[$field->field_type])) ? $cf_arr[$field->field_type] : ''; // the field type
			}

			$field = stripslashes_deep( $field );

//			print_r($field);
//			print_r($cf_arr);
			///print_r($cf );echo '####';exit;

			####################


			//$htmlvar_name = isset( $field_type_key ) ? $field_type_key : '';

			$frontend_title = '';
			if ( $frontend_title == '' ) {
				$frontend_title = isset( $field->frontend_title ) ? $field->frontend_title : '';
			}

			if ( $frontend_title == '' ) {
				$frontend_title = isset( $cf['frontend_title'] ) ? $cf['frontend_title'] : '';
			}


			$nonce = wp_create_nonce( 'custom_fields_' . $field->id );

			$field_icon = '<i class="fa fa-cog" aria-hidden="true"></i>';

			if ( isset( $cf['field_icon'] ) && strpos( $cf['field_icon'], 'fa fa-' ) !== false ) {
				$field_icon = '<i class="' . $cf['field_icon'] . '" aria-hidden="true"></i>';
			} elseif ( isset( $cf['field_icon'] ) && $cso['field_icon'] ) {
				$field_icon = '<b style="background-image: url("' . $cf['field_icon'] . '")"></b>';
			}


			$radio_id = ( isset( $field->htmlvar_name ) ) ? $field->htmlvar_name . $field->field_type : rand( 5, 500 );

			//print_r($field);

			/**
			 * Contains custom field html.
			 *
			 * @since 2.0.0
			 */
			include( dirname( __FILE__ ) . '/../views/html-admin-settings-cpt-sorting-setting-item.php' );

		}

		/**
		 * Get the sort order if not set.
		 *
		 * @return int
		 */
		public static function default_sort_order(){
			global $wpdb;
			$last_order = $wpdb->get_var("SELECT MAX(sort_order) as last_order FROM " . GEODIR_CUSTOM_SORT_FIELDS_TABLE);

			return (int)$last_order + 1;
		}

		/**
		 * Sanatize the custom field
		 *
		 * @param array/object $input {
		 *    Attributes of the request field array.
		 *
		 *    @type string $action Ajax Action name. Default "geodir_ajax_action".
		 *    @type string $manage_field_type Field type Default "custom_fields".
		 *    @type string $create_field Create field Default "true".
		 *    @type string $field_ins_upd Field ins upd Default "submit".
		 *    @type string $_wpnonce WP nonce value.
		 *    @type string $listing_type Listing type Example "gd_place".
		 *    @type string $field_type Field type Example "radio".
		 *    @type string $field_id Field id Example "12".
		 *    @type string $data_type Data type Example "VARCHAR".
		 *    @type string $is_active Either "1" or "0". If "0" is used then the field will not be displayed anywhere.
		 *    @type array $show_on_pkg Package list to display this field.
		 *    @type string $admin_title Personal comment, it would not be displayed anywhere except in custom field settings.
		 *    @type string $frontend_title Section title which you wish to display in frontend.
		 *    @type string $frontend_desc Section description which will appear in frontend.
		 *    @type string $htmlvar_name Html variable name. This should be a unique name.
		 *    @type string $clabels Section Title which will appear in backend.
		 *    @type string $default_value The default value (for "link" this will be used as the link text).
		 *    @type string $sort_order The display order of this field in backend. e.g. 5.
		 *    @type string $is_default Either "1" or "0". If "0" is used then the field will be displayed as main form field or additional field.
		 *    @type string $for_admin_use Either "1" or "0". If "0" is used then only site admin can edit this field.
		 *    @type string $is_required Use "1" to set field as required.
		 *    @type string $required_msg Enter text for error message if field required and have not full fill requirement.
		 *    @type string $show_in What locations to show the custom field in.
		 *    @type string $show_as_tab Want to display this as a tab on detail page? If "1" then "Show on detail page?" must be Yes.
		 *    @type string $option_values Option Values should be separated by comma.
		 *    @type string $field_icon Upload icon using media and enter its url path, or enter font awesome class.
		 *    @type string $css_class Enter custom css class for field custom style.
		 *    @type array $extra_fields An array of extra fields to store.
		 *
		 * }
		 */
		private static function sanatize_custom_field($input){

			// if object convert to array
			if(is_object($input)){
				$input = json_decode(json_encode($input), true);
			}

			$field = new stdClass();

			// sanatize
			$field->post_type = isset( $input['post_type'] ) ? sanitize_text_field( $input['post_type'] ) : null;
			$field->field_type = isset( $input['field_type'] ) ? sanitize_text_field( $input['field_type'] ) : null;
			$field->field_id = isset( $input['field_id'] ) ? absint( $input['field_id'] ) : '';
			$field->data_type = isset( $input['data_type'] ) ? sanitize_text_field( $input['data_type'] ) : '';
			$field->htmlvar_name = isset( $input['htmlvar_name'] ) ? str_replace(array('-',' ','"',"'"), array('_','','',''), sanitize_title_with_dashes( $input['htmlvar_name'] ) ) : null;
			$field->frontend_title = isset( $input['frontend_title'] ) ? sanitize_text_field( $input['frontend_title'] ) : null;
			$field->asc = isset( $input['asc'] ) ? absint( $input['asc'] ) : 0;
			$field->asc_title = isset( $input['asc_title'] ) ? sanitize_text_field( $input['asc_title'] ) : $field->frontend_title." ASC";
			$field->desc = isset( $input['desc'] ) ? absint( $input['desc'] ) : 0;
			$field->desc_title = isset( $input['desc_title'] ) ? sanitize_text_field( $input['desc_title'] ) : $field->frontend_title." DESC";
			$field->is_active = isset( $input['is_active'] ) ? absint( $input['is_active'] ) : 0;
			$field->is_default = isset( $input['is_default'] ) && $input['is_default'] ? 1 : 0;
			$field->default_order = isset( $input['default_order'] ) ? sanitize_text_field( $input['default_order'] ) : '';
			$field->sort_order = isset( $input['sort_order'] ) ? absint( $input['sort_order'] ) : self::default_sort_order();

			// Set some default after sanitation
			$field->data_type = self::sanitize_data_type($field);
			if(!$field->htmlvar_name){$field->htmlvar_name =str_replace(array('-',' ','"',"'"), array('_','','',''), sanitize_title_with_dashes( $input['frontend_title'] ) );} // we use original input so the special chars are no converted already

			// setup the default sort
			if( !$field->default_order && $field->is_default ){$field->default_order = sanitize_text_field($input['is_default']);}

			return $field;

		}

		/**
		 * Sanatize data type.
		 *
		 * Sanatize option values.
		 * @param $value
		 *
		 * @return mixed
		 */
		private static function sanitize_data_type( $field ){

			$value = 'VARCHAR';

			if($field->data_type == ''){

				switch ($field->field_type){

					case 'checkbox':
						$value = 'TINYINT';
						break;
					case 'textarea':
					case 'html':
					case 'url':
					case 'file':
						$value = 'TEXT';
						break;
					default:
						$value = 'VARCHAR';
				}

			}else{
				// Strip X if first character, this is added as some servers will flag security rules if a data type is posted via form.
				$value = ltrim($field->data_type, 'X');
			}

			return sanitize_text_field( $value);
		}

		/**
		 * Save the custom field.
		 *
		 * @param array $field
		 *
		 * @return int|string
		 */
		public static function save_custom_field($field = array()){
			global $wpdb, $plugin_prefix;



			$field = self::sanatize_custom_field($field);



			//print_r($field);//exit;

			// Check field exists.
			$exists = self::field_exists($field->htmlvar_name,$field->post_type);


//			if($exists){echo '###exizts';}else{echo '###nonexists';}
//			print_r($_REQUEST);
//			echo 'xxxx';
//			print_r($field);

			//exit;

			if(is_wp_error( $exists ) ){
				return new WP_Error( 'failed', $exists->get_error_message() );
			}elseif( $exists && !$field->field_id ){
				return new WP_Error( 'failed', __( "Duplicate field detected, save failed.", "geodirectory" ) );
			}




			// if this is set as the default blank all the others first just incase.
			if($field->is_default){
				self::blank_default_order($field->post_type);
			}

			$db_data = array(
				'post_type' => $field->post_type,
				'data_type' => $field->data_type,
				'field_type' => $field->field_type,
				'frontend_title' => $field->frontend_title,
				'htmlvar_name' => $field->htmlvar_name,
				'sort_order' => $field->sort_order,
				'is_active' => $field->is_active,
				'is_default' => $field->is_default,
				'default_order' => $field->default_order,
				'sort_asc' => $field->asc,
				'sort_desc' => $field->desc,
				'asc_title' => $field->asc_title,
				'desc_title' => $field->desc_title,
			);

			$db_format = array(
				'%s', // post_type
				'%s', // data_type
				'%s', // field_type
				'%s', // frontend_title
				'%s', // htmlvar_name
				'%d', // sort_order
				'%d', // is_active
				'%d', // is_default
				'%s', // default_order
				'%d', // asc
				'%d', // desc
				'%s', // asc_title
				'%s', // desc_title
			);

			if($exists){

				// Update the field settings.
				$result = $wpdb->update(
					GEODIR_CUSTOM_SORT_FIELDS_TABLE,
					$db_data,
					array('id' => $field->field_id),
					$db_format
				);

				if($result === false){
					return new WP_Error( 'failed', __( "Field update failed.", "geodirectory" ) );
				}

			}else{
				// Insert the field settings.
				$result = $wpdb->insert(
					GEODIR_CUSTOM_SORT_FIELDS_TABLE,
					$db_data,
					$db_format
				);

				if($result === false){
					return new WP_Error( 'failed', __( "Field create failed.", "geodirectory" ) );
				}else{
					$field->field_id = $wpdb->insert_id;
				}

			}

//			$default_order = '';
//			if ($is_default != '') {
//				$default_order = $is_default;
//				$is_default = '1';
//			}
//
//
//			$check_html_variable = $wpdb->get_var(
//				$wpdb->prepare(
//					"select htmlvar_name from " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " where htmlvar_name = %s and post_type = %s and field_type=%s ",
//					array($cehhtmlvar_name, $post_type, $field_type)
//				)
//			);
//
//			if ($is_default == 1) {
//
//				$wpdb->query($wpdb->prepare("update " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " set is_default='0', default_order='' where post_type = %s", array($post_type)));
//
//			}


			/**
			 * Called after all custom sort fields are saved for a post.
			 *
			 * @since 1.0.0
			 * @param int $lastid The post ID.
			 */
			do_action('geodir_after_custom_sort_fields_updated', $field->field_id);


			return $field->field_id;

		}

		/*
		 * Blank all defaults for a post type.
		 */
		public static function blank_default_order($post_type){
			global $wpdb;

			// blank all first
			$wpdb->query($wpdb->prepare("update " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " set is_default='0', default_order='' where post_type = %s", array($post_type)));

		}


		/**
		 * Delete a custom sort field using field id.
		 * @since 1.0.0
		 * @package GeoDirectory
		 * @global object $wpdb WordPress Database object.
		 * @global string $plugin_prefix Geodirectory plugin table prefix.
		 * @param string $field_id The field ID.
		 * @return int|string Returns field id when successful deletion, else returns 0.
		 */
		public static function delete_custom_field($field_id = '')
		{

			global $wpdb;
			if ($field_id != '') {
				$cf = trim($field_id, '_');

				$wpdb->query($wpdb->prepare("delete from " . GEODIR_CUSTOM_SORT_FIELDS_TABLE . " where id= %d ", array($cf)));

				return $field_id;

			} else
				return 0;

		}

		/**
		 * Set custom field order
		 *
		 * @since 1.0.0
		 * @package GeoDirectory
		 * @global object $wpdb WordPress Database object.
		 * @param array $field_ids List of field ids.
		 * @return array|bool Returns field ids when success, else returns false.
		 */
		public function set_field_orders($field_ids = array()){
			global $wpdb;

			$count = 0;
			if (!empty($field_ids)) {
				$post_meta_info = false;
				foreach ( $field_ids as $id ) {
					$post_meta_info = $wpdb->update(
						GEODIR_CUSTOM_SORT_FIELDS_TABLE,
						array('sort_order' => $count),
						array('id' => absint($id)),
						array('%d')
					);
					$count ++;
				}
				if($post_meta_info !== false){
					return true;
				}else{
					return new WP_Error( 'failed', __( "Failed to sort custom fields.", "geodirectory" ) );
				}
			}else{
				return new WP_Error( 'failed', __( "Failed to sort custom fields.", "geodirectory" ) );
			}
		}



	}

endif;

return new GeoDir_Settings_Cpt_Sorting();