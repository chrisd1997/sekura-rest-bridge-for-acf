<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sekura_Users_Controller' ) ) {
	class Sekura_Users_Controller extends Sekura_Controller {
		public function __construct() {
			$this->type      = 'user';
			$this->rest_base = 'users';
			parent::__construct();
		}

		public function get_items( $request ) {
			$this->controller = new WP_REST_Users_Controller();
			return parent::get_items( $request );
		}

		/**
		 * User ACF fields may contain sensitive profile data.
		 * Allow reading own fields, require list_users for others.
		 */
		protected function check_read_permission( $request ) {
			$id = absint( $request->get_param( 'id' ) );

			// Collection endpoint — require list_users.
			if ( ! $id ) {
				if ( current_user_can( 'list_users' ) ) {
					return true;
				}

				return new WP_Error(
					'rest_forbidden',
					__( 'You do not have permission to list user fields.', 'sekura-rest-bridge-for-acf' ),
					array( 'status' => 403 )
				);
			}

			// Users can always read their own ACF fields.
			if ( get_current_user_id() === $id ) {
				return true;
			}

			// Reading another user's fields requires list_users.
			if ( current_user_can( 'list_users' ) ) {
				return true;
			}

			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access this user\'s fields.', 'sekura-rest-bridge-for-acf' ),
				array( 'status' => 403 )
			);
		}

		public function update_item_permissions_check( $request ) {
			$id = absint( $request->get_param( 'id' ) );

			// Users can edit their own ACF fields.
			if ( $id && get_current_user_id() === $id ) {
				$permitted = true;
			} else {
				$permitted = current_user_can( 'edit_users' );
			}

			return apply_filters( 'sekura/item_permissions/update', $permitted, $request, $this->type );
		}
	}
}
