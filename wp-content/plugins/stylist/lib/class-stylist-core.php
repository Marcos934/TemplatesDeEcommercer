<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}


/**
 * Core Class
 */
class Stylist_Core {

	/**
	 * Was this class ever instantiated?
	 *
	 * @var bool
	 */
	private static $initiated = false;

	/**
	 * Current version of the plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * If current user allowed to use Stylist.
	 *
	 * @var bool
	 */
	private $canuse = false;

	/**
	 * If is in editing mode (editor is active).
	 *
	 * @var bool
	 */
	private $editing = false;

	/**
	 * Is current session initiated from the inframe view?
	 *
	 * @var bool
	 */
	private $inframe = false;

	/**
	 * Is current session initiated from the preview tab?
	 *
	 * @var bool
	 */
	private $preview = false;

	/**
	 * Define the plugin absolute path.
	 *
	 * @var string
	 */
	private $abspath;

	/**
	 * URL to the editing screen.
	 *
	 * @var string
	 */
	private $editing_screen_uri;

	/**
	 * Do all the required job on core object creation.
	 */
	function __construct() {
		// Actions that needs to be lunched only once.
		if ( ! self::$initiated ) {
			$this->set_abspath();
			$this->set_version();
			$this->set_permissions();
			$this->set_editing_state();
			$this->set_slug();
			$this->set_inframe();
			$this->set_preview();
			$this->set_editing_screen_uri();

			$this->require_files();
			$this->load_class_manager();
			$this->load_stylist_interface();
			$this->load_code_manager();

			$this->editing_mode();
			$this->non_editing_mode();

			add_action( 'admin_enqueue_scripts', array( $this, 'non_editing_mode' ) );

			if ( $this->is_inframe() ) {
				// Add special classes to the BODY element.
				// Used to add weight to css selectors. Ex: body.s.t.y.l.e .button
				add_filter( 'body_class', array( $this, 'body_class' ) );
			}

			self::$initiated = true;
		}
	}

	/**
	 * Set $abspath class property value.
	 */
	private function set_abspath() {
		$path_to_current_folder = wp_normalize_path( __DIR__ ) ;
		// Fixes the issue with path on Windows machines.
		$this->abspath = str_replace( '/lib', '', $path_to_current_folder );
	}

	/**
	 * Set $version class property value.
	 */
	private function set_version() {
		/* $version = get_plugin_data( $this->abspath . '/stylist.php', array( 'Version' => 'Version' ), 'plugin' );
		$this->version = $version[ 'Version' ]; */

		$default_headers = array(
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path',
			'Network' => 'Network',
			// Site Wide Only is deprecated in favor of Network.
			'_sitewide' => 'Site Wide Only',
		);

		$plugin_data = get_file_data(  $this->abspath . '/stylist.php', $default_headers, 'plugin' );
		$this->version = $plugin_data[ 'Version' ];
	}

