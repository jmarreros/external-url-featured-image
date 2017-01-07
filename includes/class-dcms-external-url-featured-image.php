<?php

// main class external url image
class Dcms_External_Url_Featured_Image{

	private $meta_img 	= '_dcms_eufi_img';
	private $meta_alt 	= '_dcms_eufi_alt';

	private $nelio_img 	= '_nelioefi_url';
	private $nelio_alt 	= '_nelioefi_alt';
	private $nelio_first = '_nelioefi_first_image';
	

	// inicialization
	public function __construct(){

		if ( is_admin() ){
			add_action('add_meta_boxes', [$this, 'dcms_eufi_add_metaboxes']);
			add_action('save_post', [$this, 'dcms_eufi_save_data']);
		}
		else
			add_filter('post_thumbnail_html', [$this, 'dcms_eufi_replace_thumbnail'], 10, 5 );

	}

	// hook filter for thumbnail
	public function dcms_eufi_replace_thumbnail($html, $post_id, $post_image_id, $size, $attr){

		$img = get_post_meta($post_id, $this->meta_img , true); 
		$alt = get_post_meta($post_id, $this->meta_alt , true); 

		if ( ! $img ) {
			$img = get_post_meta($post_id, $this->nelio_img, true); 
			$alt = get_post_meta($post_id, $this->nelio_alt, true);

			if ( ! $img ) return $html;
		}

		$classes 	= 'external-img wp-post-image ';
		$classes   .= ( isset($attr['class']) ) ? $attr['class'] : '';
		$style 		= ( isset($attr['style']) ) ? 'style="'.$attr['style'].'"' : '';
		$alt 		= ( $alt ) ? 'alt="'.$alt.'"' : ''; 

		$html = sprintf('<img src="%s" %s class="%s" %s />', 
						$img, $alt, $classes, $style);

		return $html;
	}

	// hook Constructor Callback 
	public function dcms_eufi_add_metaboxes(){

		$title			= __('External Featured Image', DCMS_DOMAIN);

		$excluded_types	= ['attachment', 'revision', 'nav_menu_item']; // exclude some post types
		$post_types 	= array_diff( get_post_types( ['public'   => true], 'names' ), $excluded );

		add_meta_box( 'dcmsExternalImage',
						$title, 
						[$this, 'dcms_eufi_show_metabox'],
						$post_types,
						'side',
						'low');

	}

	// add_meta_box Callback, it use nelio data if exists 
	public function dcms_eufi_show_metabox( $post ){

		$img = get_post_meta($post->ID, $this->meta_img , true); 
		$alt = get_post_meta($post->ID, $this->meta_alt , true); 

		if ( ! $img ) {
			$img = get_post_meta($post->ID, $this->nelio_img, true); 
			$alt = get_post_meta($post->ID, $this->nelio_alt, true);
		}

		$hasdata = isset($img) && ! empty($img); //if exists and has valid value

		include 'html/inc-metabox.php';
	}

	// save data url and alt, it removes nelio data if exists
	public function dcms_eufi_save_data( $post_id ){
		
		$url = isset($_POST['dcmsefi_url'])?$_POST['dcmsefi_url']:null;
		$alt = isset($_POST['dcmsefi_alt'])?$_POST['dcmsefi_alt']:null;

		if ( $url ){
			update_post_meta($post_id, $this->meta_img, wp_strip_all_tags($url));
			if ( $alt )	update_post_meta($post_id, $this->meta_alt, wp_strip_all_tags($alt));	
		}
		else{
			delete_post_meta($post_id, $this->meta_img);
			delete_post_meta($post_id, $this->meta_alt);
		}

		if ( get_post_meta($post_id, $this->nelio_img, true) ){ // drop nelio data if exists
			delete_post_meta($post_id, $this->nelio_img);
			delete_post_meta($post_id, $this->nelio_alt);
			delete_post_meta($post_id, $this->nelio_first);
		}

	}


}
