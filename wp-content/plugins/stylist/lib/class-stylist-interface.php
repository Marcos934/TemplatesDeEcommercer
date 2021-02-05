<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Stylist_Interface Class
 */
class Stylist_Interface {

	/**
	 * Reference to the main class.
	 *
	 * @var Object
	 */
	private $core;

	/**
	 * Do all the required job on core object creation.
	 */
	function __construct( $core ) {

		// Main plugin core class.
		$this->core = $core;


		// No functionality for inframe preview.
		if ( $this->core->is_editing() && ! $this->core->is_inframe() ) {

			// Hidden admin page used as a base for the Stylist editor.
			add_action( 'admin_menu', array( $this, 'hidden_editing_admin_page' ) );
			add_action( 'admin_menu', array( $this, 'admin_appearance_menu' ) );

			// Editing screen:
			add_action( 'admin_enqueue_scripts', array( $this, 'parent_scripts_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_scripts_styles' ) );
			add_filter( 'admin_body_class', array( $this, 'parent_body_class' ) );
			add_action( 'admin_footer', array( $this, 'parent_footer_scripts' ) );

			$this->in_frame();
			$this->in_preview();
		} else {
			// JS and CSS to load when Gutenberg is in editing mode.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_scripts_styles' ) );
		}
	}

	/**
	 * Code to run while rendering iframe.
	 *
	 * @return void
	 */
	private function in_frame() {
		if ( ! $this->core->is_inframe() ) {
			return;
		}

		// Show content in iframe as for regular visitor, no admin user.
		add_action( 'init', array( $this, 'visitor_mode' ), 9996 );
	}

	/**
	 * Code to run while rendering preview page.
	 *
	 * @return void
	 */
	private function in_preview() {
		if ( ! $this->core->is_preview() ) {
			return;
		}

		// Show content on preview page as for regular visitor, no admin user.
		add_action( 'init', array( $this, 'visitor_mode' ), 9999 );
	}

	/**
	 * Create empty hidden admin page in the WP ADMIN to be used
	 * as a base for the plugin editing inerface.
	 */
	public function hidden_editing_admin_page() {
		add_submenu_page(
			null,
			__( 'Stylist Editor', 'stylist' ),
			__( 'Stylist Editor', 'stylist' ),
			'edit_theme_options',
			'stylist-editor',
			array( $this, 'editing_page_parent' )
		);
	}

	/**
	 * Add a hidden link for Stylist.
	 * WP Admin > Appearance > Stylist.
	 */
	public function admin_appearance_menu() {
		add_theme_page(
			'Stylist Editor',
			'Stylist Editor',
			'edit_theme_options',
			'stylist',
			array( $this, 'render_empty_admin_appearance_page' ),
			999
		);
	}

	public function render_empty_admin_appearance_page() {
		$stylist_uri = $this->core->get_editing_screen_uri();

		// Background
		echo '<div class="stlst-bg"></div>';

		// Loader
		echo '';

		// Background and loader CSS
		echo '<style>html,body{display:none;}</style>';

		// Location..
		echo '<script type="text/javascript">window.location = "' . add_query_arg(
			array(
			'href' => stlst_urlencode( get_home_url() . '/' ),
			), $stylist_uri
		) . '";</script>';

		// Die
		exit;
	}

	public function admin_page_scripts_styles( $hook ) {
		// Post pages.
		if ( 'post.php' === $hook ) {
			wp_enqueue_script( 'stylist-admin', plugins_url( '../build/admin.js', __FILE__ ), 'jquery', '1.0', true );
		}

		// Admin css.
		wp_enqueue_style( 'stylist-admin', plugins_url( '../build/admin.css', __FILE__ ) );

	}

