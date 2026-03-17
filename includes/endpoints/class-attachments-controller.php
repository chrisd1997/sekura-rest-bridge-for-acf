<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Secrbr_Attachments_Controller' ) ) {
	class Secrbr_Attachments_Controller extends Secrbr_Controller {
		public function __construct( $type ) {
			$this->type      = $type->name;
			$this->rest_base = ! empty( $type->rest_base ) ? $type->rest_base : $type->name;
			parent::__construct( $type );
		}

		public function get_items( $request ) {
			$this->controller = new WP_REST_Attachments_Controller( $this->type );
			return parent::get_items( $request );
		}
	}
}
