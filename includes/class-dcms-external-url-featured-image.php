<?php

//Verificar si funciona bien : dcms_eufi_hasdata

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
			// add_action('plugins_loaded', [$this, 'dcms_eufi_plugin_load_textdomain']);
			// add_action('init', [$this, 'dcms_eufi_faking_featured_image']);
			add_action('add_meta_boxes', [$this, 'dcms_eufi_add_metaboxes']);
			add_action('save_post', [$this, 'dcms_eufi_save_data']);
		}
		else
			add_filter('post_thumbnail_html', [$this, 'dcms_eufi_replace_thumbnail'], 10, 5 );

	}


	// ---- Show Thumbnails ----
	// hook filter for thumbnail in front-end
	public function dcms_eufi_replace_thumbnail($html, $post_id, $post_image_id, $size, $attr){

		$data = $this->dcms_eufi_get_meta( $post_id );

		if ( ! $data['hasdata'] ) return $html;

		$img 		= $data['img'];
		$alt 		= ( $data['alt'] ) ? 'alt="'.$data['alt'].'"' : '';
		$classes 	= 'external-img wp-post-image ';
		$classes   .= ( isset($attr['class']) ) ? $attr['class'] : '';
		$style 		= ( isset($attr['style']) ) ? 'style="'.$attr['style'].'"' : '';

		$html = sprintf('<img src="%s" %s class="%s" %s />', 
						$img, $alt, $classes, $style);

		return $html;
	}
	// --------------


	// ---- Add Metaboxes ----
	// hook Constructor Callback 
	public function dcms_eufi_add_metaboxes(){

		$title			= __('External Featured Image', DCMS_EUFI_DOMAIN);
		$post_types 	= $this->dcms_eufi_get_post_types();

		add_meta_box( 'dcmsExternalImage',
						$title, 
						[$this, 'dcms_eufi_show_metabox'],
						$post_types,
						'side',
						'low');

	}

	// add_meta_box Callback, it use nelio data if exists 
	public function dcms_eufi_show_metabox( $post ){

		$data 	 = $this->dcms_eufi_get_meta( $post->ID );

		$img 	 = $data['img'];
		$alt 	 = $data['alt'];
		$hasdata = $data['hasdata'];

		include 'html/inc-metabox.php';	
	}
	// ----------------------


	// ---- Save Data ----
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
	// ----------------------


	// ---- Faking Thumbnail ----
	// build filter for making faking default featured image
	public function dcms_eufi_faking_featured_image(){

		foreach ( $this->dcms_eufi_get_post_types() as $post_type ) {
			add_filter( "get_${post_type}_metadata", 'dcms_eufi_verify_thumbnail', 10, 3 );
		}

	}

	// verify if 
	public function dcms_eufi_verify_thumbnail( $null, $object_id, $meta_key ){

		if ( $meta_key == '_thumbnail_id' ){
			
			if ( $this->dcms_eufi_hasdata( $object_id ) )
				return true;

		}

		return null;
	}

	// // text domain for languages
	// public function dcms_eufi_plugin_load_textdomain() {
		
	// 	$path_languages = basename(dirname(__FILE__)).'/languages/';

	//  	load_plugin_textdomain(DCMS_EUFI_DOMAIN, false, DCMS_EUFI_PATH_LANGUAGE );
	// }



/*
add_action( 'init', 'nelioefi_add_hooks_for_faking_featured_image_if_necessary' );
function nelioefi_add_hooks_for_faking_featured_image_if_necessary(){

	nelioefi_hook_thumbnail_id();

}//end nelioefi_add_hooks_for_faking_featured_image_if_necessary();

function nelioefi_fake_featured_image_if_necessary( $null, $object_id, $meta_key ) {

	$result = null;
	if ( '_thumbnail_id' === $meta_key ) {

		if ( uses_nelioefi( $object_id ) ) {
			$result = true;
		}//end if

	}//end if


	return $result;

}//end nelioefi_fake_featured_image_if_necessary()

function nelioefi_hook_thumbnail_id() {
	foreach ( get_post_types() as $post_type ) {
		add_filter( "get_${post_type}_metadata", 'nelioefi_fake_featured_image_if_necessary', 10, 3 );
	}//end foreach
}//end nelioefi_hook_thumbnail_id()
*/


	// ------------------------------
	//        util functions 
	// ------------------------------

	// get list post types without exclude post types
	private function dcms_eufi_get_post_types(){
		
		$excluded_types	= ['attachment', 'revision', 'nav_menu_item'];
		$post_types 	= array_diff( get_post_types( ['public'   => true], 'names' ), $excluded_types );

		return $post_types;
	}


	// get metadata img and alt and return a data array
	private function dcms_eufi_get_meta( $id ){
		
		$data  = [];

		$img = get_post_meta($id, $this->meta_img , true); 
		$alt = get_post_meta($id, $this->meta_alt , true); 

		if ( ! $img ) {
			$img   = get_post_meta($id, $this->nelio_img, true); 
			$alt   = get_post_meta($id, $this->nelio_alt, true);
		}

		$data['img'] 	 = $img;
		$data['alt'] 	 = $alt;
		$data['hasdata'] = isset($img) && ! empty($img); 

		return $data;
	}

	// validate if a post has data
	private function dcms_eufi_hasdata( $id ){

		return  get_post_meta($id, $this->meta_img , true) ||
				get_post_meta($id, $this->nelio_img, true);

	}


}
