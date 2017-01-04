<?php
 /*
 * Plugin Name:       External URL Featured Image
 * Plugin URI:        https://decodecms.com
 * Description:       This plugin allow to use external url images for your posts
 * Version:           1.0.0
 * Author:            Jhon Marreros Guzmán
 * Author URI:        https://decodecms.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dcms-eufi
 * Domain Path:       /languages
 */

//if this file is called directly, abort
if ( ! defined('WPINC') ) die();


//Define constants
define('DCMS_EUFI_PATH_INCLUDE', plugin_dir_path( __FILE__ ).'includes/');
define('DCMS_EUFI_PATH_LANGUAGE', 'external-url-featured-image/languages');
define('DCMS_EUFI_PATH_PLUGIN',	__FILE__);
define('DCMS_DOMAIN', 'dcms-eufi');

require_once DCMS_EUFI_PATH_INCLUDE.'class-dcms-external-url-featured-image.php';

new Dcms_External_Url_Featured_Image();

