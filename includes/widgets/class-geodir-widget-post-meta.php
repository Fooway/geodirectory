<?php

/**
 * GeoDir_Widget_Detail_Meta class.
 *
 * @since 2.0.0
 * @since 2.0.0.49 Added list_hide and list_hide_secondary options for more flexible designs.
 */
class GeoDir_Widget_Post_Meta extends WP_Super_Duper {


	public $arguments;
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		$options = array(
			'textdomain'    => GEODIRECTORY_TEXTDOMAIN,
			'block-icon'    => 'location-alt',
			'block-category'=> 'common',
			'block-keywords'=> "['geo','geodirectory','geodir']",
			'class_name'    => __CLASS__,
			'base_id'       => 'gd_post_meta', // this us used as the widget id and the shortcode id.
			'name'          => __('GD > Post Meta','geodirectory'), // the name of the widget.
			'widget_ops'    => array(
				'classname'   => 'geodir-post-meta-container', // widget class
				'description' => esc_html__('This shows a post single post meta.','geodirectory'), // widget description
				'customize_selective_refresh' => true,
				'geodirectory' => true,
				'gd_wgt_showhide' => 'show_on',
				'gd_wgt_restrict' => array( 'gd-detail' ), //@todo implement this on all other widgets
			),
			'arguments'     => array(
				'title'  => array(
					'title' => __('Title:', 'geodirectory'),
					'desc' => __('Extra main title if needed.', 'geodirectory'),
					'type' => 'text',
					'placeholder' => __( 'Extra main title if needed.', 'geodirectory' ),
					'default'  => '',
					'desc_tip' => true,
					'advanced' => true
				),
				'id'  => array(
					'title' => __('Post ID:', 'geodirectory'),
					'desc' => __('Leave blank to use current post id.', 'geodirectory'),
					'type' => 'number',
					'placeholder' => 'Leave blank to use current post id.',
					'desc_tip' => true,
					'default'  => '',
					'advanced' => false
				),
				'key'  => array(
					'title' => __('Key:', 'geodirectory'),
					'desc' => __('This is the custom field key.', 'geodirectory'),
					'type' => 'select',
					'placeholder' => 'website',
					'options'   => $this->get_custom_field_keys(),
					'desc_tip' => true,
					'default'  => '',
					'advanced' => false
				),
				'show'  => array(
					'title' => __('Show:', 'geodirectory'),
					'desc' => __('What part of the post meta to show.', 'geodirectory'),
					'type' => 'select',
					'options'   =>  array(
						"" => __('icon + label + value', 'geodirectory'),
						"icon-value" => __('icon + value', 'geodirectory'),
						"label-value" => __('label + value', 'geodirectory'),
						"label" => __('label', 'geodirectory'),
						"value" => __('value', 'geodirectory'),
						"value-strip" => __('value (strip_tags)', 'geodirectory'),
					),
					'desc_tip' => true,
					'advanced' => false
				),
				'alignment'  => array(
					'title' => __('Alignment:', 'geodirectory'),
					'desc' => __('How the item should be positioned on the page.', 'geodirectory'),
					'type' => 'select',
					'options'   =>  array(
						"" => __('None', 'geodirectory'),
						"block" => __('Block', 'geodirectory'),
						"left" => __('Left', 'geodirectory'),
						"center" => __('Center', 'geodirectory'),
						"right" => __('Right', 'geodirectory'),
					),
					'desc_tip' => true,
					'advanced' => false
				),
				'text_alignment'  => array(
					'title' => __('Text Align:', 'geodirectory'),
					'desc' => __('How the text should be aligned.', 'geodirectory'),
					'type' => 'select',
					'options'   =>  array(
						"" => __('None', 'geodirectory'),
						"left" => __('Left', 'geodirectory'),
						"center" => __('Center', 'geodirectory'),
						"right" => __('Right', 'geodirectory'),
					),
					'desc_tip' => true,
					'advanced' => false
				),
				'list_hide'  => array(
					'title' => __('Hide item on view:', 'geodirectory'),
					'desc' => __('You can set at what view the item will become hidden.', 'geodirectory'),
					'type' => 'select',
					'options'   =>  array(
						"" => __('None', 'geodirectory'),
						"2" => __('Grid view 2', 'geodirectory'),
						"3" => __('Grid view 3', 'geodirectory'),
						"4" => __('Grid view 4', 'geodirectory'),
						"5" => __('Grid view 5', 'geodirectory'),
					),
					'desc_tip' => true,
					'advanced' => true
				),
				'list_hide_secondary'  => array(
					'title' => __('Hide secondary info on view', 'geodirectory'),
					'desc' => __('You can set at what view the secondary info such as label will become hidden.', 'geodirectory'),
					'type' => 'select',
					'options'   =>  array(
						"" => __('None', 'geodirectory'),
						"2" => __('Grid view 2', 'geodirectory'),
						"3" => __('Grid view 3', 'geodirectory'),
						"4" => __('Grid view 4', 'geodirectory'),
						"5" => __('Grid view 5', 'geodirectory'),
					),
					'desc_tip' => true,
					'advanced' => true
				),
				'css_class'  => array(
					'type' => 'text',
					'title' => __('Extra class:', 'geodirectory'),
					'desc' => __('Give the wrapper an extra class so you can style things as you want.', 'geodirectory'),
					'placeholder' => '',
					'default' => '',
					'desc_tip' => true,
					'advanced' => true,
				),
			)
		);