	/**
	 * Output Parent editing page that includes all the styling interface
	 * including panels and iframe.
	 */
	public function editing_page_parent() {
		$protocol = is_ssl() ? 'https' : 'http';

		$protocol = $protocol . '://';

		// Fix WooCommerce shop page bug
		if ( class_exists( 'WooCommerce' ) ) {

			$currentID = 0;
			$href      = '';

			// ID
			if ( isset( $_GET['stlst_id'] ) ) { // ID
				$currentID = intval( $_GET['stlst_id'] );
			}

			// href
			if ( isset( $_GET['href'] ) ) { // ID
				$href = $_GET['href'];
			}

			// get shop id
			$shopID = wc_get_page_id( 'shop' );

			// If current id is shop && and href has "page_id"
			if ( $currentID == $shopID && strstr( $href, 'page_id' ) == true && strstr( $href, 'post_type' ) == false ) {

				// Redirect
				wp_safe_redirect( admin_url( 'admin.php?page=stylist-editor&href=' . stlst_urlencode( get_post_type_archive_link( 'product' ) ) . '&stlst_id=' . $shopID ) );

			}
		}

		// Editor Markup
		// include(STLST_PLUGIN_DIR . 'editor.php');
		// exit;
		// NEW APPROACH
		// $frame_url = set_url_scheme( add_query_arg( $previewurl_keys, get_permalink( $previewurl_keys['page_id'] ) ) );

		// Edited page URL get transmitted via href parammeter of the URL.
		$frameLink = '';

		if( isset( $_GET['href'] ) ){
			$frameLink = esc_url( urldecode( $_GET['href'] ) );

			if ( empty( $frameLink ) ) {
				$frameLink = trim( strip_tags( urldecode( $_GET['href'] ) ) );
			}
		}

		// Styling by content type: ?stlst_type=
		if ( isset( $_GET['stlst_type'] ) ) {

			$type = trim( strip_tags( $_GET['stlst_type'] ) );

			$frame = add_query_arg( array(
				'stylist_frame' => 'true',
				'stlst_type' => $type,
			), $frameLink );

		// Styling by post id: ?stlst_id=
		} elseif ( isset( $_GET['stlst_id'] ) ) {

			$id = intval( $_GET['stlst_id'] );

			$frame = add_query_arg( array(
				'stylist_frame' => 'true',
				'stlst_id' => $id,
			), $frameLink );

		} else {

			$frame = add_query_arg( array(
				'stylist_frame' => 'true',
			), $frameLink );

		}

		// if isset out, set stlst_out to frame
		if ( isset( $_GET['stlst_out'] ) ) {

			$frame = add_query_arg( array(
				'stlst_out' => 'true',
			), $frame );

		}

		$protocol = is_ssl() ? 'https' : 'http';

		$frameNew = esc_url( $frame, array( $protocol ) );

		if ( empty( $frameNew ) == true && strstr( $frame, '://' ) == true ) {
			$frameNew = explode( '://', $frame );
			$frameNew = $protocol . '://' . $frameNew[1];
		} elseif ( empty( $frameNew ) == true && strstr( $frame, '://' ) == false ) {
			$frameNew = $protocol . '://' . $frame;
		}

		$frameNew = str_replace( '&#038;', '&amp;', $frameNew );
		$frameNew = str_replace( '&#38;', '&amp;', $frameNew );

		echo '<div id="stylist-preview-area">';
		echo '<div class="stlst-preloader" id="stlst-preloader"><svg aria-hidden="true" role="img" focusable="false" class="dashicon dashicons-update" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M10.2 3.28c3.53 0 6.43 2.61 6.92 6h2.08l-3.5 4-3.5-4h2.32c-.45-1.97-2.21-3.45-4.32-3.45-1.45 0-2.73.71-3.54 1.78L4.95 5.66C6.23 4.2 8.11 3.28 10.2 3.28zm-.4 13.44c-3.52 0-6.43-2.61-6.92-6H.8l3.5-4c1.17 1.33 2.33 2.67 3.5 4H5.48c.45 1.97 2.21 3.45 4.32 3.45 1.45 0 2.73-.71 3.54-1.78l1.71 1.95c-1.28 1.46-3.15 2.38-5.25 2.38z"></path></svg></div>';
		echo '	<iframe id="stylist_iframe" class="stylist_iframe" src="' . esc_url( $frameNew ) . '"></iframe>';
		echo '	<div id="stylist-root"></div>';
		?>
			<div class="stlst-responsive-handle responsive-bottom-handle"></div>
			<div class="stlst-responsive-handle responsive-right-handle"></div>

			<!-- <div id="image_uploader">
				<iframe data-url="<?php echo admin_url( 'media-upload.php?type=image&TB_iframe=true&reauth=1&stlst_uploader=1' ); ?>"></iframe>
			</div> -->
			<div id="image_uploader_background"></div>
		<?php
		echo '</div>';
	}

	/**
	 * Inline scripts to render on the editing WP Admin page.
	 */
	public function parent_footer_scripts() {
		$screen = get_current_screen();

		if ( ! $this->core->is_editing() ) {
			return;
		}
		?>
		<script type="text/javascript">
			//#wpadminbar,
			jQuery('#wpfooter, #adminmenuwrap, #adminmenuback, #adminmenumain, #screen-meta, .update-nag, .updated').remove();
			jQuery('#wpbody-content > *').each(function() {
				var current_el = jQuery(this);
				if ( 'stylist-preview-area' !== current_el[0].getAttribute('id') ) {
					current_el.remove();
				}
			});
		</script>
		<?php
		do_action( 'stylist_editing_screen_footer' );
	}