	/**
	 * Determine if current user has enough permissions to use Stylist.
	 */
	private function set_permissions() {
		if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
			$this->canuse = true;
		}
	}

	/**
	 * Set $editing class property value.
	 */
	private function set_editing_state() {
		$editing_page = false;

		if ( isset( $_GET['page'] ) && 'stylist-editor' === esc_attr( $_GET['page'] ) ) {
			$editing_page = true;
		} elseif ( isset( $_GET['stylist_frame'] ) && 'true' === esc_attr( $_GET['stylist_frame'] ) ) {
			$editing_page = true;
		}

		if ( $editing_page && $this->has_access() ) {
			$this->editing = true;
		}
	}

	/**
	 * Plugin slug and translation functionality.
	 */
	private function set_slug() {
		// Get Translation Text Domain.
		load_plugin_textdomain('stylist', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Is current session initiated from the inframe view?
	 */
	private function set_inframe() {
		if ( isset($_GET['stylist_frame']) ) {
			$this->inframe = true;
		}
	}

	/**
	 * Is current session initiated from the preview tab?
	 */
	private function set_preview() {
		if ( isset($_GET['stlst_live_preview']) ) {
			$this->preview = true;
		}
	}

	private function set_editing_screen_uri() {
		$this->editing_screen_uri = admin_url('admin.php?page=stylist-editor');
	}

	/**
	 * Required actions on plugin bootstrap.
	 *
	 * @return void
	 */
	public function require_files() {

		require_once $this->abspath . '/lib/class-stylist-code-manager.php';

		// Load the next files only if user can edit styles.
		// To not overuse resources for regular visitors.
		if ( $this->has_access() ) {
			require_once $this->abspath . '/lib/class-stylist-interface.php';
			require_once $this->abspath . '/lib/class-stylist-class-manager.php';
		}
	}

	/**
	 * Initiate Stylist_Class_Manager.
	 */
	private function load_class_manager() {
		// If is in Editing mode.
		if (  $this->has_access() && ( $this->is_editing() || is_admin() ) ) {
			// CSS block classes manager.
			$class_manager = new Stylist_Class_Manager( $this );
		}
	}

	/**
	 * Initiate Stylist_Interface.
	 */
	private function load_stylist_interface() {
		if ( $this->has_access() ) {
			// Stylist_Interface.
			$interface = new Stylist_Interface( $this );
		}
	}

	/**
	 * Initiate Stylist_Interface.
	 */
	private function load_code_manager() {
		$code_manager = new Stylist_Code_Manager( $this );
	}

	/**
	 * Code to run if it's editing mode (Editor is active).
	 */
	private function editing_mode() {
		if ( $this->has_access() && $this->is_editing() ) {
			if ( $this->is_inframe() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'iframe_scripts_styles' ) );
			}
		}
	}

	/**
	 * Code to run if it's OTHER than editing mode.
	 */
	public function non_editing_mode( $hook = null ) {
		if ( $this->has_access() ) {
			// Non-editing mode: Show 'Style this page' link on public pages
			// or while in the Gutenberg editor.
			if ( ( ! $this->is_editing() && is_admin() === false ) || ( $this->has_access() && 'post.php' === $hook )  ) {
				add_action( 'admin_bar_menu', array( $this, 'edit_in_stylist_link' ), 999);
				add_action( 'wp_head', array( $this, 'admin_bar_helper_css' ) );
				add_action( 'admin_head', array( $this, 'admin_bar_helper_css' ) );
			}
		}
	}

	/**
	 * CSS classes to add to the body.
	 */
	public function body_class( $classes ) {
		$classes[] = 'stlst-stylist';
		$classes = array_merge( $classes, array( 's', 't', 'y', 'l', 'e' ) );

		return $classes;
	}

	// Getters ----------------------------------------------------------

	/**
	 * Get current version of the plugin.
	 * Use like this: Stylist_Core::get_version()
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return true if current user has enough permissions to use Stylist.
	 * Use like this: Stylist_Core::has_access()
	 */
	public function has_access() {
		return $this->canuse;
	}

	/**
	 * If is in editing mode (Editor is active).
	 */
	public function is_editing() {
		return $this->editing;
	}

	/**
	 * Return true if Stylist loaded from inframe area.
	 */
	public function is_inframe() {
		return $this->inframe;
	}

	/**
	 * Return true if Stylist in preview mode.
	 */
	public function is_preview() {
		return $this->preview;
	}

	// Generate Base Editor URL.
	// ex.: stlst_get_uri
	public function get_editing_screen_uri() {
		return $this->editing_screen_uri;
	}

	// Other methods ----------------------------------------------------

	/**
	 * Scripts and styles to output in preview area of the editing page.
	 */
	public function iframe_scripts_styles( $hook ) {
		wp_enqueue_style( 'stylist-iframe', plugins_url( '../build/stylist_iframe.css', __FILE__ ), array(), $this->get_version() );
	}

	// @todo: function needs rewrite.
	public function edit_in_stylist_link( $wp_admin_bar ) {

		$id = null;
		global $wp_query;
		global $wp;
		$stylist_uri = $this->get_editing_screen_uri();

		if (isset($_GET['page_id'])) {
			$id = intval($_GET['page_id']);
		} elseif (isset($_GET['post']) && is_admin() == true) {
			$id = intval($_GET['post']);
		} elseif (isset($wp_query->queried_object) == true) {
			$id = @$wp_query->queried_object->ID;
		}

		// ============================================================

		$go_link = get_permalink( $id );

		if (is_author()) {
			// $status  = __('Author', 'stylist');
			$key     = 'author';
			$id      = $wp_query->query_vars['author'];
			$go_link = get_author_posts_url($id);
		} elseif (is_tag()) {
			// $status  = __('Tag', 'stylist');
			$key     = 'tag';
			$id      = $wp_query->query_vars['tag_id'];
			$go_link = get_tag_link($id);
		} elseif (is_category()) {
			// $status  = __('Category', 'stylist');
			$key     = 'category';
			$id      = $wp_query->query_vars['cat'];
			$go_link = get_category_link($id);
		} elseif (is_404()) {
			// $status  = '404';
			$key     = '404';
			$go_link = esc_url(get_home_url() . '/?p=987654321');
		} elseif (is_archive()) {
			// $status = __('Archive', 'stylist');
			$key    = 'archive';
		} elseif (is_search()) {
			// $status  = __('Search', 'stylist');
			$key     = 'search';
			$go_link = esc_url(get_home_url() . '/?s=' . stlst_getting_last_post_title() . '');
		}

		// Blog
		if (is_front_page() && is_home()) {
			// $status  = __('Home Page', 'stylist');
			$key     = 'home';
			$go_link = esc_url(get_home_url() . '/');
		} elseif (is_front_page() == false && is_home() == true) {
			// $status = __('Page', 'stylist');
		}

		if (class_exists('WooCommerce')) {

			if (is_shop()) {
				$id      = wc_get_page_id('shop');
				// $status  = __('Page', 'stylist');
				$key     = 'shop';
				$go_link = esc_url(get_permalink($id));
			}

			if (is_product_category() || is_product_tag()) {
				$id      = null;
				$go_link = add_query_arg($_SERVER['QUERY_STRING'], '', home_url($wp->request));
			}

		}

		if ( $go_link == '' ) {
			$key     = '';
			$go_link = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		}

		// ============================================================

		$args = array(
			'id' => 'stylist',
			'title' => __('Style This Page', 'stylist'),
			'href' => add_query_arg(array(
				'href' => stlst_urlencode($go_link),
				'stlst_id' => $id
			), $stylist_uri),
			'meta' => array(
				'class' => 'stylist'
			)
		);

		$wp_admin_bar->add_node( $args );

		wp_localize_script( 'stylist-admin', 'stylistEditLink', $args );

		/*

		$args = array();

		// category,author,tag, 404 and archive page support.
		$status  = get_post_type($id);
		$key     = get_post_type($id);
		$go_link = get_permalink($id);

		if (is_author()) {
			$status  = __('Author', 'stylist');
			$key     = 'author';
			$id      = $wp_query->query_vars['author'];
			$go_link = get_author_posts_url($id);
		} elseif (is_tag()) {
			$status  = __('Tag', 'stylist');
			$key     = 'tag';
			$id      = $wp_query->query_vars['tag_id'];
			$go_link = get_tag_link($id);
		} elseif (is_category()) {
			$status  = __('Category', 'stylist');
			$key     = 'category';
			$id      = $wp_query->query_vars['cat'];
			$go_link = get_category_link($id);
		} elseif (is_404()) {
			$status  = '404';
			$key     = '404';
			$go_link = esc_url(get_home_url() . '/?p=987654321');
		} elseif (is_archive()) {
			$status = __('Archive', 'stylist');
			$key    = 'archive';
		} elseif (is_search()) {
			$status  = __('Search', 'stylist');
			$key     = 'search';
			$go_link = esc_url(get_home_url() . '/?s=' . stlst_getting_last_post_title() . '');
		}

		// Blog
		if (is_front_page() && is_home()) {
			$status  = __('Home Page', 'stylist');
			$key     = 'home';
			$go_link = esc_url(get_home_url() . '/');
		} elseif (is_front_page() == false && is_home() == true) {
			$status = __('Page', 'stylist');
		}

		if (class_exists('WooCommerce')) {

			if (is_shop()) {
				$id      = wc_get_page_id('shop');
				$status  = __('Page', 'stylist');
				$key     = 'shop';
				$go_link = esc_url(get_permalink($id));
			}

			if (is_product_category() || is_product_tag()) {
				$id      = null;
				$go_link = add_query_arg($_SERVER['QUERY_STRING'], '', home_url($wp->request));
			}

		}

		if ($go_link == '') {
			$key     = '';
			$go_link = add_query_arg($wp->query_string, '', home_url($wp->request));
		}

		// null if zero.
		if ($id == 0) {
			$id = null;
		}

		// Edit theme
		array_push($args, array(
			'id' => 'stlst-edit-theme',
			'title' => __('Global Customize', 'stylist'),
			'href' => add_query_arg(array(
				'href' => stlst_urlencode($go_link)
			), $stylist_uri),
			'parent' => 'stylist'
		));

		// Edit All similar
		if ($key != 'home' && $key != 'archive' && $key != '' && $key != 'shop') {

			if ($key != '404' && $key != 'search') {
				$s   = '\'s';
				$all = 'All ';
			} else {
				$s   = '';
				$all = '';
			}

			array_push($args, array(
				'id' => 'stlst-edit-all',
				'title' => '' . __("Edit", 'stylist') . ' ' . ucfirst($status) . ' ' . __("Template", 'stylist') . '',
				'href' => add_query_arg(array(
					'href' => stlst_urlencode($go_link),
					'stlst_type' => $key
				), $stylist_uri),
				'parent' => 'stylist',
				'meta' => array(
					'class' => 'first-toolbar-group'
				)
			));

		}

		// Edit it.
		if ($key != 'search' && $key != 'archive' && $key != 'tag' && $key != 'category' && $key != 'author' && $key != '404' && $key != '') {

			if ($key == 'home') {

				array_push($args, array(
					'id' => 'stlst-edit-it',
					'title' => '' . __("Edit", 'stylist') . ' ' . ucfirst($status) . ' only',
					'href' => add_query_arg(array(
						'href' => stlst_urlencode($go_link),
						'stlst_type' => $key
					), $stylist_uri),
					'parent' => 'stylist'
				));
			} else {

				array_push($args, array(
					'id' => 'stlst-edit-it',
					'title' => '' . __("Edit This", 'stylist') . ' ' . ucfirst($status) . '',
					'href' => add_query_arg(array(
						'href' => stlst_urlencode($go_link),
						'stlst_id' => $id
					), $stylist_uri),
					'parent' => 'stylist'
				));

			}


		}

		// Add to Wp Admin Bar
		for ($a = 0; $a < sizeOf($args); $a++) {
			$wp_admin_bar->add_node($args[$a]);
		}

		*/
	}

	public function admin_bar_helper_css() {
		echo '<style>#wp-admin-bar-stylist > .ab-item:before{content: "\f100";top:2px;}#wp-admin-bar-stlst-update .ab-item:before{content: "\f316";top:3px;}</style>';
	}
}