		parent::__construct( $options );


	}

	//gd_wgt_showhide

	/**
	 * The Super block output function.
	 *
	 * @param array $args
	 * @param array $widget_args
	 * @param string $content
	 *
	 * @return mixed|string|void
	 */
	public function output($args = array(), $widget_args = array(),$content = ''){

		/**
		 * @var int    $ID Optional. The current post ID if empty.
		 * @var string $key The meta key : email
		 * @var string $show Optional. What to show, 'title','value' or 'all'. Default 'all'.
		 * @var string $align left,right,center or blank.. Default ''
		 * @var string $location The show in what location key. Default 'none'
		 */
		extract($args, EXTR_SKIP);

		global $post,$gd_post;

		$original_id = isset($args['id']) ? $args['id'] : '';
		$args['location'] = !empty($args['location']) ? $args['location'] : 'none';
		$output = '';
		$args = shortcode_atts( array(
			'id'    => isset($gd_post->ID) ? $gd_post->ID : 0,
			'key'    => '', // the meta key : email
			'show'    => '', // title,value (default blank, all)
			'list_hide'    => '',
			'list_hide_secondary'    => '',
			'css_class' => '',
			'alignment'    => '', // left,right,center
			'text_alignment'    => '', // left,right,center
			'location'  => 'none',
		), $args, 'gd_post_meta' );

		if(empty($args['id'])){
			$args['id'] =  isset($gd_post->ID) ? $gd_post->ID : 0;
		}
		
		$post_type = !$original_id && isset($post->post_type) ? $post->post_type : get_post_type($args['id']);


		// print_r($args);
		// error checks
		$errors = array();
		if(empty($args['key'])){$errors[] = __('key is missing','geodirectory');}
//		if(empty($args['id'])){$errors[] = __('id is missing','geodirectory');}
		if(empty($post_type)){$errors[] = __('invalid post type','geodirectory');}

		if(!empty($errors)){
			$output .= implode(", ",$errors);
		}

		// check if its demo content
		if($post_type == 'page' && !empty($args['id']) && geodir_is_block_demo()){
			$post_type = 'gd_place';
		}

		if ( geodir_is_gd_post_type( $post_type ) ) {
			$package_id = geodir_get_post_package_id( $args['id'], $post_type );
			$fields = geodir_post_custom_fields( $package_id,  'all', $post_type , 'none' );

			$fields = $fields + self::get_standard_fields();

//			echo '###';
//			print_r( $fields );

			if(!empty($fields)){
				$field = array();
				foreach($fields as $field_info){
					if($args['key']==$field_info['htmlvar_name']){
						$field = $field_info;
					}
				}
				if(!empty($field)){
					$field = stripslashes_deep( $field );

					// apply standard css
					if(!empty($args['css_class'])){
						$field['css_class'] .=" ".$args['css_class']." ";
					}

					// set text alignment class
					if($args['text_alignment']=='left'){$field['css_class'] .= " geodir-text-alignleft ";}
					if($args['text_alignment']=='center'){$field['css_class'] .= " geodir-text-aligncenter ";}
					if($args['text_alignment']=='right'){$field['css_class'] .= " geodir-text-alignright ";}

					// set alignment class
					if($args['alignment']=='left'){$field['css_class'] .= " geodir-alignleft ";}
					if($args['alignment']=='center'){$field['css_class'] .= " geodir-aligncenter ";}
					if($args['alignment']=='right'){$field['css_class'] .= " geodir-alignright ";}
					if($args['alignment']=='block'){$field['css_class'] .= " gd-d-block gd-clear-both ";}

					// set list_hide class
					if($args['list_hide']=='2'){$field['css_class'] .= " gd-lv-2 ";}
					if($args['list_hide']=='3'){$field['css_class'] .= " gd-lv-3 ";}
					if($args['list_hide']=='4'){$field['css_class'] .= " gd-lv-4 ";}
					if($args['list_hide']=='5'){$field['css_class'] .= " gd-lv-5 ";}

					// set list_hide_secondary class
					if($args['list_hide_secondary']=='2'){$field['css_class'] .= " gd-lv-s-2 ";}
					if($args['list_hide_secondary']=='3'){$field['css_class'] .= " gd-lv-s-3 ";}
					if($args['list_hide_secondary']=='4'){$field['css_class'] .= " gd-lv-s-4 ";}
					if($args['list_hide_secondary']=='5'){$field['css_class'] .= " gd-lv-s-5 ";}

					$output = apply_filters("geodir_custom_field_output_{$field['type']}",'',$args['location'],$field,$args['id'],$args['show']);

					if($field['name']=='post_content'){
						//$output = wp_strip_all_tags($output);
					}

				}else{
					//$output = __('Key does not exist','geodirectory');
				}
			}else{

			}
		}

		return $output;

	}

	/**
	 * Gets an array of custom field keys.
	 *
	 * @return array
	 */
	public function get_custom_field_keys(){
		$fields = geodir_post_custom_fields('', 'all', 'all','none');
		$keys = array();
		$keys[] = __('Select Key','geodirectory');
		if(!empty($fields)){
			foreach($fields as $field){
				$keys[$field['htmlvar_name']] = $field['htmlvar_name'];
			}
		}



		// add some general types:
		$keys['post_date'] = 'post_date';
		$keys['post_modified'] = 'post_modified';
		$keys['post_author'] = 'post_author';

//		print_r($keys);exit;
		return $keys;

	}


	/**
	 * Get some standard post fields info.
	 *
	 * @return array
	 */
	public function get_standard_fields(){
		$fields = array();


		$fields['post_date'] = array(
			'name'          =>  'post_modified',
			'htmlvar_name'  =>  'post_modified',
			'frontend_title'              =>  __('Modified','geodirectory'),
			'type'              =>  'datepicker',
			'field_icon'              =>  'fas fa-calendar-alt',
			'field_type_key'              =>  '',
			'css_class'              =>  '',
			'extra_fields'              =>  '',
		);

		$fields['post_modified'] = array(
			'name'          =>  'post_date',
			'htmlvar_name'  =>  'post_date',
			'frontend_title'              =>  __('Published','geodirectory'),
			'type'              =>  'datepicker',
			'field_icon'              =>  'fas fa-calendar-alt',
			'field_type_key'              =>  '',
			'css_class'              =>  '',
			'extra_fields'              =>  '',
		);

		$fields['post_date_gmt'] = array(
			'name'          =>  'post_author',
			'htmlvar_name'  =>  'post_author',
			'frontend_title'              =>  __('Author','geodirectory'),
			'type'              =>  'author',
			'field_icon'              =>  'fas fa-user',
			'field_type_key'              =>  '',
			'css_class'              =>  '',
			'extra_fields'              =>  '',
		);


		/**
		 * Filter the post meta standard fields info.
		 *
		 * @since 2.0.0.49
		 */
		return apply_filters('geodir_post_meta_standard_fields',$fields);
	}
	
}

