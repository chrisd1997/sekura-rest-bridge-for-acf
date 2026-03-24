<?php
/**
 * Plugin Name: Sekura REST Bridge for ACF
 * Description: Exposes Advanced Custom Fields in the WordPress REST API with proper access control.
 * Author: CW Dekker
 * Author URI: https://cwdekker.com
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: sekura-rest-bridge-for-acf
 *
 * Based on ACF to REST API by Aires Goncalves (GPLv2).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEKURA_VERSION', '1.0.0' );
define( 'SEKURA_PATH', plugin_dir_path( __FILE__ ) );

if ( ! class_exists( 'Sekura' ) ) {

	class Sekura {

		private static $instance = null;

		public static function init() {
			if ( ! self::dependencies_met() ) {
				add_action( 'admin_notices', array( __CLASS__, 'missing_dependencies_notice' ) );
				return;
			}

			self::includes();
			self::hooks();
		}

		private static function dependencies_met() {
			return class_exists( 'WP_REST_Controller' ) && ( class_exists( 'acf' ) || function_exists( 'acf' ) );
		}

		private static function includes() {
			require_once SEKURA_PATH . 'includes/class-acf-api.php';
			require_once SEKURA_PATH . 'includes/class-field-settings.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-controller.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-posts-controller.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-terms-controller.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-comments-controller.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-attachments-controller.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-options-controller.php';
			require_once SEKURA_PATH . 'includes/endpoints/class-users-controller.php';
		}

		private static function hooks() {
			$acf_version = get_option( 'acf_version' );
			$hook = $acf_version >= '5.12' ? 'rest_pre_dispatch' : 'rest_api_init';

			add_action( $hook, array( __CLASS__, 'create_rest_routes' ), 10 );
			Sekura_Field_Settings::hooks();
		}

		public static function create_rest_routes() {
			foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
				if ( 'attachment' === $post_type->name ) {
					$controller = new Sekura_Attachments_Controller( $post_type );
				} else {
					$controller = new Sekura_Posts_Controller( $post_type );
				}
				$controller->register();
			}

			foreach ( get_taxonomies( array( 'show_in_rest' => true ), 'objects' ) as $taxonomy ) {
				$controller = new Sekura_Terms_Controller( $taxonomy );
				$controller->register();
			}

			$controller = new Sekura_Comments_Controller();
			$controller->register();

			$controller = new Sekura_Options_Controller();
			$controller->register();

			$controller = new Sekura_Users_Controller();
			$controller->register();
		}

		public static function missing_dependencies_notice() {
			$missing = array();

			if ( ! class_exists( 'WP_REST_Controller' ) ) {
				$missing[] = 'WordPress REST API';
			}

			if ( ! class_exists( 'acf' ) ) {
				$missing[] = 'Advanced Custom Fields';
			}

			if ( ! empty( $missing ) ) {
				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						/* translators: %s: comma-separated list of missing plugins */
						esc_html__( 'Sekura REST Bridge for ACF requires the following plugins to be active: %s', 'sekura-rest-bridge-for-acf' ),
						esc_html( implode( ', ', $missing ) )
					)
				);
			}
		}
	}

	add_action( 'plugins_loaded', array( 'Sekura', 'init' ) );
}
