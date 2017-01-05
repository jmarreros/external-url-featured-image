<?php

class Dcms_External_Url_Featured_Image{
	
	public function __construct(){

		if ( is_admin() ){
			add_action('add_meta_boxes', [$this, 'dcms_eufi_add_metaboxes']);
		}

	}

	// Hook Constructor Callback 
	public function dcms_eufi_add_metaboxes(){
		$post_types = ['post']; //Mejor abarcar más tipos y excluir algunos como nelio
		$title		= __('External Featured Image', DCMS_DOMAIN);

		add_meta_box( 'dcmsExternalImage',
						$title, 
						[$this, 'dcms_eufi_show_metabox'],
						$post_types,
						'side',
						'low');
	}

	// add_meta_box Callback
	public function dcms_eufi_show_metabox( $post ){

		$img = get_post_meta($post->ID, 'dcms_eufi_img', true);
		$alt = get_post_meta($post->ID, 'dcms_eufi_alt', true);


		$img = "http://decodecms.app/wp-content/uploads/2017/01/mantenimiento-wordpress.svg";
		$alt = "Este es el texto alt";

		$hasdata = isset($img) && ! empty($img); //if exists and has valid value

		include 'inc-metabox.php';
	}


}
