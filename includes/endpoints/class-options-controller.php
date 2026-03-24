<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sekura_Options_Controller' ) ) {
	class Sekura_Options_Controller extends Sekura_Controller {
		public function __construct() {
			$this->type      = 'option';
			$this->rest_base = 'options';
			parent::__construct();
		}

		public function register_routes() {
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w\-\_]+)/?(?P<field>[\w\-\_]+)?', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
			) );
		}

		public function register() {
			$this->register_routes();
		}

		/**
		 * Options pages can contain sensitive data (API keys, site config).
		 * Always require manage_options capability.
		 */
		protected function check_read_permission( $request ) {
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			return new WP_Error(
				'rest_forbidden',
				__( 'You must be an administrator to access options.', 'sekura-rest-bridge-for-acf' ),
				array( 'status' => 403 )
			);
		}

		public function update_item_permissions_check( $request ) {
			return apply_filters(
				'sekura/item_permissions/update',
				current_user_can( 'manage_options' ),
				$request,
				$this->type
			);
		}
	}
}
