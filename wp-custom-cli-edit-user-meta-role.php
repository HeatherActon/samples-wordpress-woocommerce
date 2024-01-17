<?php
// Custom CLI commands to update user meta or user role via Action Scheduler. Can be for 1 user ID, or from a list of user IDs in a file.

if ( class_exists( 'WPCOM_VIP_CLI_Command' ) ) {

	class EQCLI_Users extends WPCOM_VIP_CLI_Command {

		/**
		 * Lets you update a user meta value
		 *
		 * Usage: wp eqedituser
		 *
		 * @subcommand  update-meta
		 * @synopsis    --key=<meta_key> [--value] [--file] [--user-id] [--dry-run] [--force]
		 */
		public function update_meta( $args, $assoc_args ) {

			$key     = WP_CLI\Utils\get_flag_value( $assoc_args, 'key', '' );
			$value   = WP_CLI\Utils\get_flag_value( $assoc_args, 'value', '' );
			$file    = WP_CLI\Utils\get_flag_value( $assoc_args, 'file', false );
			$user_id = WP_CLI\Utils\get_flag_value( $assoc_args, 'user-id', '' );

			$is_prod = ( defined( 'VIP_GO_APP_ENVIRONMENT' ) && 'production' === VIP_GO_APP_ENVIRONMENT );
			$dry_run = WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', true );
			$force   = WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );

			if ( empty( $key ) ) {
				WP_CLI::error( "key is required" );
			}
			else {

				// Load user IDs from a file.
				if ( $file ) {
					$user_ids = file( __DIR__ . '/files/' . $file );

					if ( $is_prod && ! $force ) {
						WP_CLI::confirm( 'You are about to do this on production. ARE YOU SURE YOU WANT TO DO THIS?' );
					}

					if ( 'false' !== $dry_run ) {

						if ( $user_ids ) :
							WP_CLI::success( sprintf( '%d users would be queued to Action Scheduler to update meta key %s to %s.', count( $user_ids ), $key, $value ) );
						else :
							WP_CLI::success( 'There are no users to update.' );
						endif;
					}
					else {
						if ( $user_ids ) :
							foreach ( $user_ids as $user_id ) {
								$user_id = trim( $user_id );
								$args    = array(
									'data' => array(
										'user_id'    => $user_id,
										'meta_key'   => $key,
										'meta_value' => $value,
									),
								);
								as_schedule_single_action( time(), 'as_equilibria_update_user_meta', $args );
							}
							WP_CLI::success( sprintf( '%d users have been queued to Action Scheduler to update meta key %s to %s.', count( $user_ids ), $key, $value ) );
						else :
							WP_CLI::success( 'There are no users to update.' );
						endif;
					}
				}
				// No file. Get the single user.
				elseif ( $user_id ) {

					if ( $is_prod && ! $force ) {
						WP_CLI::confirm( 'You are about to do this on production. ARE YOU SURE YOU WANT TO DO THIS?' );
					}

					if ( 'false' !== $dry_run ) {
						WP_CLI::success( sprintf( 'User ID %d would be queued to Action Scheduler to update meta key %s to %s.', $user_id, $key, $value ) );
					}
					else {

						$args = array(
							'data' => array(
								'user_id'    => $user_id,
								'meta_key'   => $key,
								'meta_value' => $value,
							),
						);
						as_schedule_single_action( time(), 'as_equilibria_update_user_meta', $args );

						WP_CLI::success( sprintf( 'User ID %d has been queued to Action Scheduler to update meta key %s to %s.', $user_id, $key, $value ) );

					}
				}
				else {
					WP_CLI::success( 'There are no users to update.' );
				}
			}
		}

		/**
		 * Lets you add a role to user(s)
		 *
		 * Usage: wp eqedituser
		 *
		 * @subcommand  add-role
		 * @synopsis    --role=<role-slug> [--file] [--user-id] [--dry-run] [--force]
		 */
		public function add_role( $args, $assoc_args ) {

			$role    = WP_CLI\Utils\get_flag_value( $assoc_args, 'role', '' );
			$file    = WP_CLI\Utils\get_flag_value( $assoc_args, 'file', false );
			$user_id = WP_CLI\Utils\get_flag_value( $assoc_args, 'user-id', '' );

			$is_prod = ( defined( 'VIP_GO_APP_ENVIRONMENT' ) && 'production' === VIP_GO_APP_ENVIRONMENT );
			$dry_run = WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', true );
			$force   = WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );

			if ( empty( $role ) ) {
				WP_CLI::error( "Role slug is required" );
			}
			else {

				// Load user IDs from a file.
				if ( $file ) {
					$user_ids = file( __DIR__ . '/files/' . $file );

					if ( $is_prod && ! $force ) {
						WP_CLI::confirm( 'You are about to do this on production. ARE YOU SURE YOU WANT TO DO THIS?' );
					}

					if ( 'false' !== $dry_run ) {

						if ( $user_ids ) :
							WP_CLI::success( sprintf( '%d users would be queued to Action Scheduler to add role %s.', count( $user_ids ), $role ) );
						else :
							WP_CLI::success( 'There are no users to update.' );
						endif;
					}
					else {
						if ( $user_ids ) :
							foreach ( $user_ids as $user_id ) {
								$user_id = trim( $user_id );
								$args    = array(
									'data' => array(
										'user_id' => $user_id,
										'role'    => $role,
									),
								);
								as_schedule_single_action( time(), 'as_equilibria_add_user_role', $args );
							}
							WP_CLI::success( sprintf( '%d users have been queued to Action Scheduler to add role %s.', count( $user_ids ), $role ) );
						else :
							WP_CLI::success( 'There are no users to update.' );
						endif;
					}
				}
				// No file. Get the single user.
				elseif ( $user_id ) {

					if ( $is_prod && ! $force ) {
						WP_CLI::confirm( 'You are about to do this on production. ARE YOU SURE YOU WANT TO DO THIS?' );
					}

					if ( 'false' !== $dry_run ) {
						WP_CLI::success( sprintf( 'User ID %d would be queued to Action Scheduler to update meta key %s to %s.', $user_id, $key, $value ) );
					}
					else {

						$args = array(
							'data' => array(
								'user_id' => $user_id,
								'role'    => $role,
							),
						);
						as_schedule_single_action( time(), 'as_equilibria_add_user_role', $args );

						WP_CLI::success( sprintf( 'User ID %d has been queued to Action Scheduler to add role %s.', $user_id, $role ) );

					}
				}
				else {
					WP_CLI::success( 'There are no users to update.' );
				}
			}
		}
	}

	WP_CLI::add_command( 'eqedituser', 'EQCLI_Users' );
}
