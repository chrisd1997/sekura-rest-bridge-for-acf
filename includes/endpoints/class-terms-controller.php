<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Secrbr_Terms_Controller' ) ) {
	class Secrbr_Terms_Controller extends Secrbr_Controller {
		public function __construct( $type ) {
			$this->type      = $type->name;
			$this->rest_base = ! empty( $type->rest_base ) ? $type->rest_base : $type->name;
			parent::__construct( $type );
		}

		public function get_items( $request ) {
			$this->controller = new WP_REST_Terms_Controller( $this->type );
			return parent::get_items( $request );
		}

		/**
		 * Terms in public taxonomies are generally public.
		 * Only restrict if the taxonomy itself is not public.
		 */
		protected function check_read_permission( $request ) {
			$taxonomy = get_taxonomy( $this->type );
			if ( $taxonomy && $taxonomy->public ) {
				return true;
			}

			if ( current_user_can( $taxonomy->cap->manage_terms ) ) {
				return true;
			}

			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access this resource.', 'secure-rest-bridge-for-acf' ),
				array( 'status' => 403 )
			);
		}
	}
}
