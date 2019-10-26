<?php
/**
 * The admin pages of the plugin.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Admin
 */

namespace ClassicPress_SEO\Admin;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Runner;
use ClassicPress_SEO\Traits\Hooker;
use ClassicPress_SEO\Admin\Page;
use ClassicPress_SEO\Admin\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Admin_Menu class.
 *
 * @codeCoverageIgnore
 */
class Admin_Menu implements Runner {

	use Hooker;

	/**
	 * Register hooks.
	 */
	public function hooks() {
		$this->action( 'init', 'register_pages' );
		$this->action( 'admin_menu', 'fix_first_submenu', 999 );
		$this->action( 'admin_head', 'icon_css' );
	}

	/**
	 * Register admin pages for plugin.
	 */
	public function register_pages() {

		// Dashboard / Welcome / About.
		new Page(
			'cpseo',
			esc_html__( 'ClassicPress SEO', 'cpseo' ),
			[
				'position'   => 80,
				'capability' => 'manage_options',
				'icon'       => CPSEO_PLUGIN_URL . 'assets/admin/img/feather-white.svg',
				'render'     => Admin_Helper::get_view( 'dashboard' ),
				'classes'    => [ 'cpseo-page' ],
				'assets'     => [
					'styles'  => [ 'cpseo-dashboard' => '' ],
					'scripts' => [ 'cpseo-dashboard' => '' ],
				],
				'is_network' => is_network_admin() && Helper::is_plugin_active_for_network(),
			]
		);

		// Help & Support.
		new Page(
			'cpseo-help',
			esc_html__( 'Help', 'cpseo' ),
			[
				'position'   => 99,
				'parent'     => 'cpseo',
				'capability' => 'level_1',
				'classes'    => [ 'cpseo-page' ],
				'render'     => Admin_Helper::get_view( 'help-manager' ),
				'assets'     => [
					'styles'  => [ 'cpseo-common' => '' ],
					'scripts' => [ 'cpseo-common' => '' ],
				],
			]
		);
	}

	/**
	 * Fix first submenu name.
	 */
	public function fix_first_submenu() {
		global $submenu;
		if ( ! isset( $submenu['cpseo'] ) ) {
			return;
		}

		if ( current_user_can( 'manage_options' ) && 'ClassicPress SEO' === $submenu['cpseo'][0][0] ) {
			$submenu['cpseo'][0][0] = esc_html__( 'Dashboard', 'cpseo' );
		} else {
			unset( $submenu['cpseo'][0] );
		}

		if ( empty( $submenu['cpseo'] ) ) {
			return;
		}

		// Store ID of first_menu item so we can use it in the Admin menu item.
		set_transient( 'cpseo_first_submenu_id', array_values( $submenu['cpseo'] )[0][2] );
	}

	/**
	 * Print icon CSS for admin menu bar.
	 */
	public function icon_css() {
		?>
		<style>
			#wp-admin-bar-cpseo .cpseo-icon {
				display: inline-block;
				top: 6px;
				position: relative;
				padding-right: 10px;
				max-width: 20px;
			}
			#wp-admin-bar-cpseo .cpseo-icon svg {
				fill-rule: evenodd;
				fill: #dedede;
			}
			#wp-admin-bar-cpseo:hover .cpseo-icon svg {
				fill-rule: evenodd;
				fill: #00b9eb;
			}
		</style>
		<?php
	}
}