	/**
	 * Add body classes on the editing WP Admin page.
	 */
	public function parent_body_class( $classes ) {
		// $classes .= ' stlst-stylist stlst-metric-disable';
		$classes .= ' stlst-stylist';
		return $classes;
	}

	/**
	 * Scripts and styles to include on the editing WP Admin page.
	 */
	public function parent_scripts_styles( $hook ) {

		if ( 'admin_page_stylist-editor' === $hook ) {
			// wp_enqueue_script( 'csslint' );
			wp_enqueue_style( 'wp-codemirror' );
			wp_enqueue_script( 'wp-codemirror' );
			wp_add_inline_script(
				'wp-codemirror',
				'window.CodeMirror = wp.CodeMirror;'
			);

			wp_enqueue_media();
			/* if ( isset($_GET['page_id']) ) {
				wp_enqueue_media( array(
					'post' => $_GET['page_id'],
				) );
			} else {
				wp_enqueue_media();
			} */

			wp_enqueue_script(
				'stylist-editing-parent-old',
				plugins_url( '../js/stylist.dev.js', __FILE__ ),
				array(
					'jquery',
				),
				date('U'),
				// $this->core->get_version(),
				true
			);
			wp_enqueue_style(
				'stylist-editing-parent',
				plugins_url( '../build/stylist_parent.css', __FILE__ )
			);

			wp_enqueue_script(
				'wp-i18n',
				plugins_url( '../build/i18n.js', __FILE__ ),
				array(),
				$this->core->get_version()
			);

			wp_enqueue_script(
				'stylist-editing-parent',
				plugins_url( '../build/stylist_parent.js', __FILE__ ),
				array(
					'jquery',
					'wp-i18n',
					'stylist-editing-parent-old',
					'wp-codemirror',
				),
				date('U'),
				// $this->core->get_version(),
				true
			);

			$stylistJsData = array(
				'previewURL' => $this->get_preview_link(),
				'closeURL' => $this->get_close_link(),
			);

			wp_localize_script( 'stylist-editing-parent', 'stylistJsData', $stylistJsData );
		}
	}

	/**
	 * Returns URL to the stling preview page. Used in preview button.
	 * @todo: review method code and improve it.
	 */
	public function get_preview_link() {

		$hrefNew = $this->get_close_link();

		$liveLink = add_query_arg(array('stlst_live_preview' => 'true'),$hrefNew);

		if(isset($_GET['stlst_id'])){
			$liveLink = add_query_arg(array('stlst_id' => intval($_GET['stlst_id'])),esc_url($liveLink));
		}elseif(isset($_GET['stlst_type'])){
			$liveLink = add_query_arg(array('stlst_type' => trim( strip_tags( $_GET['stlst_type'] ) )),esc_url($liveLink));
		}

		// if isset out, set stlst_out to live preview
		if(isset($_GET['stlst_out'])){
			$liveLink = add_query_arg(array('stlst_out' => 'true'),$liveLink);
		}

		$liveLink = str_replace("#038;stlst_live_preview", "&amp;stlst_live_preview", $liveLink);

		return $liveLink;
	}

	/**
	 * Close editor URL used for interface elements.
	 */
	public function get_close_link() {

		$stylist_uri = $this->core->get_editing_screen_uri();

		// Get protocol
		$protocol = is_ssl() ? 'https' : 'http';

		// Href
		$hrefA = '';

		if( isset( $_GET['href'] ) ) {
			$hrefA = $_GET['href'];
		}

		// Update protocol.
		if(strstr($hrefA,'://') == true){
			$hrefNew = explode("://",$hrefA);
			$hrefNew = $protocol.'://'.$hrefNew[1];
		}elseif(strstr($hrefA,'://') == false){
			$hrefNew = $protocol.'://'.$hrefA;
		}

		// Filter
		$hrefNew = esc_url($hrefNew);

		return $hrefNew;
	}

	/**
	 * Logout user before showing content in the iframe(preview area)
	 * in special "Show as a visitor" mode.
	 * ex. stlst_out_mode()
	 *
	 * @return void
	 */
	public function visitor_mode() {
		if ( isset( $_GET['stlst_out'] ) && current_user_can('edit_theme_options') ) {
			wp_set_current_user(-1);
		}
	}
}