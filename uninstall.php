<?php
/**
 * Remove plugin data on uninstall.
 *
 * @package BASFE
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'basfe_options' );
