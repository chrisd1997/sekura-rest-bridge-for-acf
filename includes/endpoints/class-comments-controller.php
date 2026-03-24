<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sekura_Comments_Controller' ) ) {
	class Sekura_Comments_Controller extends Sekura_Controller {
		public function __construct() {
			$this->type      = 'comment';
			$this->rest_base = 'comments';
			parent::__construct();
		}

		public function get_items( $request ) {
			$this->controller = new WP_REST_Comments_Controller();
			return parent::get_items( $request );
		}

		public function update_item_permissions_check( $request ) {
			$permitted = current_user_can( 'moderate_comments' );
			return apply_filters( 'sekura/item_permissions/update', $permitted, $request, $this->type );
		}

		/**
		 * Only expose ACF data for approved comments, or if the user can moderate.
		 */
		protected function check_read_permission( $request ) {
			$id = absint( $request->get_param( 'id' ) );

			if ( ! $id ) {
				return true;
			}

			$comment = get_comment( $id );
			if ( ! $comment ) {
				return new WP_Error(
					'rest_comment_not_found',
					__( 'Comment not found.', 'sekura-rest-bridge-for-acf' ),
					array( 'status' => 404 )
				);
			}

			if ( '1' === $comment->comment_approved ) {
				return true;
			}

			if ( current_user_can( 'moderate_comments' ) ) {
				return true;
			}

			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access this resource.', 'sekura-rest-bridge-for-acf' ),
				array( 'status' => 403 )
			);
		}
	}
}
