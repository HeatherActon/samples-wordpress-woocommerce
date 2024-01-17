<?php

/**
 * Plugin Name: Sitename Restricted Products
 * Plugin URI:  https://sitename.com
 * Description: Restricts certain products from purchase by certain customers
 * Version:     1.0.0
 * Author:      Author name
 * Author URI:  https://sitename.com
 */

defined( 'ABSPATH' ) or die;

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

define( 'SITEPREFIX_RESTRICTED_PRODUCTS_URL', plugin_dir_url( __FILE__ ) );
define( 'SITEPREFIX_RESTRICTED_PRODUCTS_PATH', plugin_dir_path( __FILE__ ) );

new \Sitename\RestrictedProducts\Loader();