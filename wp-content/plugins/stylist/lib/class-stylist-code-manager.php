<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Stylist_Code_Manager {
	/**
	 * Reference to the main class.
	 *
	 * @var object
	 */
	private $core;

	/**
	 * Do all the required job on core object creation.
	 *
	 * $core @param object – Reference to the main class.
	 */
	function __construct( $core ) {
		$this->core = $core;
		$this->enqueue_css();

		add_action('wp_ajax_stlst_ajax_save', array($this, 'ajax_save') );
	}

	private function enqueue_css() {
		// Getting Current font families.
		// add_action('wp_enqueue_scripts',  array( $this, 'load_fonts' ) );

		// CSS for backend.
		if ( $this->core->is_inframe() ) {
			// ↓ not sure why we need it?
			// add_action( 'wp_head', array( $this, 'output_css_backend' ), 9998 );
			// add_action('wp_footer',  array( $this, 'output_editor_styles' ) );
		}

		if ( ! $this->core->is_editing() ) {
			// CSS for regular visitors.
			if ( 'external' === get_option('stlst-output-option') ) {
				// Output generated CSS in file.
				$this->output_css();
			} else {
				// Output generated CSS inline in head.
				add_action( 'wp_head', array( $this, 'get_css' ), 9999 );
			}
		} else {
			// CSS in iframe while in editing mode.
			if ( $this->core->is_inframe() ) {
				add_action( 'wp_footer', array( $this, 'get_css' ), 9999 );
			}
		}
	}

	/* ---------------------------------------------------- */
	/* Get Font Families   									*/
	/* ---------------------------------------------------- */
	public function load_fonts() {
		$css = $this->get_css( true );
		$this->get_font_families($css, null);
	}

	/* ---------------------------------------------------- */
	/* Getting font Families By CSS OUTPUT					*/
	/* ---------------------------------------------------- */
	// Type null = 'wp_enqueue_style'
	// Type import = 'import'
	// Type = wp_enqueue_style OR return @import CSS
	private function get_font_families($css, $type) {

		$protocol = is_ssl() ? 'https' : 'http';

		preg_match_all('/font-family:(.*?);/', $css, $r);

		foreach ($r['1'] as &$k) {
			$k = stlst_font_name($k);
		}

		$importArray = array();

		foreach (array_unique($r['1']) as $family) {

			$id = str_replace("+", "-", strtolower($family));

			$id = str_replace("\\", "", $id);

			if ($id == 'arial' || $id == 'helvetica' || $id == 'georgia' || $id == 'serif' || $id == 'helvetica-neue' || $id == 'times-new-roman' || $id == 'times' || $id == 'sans-serif' || $id == 'arial-black' || $id == 'gadget' || $id == 'impact' || $id == 'charcoal' || $id == 'tahoma' || $id == 'geneva' || $id == 'verdana' || $id == 'inherit') {
				return false;
			}

			if ($id == '' || $id == ' ') {
				return false;
			}

			// Getting fonts from google api.
			if ($type == null) {
				wp_enqueue_style($id, esc_url('' . $protocol . '://fonts.googleapis.com/css?family=' . $family . ':300,300italic,400,400italic,500,500italic,600,600italic,700,700italic'));
			} else {
				array_push($importArray, esc_url('' . $protocol . '://fonts.googleapis.com/css?family=' . $family . ':300,300italic,400,400italic,500,500italic,600,600italic,700,700italic'));
			}
		}

		if ($type != null) {
			return $importArray;
		}
	}

	/* ---------------------------------------------------- */
	/* CSS library for Stylist								*/
	/* ---------------------------------------------------- */
	public function output_css() {
		// New ref URL parameters on every new update.
        $rev = get_option('stlst_current_revision');

        if ($rev == false) {
            $rev = 1;
		}

        // Custom CSS Href
        $href = add_query_arg('revision', $rev, plugins_url('custom-' . $rev . '.css', __FILE__));
        // Add
        wp_enqueue_style('stlst-custom', $href);
	}

	/* ---------------------------------------------------- */
	/* Getting CSS Codes									*/
	/* ---------------------------------------------------- */
	// ex: stlst_get_css
	public function get_css( $return_css_only = false ) {
		global $post, $wp_query;

		$css_code = '';

		// Get current post id.
		$id = null;

		if ( class_exists('WooCommerce') ) {
			if ( is_shop() ) {
				$id = wc_get_page_id('shop');
			}
		} elseif ( isset( $wp_query->queried_object ) ) {
			$id = @$wp_query->queried_object->ID;
		}

		// Get global CSS code.
		$stlst_global_css = get_option('stlst_css');

		if ( ! empty( $stlst_global_css ) ) {
			$css_code .= $stlst_global_css;
		}

		/*
		// ORIGINAL DIVISION BY POST/TYPE/GLOBAL SIMPLIFIED.
		$css_for_type = '';
		$css_for_post = '';

		if ( $id != null ) {
			// Get CSS for the current post type.
			$css_for_type = get_option( 'stlst_' . get_post_type($id) . '_css' );

			if ( ! empty( $css_for_type ) ) {
				$css_code .= $css_for_type;
			}

			// Get CSS for the current post.
			$css_for_post   = get_post_meta( $id, '_stlst_css', true );

			if ( ! empty( $css_for_post ) ) {
				$css_code .= $css_for_post;
			}
		}

		if ( is_author() ) {
			$css_code .= get_option("stlst_author_css");
		} elseif ( is_tag() ) {
			$css_code .= get_option("stlst_tag_css");
		} elseif ( is_category() ) {
			$css_code .= get_option("stlst_category_css");
		} elseif ( is_404() ) {
			$css_code .= get_option("stlst_404_css");
		} elseif ( is_search() ) {
			$css_code .= get_option("stlst_search_css");
		}

		// home.
		if ( is_front_page() && is_home() ) {
			$css_code .= get_option("stlst_home_css");
		}
		*/

		if ( $return_css_only ) {
			return $css_code;
		} else {
			$return = '';

			// process
			// $css_code = stlst_stripslashes( $this->add_prefix_to_css_rules( stlst_hover_focus_match( $css_code ) ) );
			$css_code = stlst_stripslashes( stlst_hover_focus_match( $css_code ) );

			if ( ! $this->core->is_editing() ) {
				// If not in the editing mode:
				// return CSS code with comment and minimized code.
				$return .= "\r\n/*\r\n\tThe following CSS generated by Stylist Plugin.\r\n\thttp://stylistwp.com\r\n*/\r\n";

				$return .= str_replace(array(
					"\n",
					"\r",
					"\t"
				), '', $css_code);
			} else {
				// If editing mode: return actual CSS code without minimization.
				$return .= $css_code;
			}

			$return = '<style id="stylist">' . $return . "\n" . '</style>';
			echo $return;
		}
	}

	/*-------------------------------------------------------*/
	/*	Ajax Save Callback – SAVE button clicked in Stylist	 */
	/*-------------------------------------------------------*/
	public function ajax_save() {
		if ( $this->core->has_access() ) {
			// Revisions.
			$current_revision = get_option('stlst_current_revision', 0);
			// Update revision.
			update_option( 'stlst_current_revision', $current_revision + 1 );

			$css = wp_strip_all_tags( $_POST['stlst_data'] );
			$page_code =  $_POST['post_content'];

			$id   = '';
			$type = '';

			if ( isset( $_POST['stlst_id'] ) ) {
				$id = intval( $_POST['stlst_id'] );
			}

			if ( isset( $_POST['stlst_stype'] ) ) {
				$type = trim( strip_tags( $_POST['stlst_stype'] ) );
				if ( count( explode( '#', $type ) ) == 2 ) {
					$type = explode( '#', $type );
					$type = $type[0];
				}
			}

			if ($id === 'undefined') {
				$id = '';
			}
			if ($type === 'undefined') {
				$type = '';
			}
			if ($css === 'undefined') {
				$css = '';
			}

			/**
			 * Gutenberg Save Custom Classes.
			 * 1. Break page code into chunks divided by class.
			 * 2. Put all the stl-block-XXXXX and styled-XXXXX into array.
			 * 3. Replace temporary stl-block-XXXX classes with styled-XXXX classes.
			 */

			// Break page code into chunks divided by class.
			$page_elements = explode( 'class=\"', $page_code );
			$styled_page_elements = array();

			// Ex.: $styled_page_elements = Array (
			//     [stl-block-7e12ce7] => styled-text-fflr
			//     [stl-block-74d4897] => styled-button-xsvu
			// 	)

			foreach ( $page_elements as $key => $value ) {
				if ( stristr( $value, 'styled-' ) ) {// it was ' styled-' !!!!
					// Save found stl-block-XXXXX class.
					$re_block = '/(stl-block-7[a-z0-9]*7)/';
					preg_match($re_block, $value, $matches_block, PREG_OFFSET_CAPTURE, 0);
					$class_block = false;

					if ( ! empty( $matches_block ) && ! empty( $matches_block[0] ) ) {
						$class_block = $matches_block[0][0];
					}

					if ( $class_block ) {
						// Search for styled-XXXXX class.
						$re_styled = '/(styled-[a-zA-Z0-9-_]*)/';
						preg_match($re_styled, $value, $matches_styled, PREG_OFFSET_CAPTURE, 0);
						$class_styled = $matches_styled[0][0];

						// Save found stl-block-XXXXX > styled-XXXXX classes pair.
						$styled_page_elements[$class_block] = $class_styled;
					}
				}
			}

			/**
			 * Replace temporary stl-block-XXXX classes with custom styled-XXX
			 * classes in the post_content field.
			 */
			if ( count( $styled_page_elements ) && $id ) {
				$post = get_post( intval( $id ) );

				if ( isset( $post->ID ) ) {
					$new_post = array();
					$new_post['post_content'] = $post->post_content;
					foreach ( $styled_page_elements as $block => $styled) {
						$new_post['post_content'] =  str_replace( $block, $styled, $new_post['post_content'] );
					}

					$new_post['ID'] = $post->ID;
					wp_update_post( $new_post );
				}
			}

			// CSS Data.
			if ( ! empty( $css ) ) {
				if ( ! update_option( 'stlst_css', $css ) ) {
					add_option( 'stlst_css', $css );
				}
			} else {
				delete_option('stlst_css');
			}

			/*
			// ORIGINAL DIVISION BY POST/TYPE/GLOBAL SIMPLIFIED.
			if ( '' === $id && '' === $type) {
				// GLOBAL CSS.

				// CSS Data.
				if ( ! empty( $css ) ) {
					if ( ! update_option( 'stlst_css', $css ) ) {
						add_option( 'stlst_css', $css );
					}
				} else {
					delete_option('stlst_css');
				}

			} elseif ( '' === $type ) {
				/**
				 * CSS FOR PARTICULAR POST/PAGE:
				 *
				 * Save CSS generated for particular post/page as custom meta
				 * with key XX_stlst_css.
				 *

				// CSS Data.
				if ( empty($css) == false ) {
					if ( ! update_post_meta( $id, '_stlst_css', $css ) ) {
						add_post_meta( $id, '_stlst_css', $css, true );
					}
				} else {
					delete_post_meta( $id, '_stlst_css' );
				}

			} else {
				// PAGE TYPE CSS.

				// CSS Data
				if ( empty($css) == false ) {
					if ( ! update_option( 'stlst_' . $type . '_css', $css ) ) {
						add_option( 'stlst_' . $type . '_css', $css );
					}
				} else {
					delete_option( 'stlst_' . $type . '_css' );
				}

				// Styles
				if ( empty($css) == false ) {
					if ( ! update_option( 'stlst_' . $type . '_styles', $styles ) ) {
						add_option( 'stlst_' . $type . '_styles', $styles );
					}
				} else {
					delete_option( 'stlst_' . $type . '_styles' );
				}

			}
			*/

			// Get All CSS data as ready-to-use.
			$output = $this->get_export_css();

			// Update custom.css file.
			$this->write_css_file( $output );
		}

		wp_die();
	}

	/*-------------------------------------------------------*/
	/*	Creating an Custom.css file (Static)				 */
	/*-------------------------------------------------------*/
	private function write_css_file( $data ) {
		$uploads   = wp_upload_dir();

		// Revisions counter.
		$rev = get_option('stlst_current_revision', 1);

		$directory   = apply_filters( 'stylist_custom_css_dir', $uploads['basedir'] . '/stylist' );
		$filename  = $directory . '/custom-rev' . $rev . '.css';
		$filename_prev  = $directory . '/custom-rev' . ($rev - 1) . '.css';

		/* if ($rev == false) {
			$rev = 1;
		} */

		// Delete old version for the file if exists.
		if ( file_exists( $directory . $filename_prev ) ) {
			wp_delete_file( $directory . $filename_prev );
		}

		// Something like /wp-content/uploads/sylist/custom-rev7.css:
		$fullpath = $directory . $filename;

		/**
		 * Initialize the WP_Filesystem
		 */
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		if ( wp_mkdir_p( $directory ) && ! $wp_filesystem->put_contents( $fullpath, $data, FS_CHMOD_FILE ) ) {
			// If the directory is not writable, try inline css fallback.
			// msp_update_option( 'custom_inline_style' , $css ); // save css rules as option to print as inline css
		} else {
			// $custom_css_ver = msp_get_option( 'masterslider_custom_css_ver', '1.0' );
			// $custom_css_ver = (float)$custom_css_ver + 0.1;
			// msp_update_option( 'masterslider_custom_css_ver' , $custom_css_ver ); // disable inline css output
			// msp_update_option( 'custom_inline_style' , '' );
		}
	}

	/* ---------------------------------------------------- */
	/* Getting CSS data	for editing mode					*/
	/* ---------------------------------------------------- */
	public function output_css_backend() {
		global $post;

		$css_for_type = '';
		$css_for_post   = '';

		global $wp_query;
		if (isset($wp_query->queried_object)) {
			$id = @$wp_query->queried_object->ID;
		} else {
			$id = null;
		}

		$id_is   = isset($_GET['stlst_id']);
		$type_is = isset($_GET['stlst_type']);

		$return = '';

		$stlst_global_css = get_option("stlst_css");

		if ( $stlst_global_css == 'false' ) {
			$stlst_global_css = false;
		}

		if ( ! empty( $stlst_global_css ) ) {
			$return .= $stlst_global_css;
		}

		/*
		if ( null !== $id ) {
			$css_for_type = get_option( 'stlst_' . get_post_type($id) . "_css");
			$css_for_post   = get_post_meta($id, '_stlst_css', true);
		}

		if ($css_for_type == 'false') {
			$css_for_type = false;
		}

		if ($css_for_post == 'false') {
			$css_for_post = false;
		}

		if ( ! empty( $stlst_global_css ) && ( $id_is == true || $type_is == true ) ) {
			$return .= $stlst_global_css;
		}

		if ( ! empty( $css_for_type ) && $type_is == false) {
			$return .= $css_for_type;
		}

		if ( ! empty( $css_for_post ) &&  $id_is == false ) {
			// Don't output post css if id is set.
			$return .= $css_for_post;
		}

		if ( $type_is == false ) {

			if ( is_author() ) {
				$return .= get_option("stlst_author_css");
			} elseif ( is_tag() ) {
				$return .= get_option("stlst_tag_css");
			} elseif ( is_category() ) {
				$return .= get_option("stlst_category_css");
			} elseif ( is_404() ) {
				$return .= get_option("stlst_404_css");
			} elseif ( is_search() ) {
				$return .= get_option("stlst_search_css");
			}

			// home.
			if ( is_front_page() && is_home() ) {
				$return .= get_option("stlst_home_css");
			}

		}
		*/

		// Output if not  empty.
		if ( $return ) {
			echo '<style id="stylist-backend">' . stlst_stripslashes($return) . '</style>';
		}

	}

	/* ------------------------------------------------------------------- */
	/* Other CSS Codes (All CSS Codes excluding current editing type CSS)  */
	/* ------------------------------------------------------------------- */
	/*
	public function output_editor_styles() {
		// @todo: not working as we never save stlst_styles anymore.

		global $post;

		$css_for_type = '';
		$css_for_post   = '';

		global $wp_query;
		if (isset($wp_query->queried_object)) {
			$id = @$wp_query->queried_object->ID;
		} else {
			$id = null;
		}

		if ( class_exists('WooCommerce') ) {
			if (is_shop()) {
				$id = wc_get_page_id('shop');
			}
		}

		$id_is   = isset($_GET['stlst_id']);
		$type_is = isset($_GET['stlst_type']);

		$return = '<div id="stlst-styles-area">';

		$stlst_global_css = get_option("stlst_styles");

		if ($id != null) {
			$css_for_type = get_option( 'stlst_' . get_post_type($id) . "_styles");
			$css_for_post   = get_post_meta($id, '_stlst_styles', true);
		}

		if ( empty($stlst_global_css) == false ) {

			if ($id_is == false && $type_is == false) {
				$return .= $stlst_global_css;
			}

		}

		if (empty($css_for_type) == false) {

			if ($type_is == true) {
				$return .= $css_for_type;
			}

		}

		if (empty($css_for_post) == false) {

			if ($id_is == true) {
				$return .= $css_for_post;
			}

		}

		if ($type_is == true) {

			$type = trim(strip_tags($_GET['stlst_type']));

			if ($type == 'author') {
				$return .= get_option("stlst_author_styles");
			}

			if ($type == 'tag') {
				$return .= get_option("stlst_tag_styles");
			}

			if ($type == 'category') {
				$return .= get_option("stlst_category_styles");
			}

			if ($type == '404') {
				$return .= get_option("stlst_404_styles");
			}

			if ($type == 'search') {
				$return .= get_option("stlst_search_styles");
			}

			if ($type == 'home') {
				$return .= get_option("stlst_home_styles");
			}


		}

		$return .= '</div>';

		echo stlst_stripslashes($return);
	}
	*/

	/* ---------------------------------------------------- */
	/* Generate All CSS styles as ready-to-use				*/
	/* ---------------------------------------------------- */
	/* $method = 'export' / 'create' (string)				*/
	/* ---------------------------------------------------- */
	private function get_export_css( $method = 'create' ) {

		/*
		// Array
		$allData = array();

		// Getting all from database
		$postmeta_css = $this->get_all_post_options( '_stlst_css', false );
		$option_data  = $this->get_all_options( 'stlst_', false );

		// Push option data to Array
		if ( is_array( $option_data ) ) {
			array_push( $allData, $option_data );
		}

		// Push postmeta data to Array
		if ( is_array( $postmeta_css ) ) {
			array_push( $allData, $postmeta_css );
		}

		// Be sure The data not empty
		if (empty($allData) == false) {

			// Clean array
			$data = array_values($allData);

			// Variables
			$output     = null;
			$table      = array();
			$tableIndex = 0;
			$prefix     = '';

			// Adding WordPress Page, category etc classes to all CSS Selectors.
			foreach ($data as $nodes) {

				foreach ($nodes as $key => $css) {
					// Very basic CSS code check.
					if ( 10 > strlen( $css ) || ! strpos($css, ':') || ! strpos($css, '{') || ! strpos($css, '}') ) {
						break;
					}

					$tableIndex++;

					// If post meta
					if (strstr($key, '._')) {

						$keyArray = explode(".", $key);
						$postID   = $keyArray[0];
						$type     = get_post_type($postID);
						$page_title = get_the_title($postID);
						if ( '' === $page_title ) {
							$page_title = '#' . $postID;
						} else {
							$page_title = '"' . ucfirst( $page_title ) . '"';
						}
						$title    = ucfirst($type) . ': ' . $page_title;

						$page_for_posts = get_option('page_for_posts');

						if ($page_for_posts == $postID) {
							$prefix = '.blog';
						} elseif ($type == 'page') {
							$prefix = '.page-id-' . $postID . '';
						} else {
							$prefix = '.postid-' . $postID . '';
						}

						// not have page-id class in WooCommerce shop page.
						if ( class_exists( 'WooCommerce' ) ) {
							$shopID = wc_get_page_id('shop');
							if ($postID == $shopID) {
								$prefix = '.post-type-archive-product';
							}
						}

					} else {

						if ($key == 'stlst_css') {
							$title  = 'Global Styles';
							$prefix = '';
						} else if ($key == 'stlst_author_css') {
							$title  = 'Author Page';
							$prefix = '.author';
						} else if ($key == 'stlst_category_css') {
							$title  = 'Category Page';
							$prefix = '.category';
						} else if ($key == 'stlst_tag_css') {
							$title  = 'Tag Page';
							$prefix = '.tag';
						} else if ($key == 'stlst_404_css') {
							$title  = '404 Error Page';
							$prefix = '.error404';
						} else if ($key == 'stlst_search_css') {
							$title  = 'Search Page';
							$prefix = '.search';
						} else if ($key == 'stlst_home_css') {
							$title  = 'Home Page';
							$prefix = '.home';
						} else if ( strstr($key, 'stlst_') && strstr($key, '_css') ) {
							$title = str_replace( 'stlst_', "", $key);
							$title = str_replace("_css", "", $title);

							if (strtolower($title) == 'page') {
								$prefix = '.page';
							} else {
								$prefix = '.single-' . strtolower($title) . '';
							}

							$title = $title . " Template";
						}

					}

					if ( ! strstr($key, '_styles') ) {
						$len   = 48 - (strlen($title) + 2);
						$extra = null;

						for ($i = 1; $i < $len; $i++) {
							$extra .= ' ';
						}

						array_push($table, ucfirst($title));
						$output .= $this->add_prefix_to_css_selectors($css, $prefix) . "\r\n\r\n\r\n\r\n";

					}

				}

			}
			// Foreach end.
			*/

		$output = '';
		$table = array();
		$tableIndex = 0;

		$stlst_global_css = get_option("stlst_css");

		if ( ! empty( $stlst_global_css ) ) {
			$output .= $stlst_global_css;
		}

		// Add 'body' in front of every css path to make sure our code override
		// other code with the same css path but rendered latter on the page.
		$output = $this->add_prefix_to_css_selectors( $output, '') . "\r\n\r\n\r\n\r\n";

		// ============================================================


		// Create a table list for CSS codes
		$tableList   = null;
		$plusNumber  = 1;
		$googleFonts = array();

		// Get fonts from CSS output
		if ( $method == 'export' ) {
			$googleFonts = $this->get_font_families( $output, 'import' );
		}

		// If has any Google Font; Add Font familes to first table list.
		if ( count($googleFonts) > 0 ) {
			$tableList  = "    01. Font Families\r\n";
			$plusNumber = 2;
		}

		// Creating a table list.
		foreach ($table as $key => $value) {
			$tableList .= "    " . sprintf("%02d", $key + $plusNumber) . ". " . $value . "\r\n";
		}


		// Google Fonts
		if ( count( $googleFonts ) > 0 && is_array( $googleFonts ) ) {
			$FontsCSS = "/*-----------------------------------------------*/\r\n";
			$FontsCSS .= "/* Font Families                                 */\r\n";
			$FontsCSS .= "/*-----------------------------------------------*/\r\n";

			foreach ( $googleFonts as $fontURL ) {
				$FontsCSS .= "@import url('" . $fontURL . "');\r\n";
			}

			$FontsCSS .= "\r\n\r\n\r\n";
		}


		// All in.
		$allOutPut = "/*\r\n\r\n    This CSS code generated dynamically using StylistWP.\r\n";
		$allOutPut .= "    https://stylistwp.com/\r\n";
		$allOutPut .= "    Please do not edit this file.\r\n";
		$allOutPut .= "    All your changes here will be overwritten!\r\n\r\n\r\n";
		// $allOutPut .= "    T A B L E   O F   C O N T E N T S\r\n";
		$allOutPut .= "    ........................................................................\r\n\r\n";
		// $allOutPut .= $tableList;
		$allOutPut .= "\r\n*/\r\n\r\n\r\n\r\n";

		// Adding Google Fonts.
		if ( count( $googleFonts ) > 0 ) {
			$allOutPut .= $FontsCSS;
		}

		// Adding all CSS codues
		$allOutPut .= $output;

		// Process with some PHP functions and return Output CSS code.
		if ( $method == 'export' ) {
			// return  $this->add_prefix_to_css_rules( stlst_hover_focus_match( trim( $allOutPut ) ) );
			return stlst_hover_focus_match( trim( $allOutPut ) );
		} else {
			// return  $this->add_prefix_to_css_rules( stlst_hover_focus_match( trim( $allOutPut ) ) );
			return stlst_hover_focus_match( trim( $allOutPut ) );
		}
	}

	/* ---------------------------------------------------- */
	/* Adding Prefix To Some CSS Rules						*/
	/* ---------------------------------------------------- */
	// ex. stlst_css_prefix()
	// @todo: delete this function as all the prefixes handled by postCSS in js.
	// Breaks text-trasfrom with false -moz-tranform rules !!!!!
	private function add_prefix_to_css_rules($outputCSS) {

		$outputCSS = preg_replace('@\t-webkit-(.*?):(.*?);@si', "", $outputCSS);

		// Adding automatic prefix to output CSS.
		$CSSPrefix = array(
			"animation-name",
			"animation-fill-mode",
			"animation-iteration-count",
			"animation-delay",
			"animation-duration",
			"filter",
			"box-shadow",
			"box-sizing",
			"transform",
			"transition"
		);


		// CSS rules
		foreach ($CSSPrefix as $prefix) {

			// Webkit and o
			if ($prefix != 'filter' && $prefix != 'transform') {

				$outputCSS = preg_replace('@' . $prefix . ':([^\{]+);@U', "" . $prefix . ":$1;\r	-o-" . $prefix . ":$1;\r	-webkit-" . $prefix . ":$1;", $outputCSS);

			} else { // webkit ms moz and o

				$outputCSS = preg_replace('@' . $prefix . ':([^\{]+);@U', "" . $prefix . ":$1;\r	-o-" . $prefix . ":$1;\r	-moz-" . $prefix . ":$1;\r	-webkit-" . $prefix . ":$1;", $outputCSS);

			}

		}

		return $outputCSS;

	}

	/* ---------------------------------------------------- */
    /* Ading Prefix to CSS selectors for global export		*/
    /* ---------------------------------------------------- */
    private function add_prefix_to_css_selectors( $css, $prefix ) {

        # Wipe all block comments
        $css = preg_replace('!/\*.*?\*/!s', '', $css);

        $parts             = explode('}', $css);
        $mediaQueryStarted = false;

        foreach ($parts as &$part) {
            $part = trim($part); # Wht not trim immediately .. ?

            if (empty($part)) {
                continue;
            } else { # This else is also required

                $partDetails = explode('{', $part);

                if (substr_count($part, "{") == 2) {
                    $mediaQuery        = $partDetails[0] . "{";
                    $partDetails[0]    = $partDetails[1];
                    $mediaQueryStarted = true;
                }

                $subParts = explode(',', $partDetails[0]);

                foreach ($subParts as &$subPart) {
                    if (strstr(trim($subPart), "@") || strstr(trim($subPart), "%")) {
                        continue;
                    } else {

                        // Selector
                        $subPart = trim($subPart);

                        // Array
                        $subPartArray = explode(" ", $subPart);
                        $lov          = strtolower($subPart);

                        $lovMach = str_replace("-", "US7XZX", $lov);
                        $lovMach = str_replace("_", "TN9YTX", $lovMach);

                        preg_match_all("/\bbody\b/i", $lovMach, $bodyAll);
                        preg_match_all("/#body\b/i", $lovMach, $bodySlash);
                        preg_match_all("/\.body\b/i", $lovMach, $bodyDot);

                        preg_match_all("/\bhtml\b/i", $lovMach, $htmlAll);
                        preg_match_all("/#html\b/i", $lovMach, $htmlSlash);
                        preg_match_all("/\.html\b/i", $lovMach, $htmlDot);

                        // Get index of "body" term.
                        if (preg_match("/\bbody\b/i", $lovMach) && count($bodyAll[0]) > (count($bodyDot[0]) + count($bodySlash[0]))) {

                            $i     = 0;
                            $index = 0;
                            foreach ($subPartArray as $term) {
                                $term = trim(strtolower($term));
                                if ($term == 'body' || preg_match("/^body\./i", $term) || preg_match("/^body\#/i", $term) || preg_match("/^body\[/i", $term)) {
                                    $index = $i;
                                    break;
                                }
                                $i++;
                            }

                            // Adding prefix class to Body
                            $subPartArray[$index] = $subPartArray[$index] . $prefix;

                            // Update Selector
                            $subPart = implode(" ", $subPartArray);

                        } else if (preg_match("/\bhtml\b/i", $lovMach) && count($htmlAll[0]) > (count($htmlDot[0]) + count($htmlSlash[0]))) {

                            $i     = 0;
                            $index = 0;
                            foreach ($subPartArray as $term) {
                                $term = trim(strtolower($term));
                                if ($term == 'html' || preg_match("/^html\./i", $term) || preg_match("/^html\#/i", $term) || preg_match("/^html\[/i", $term)) {
                                    $index = $i;
                                    break;
                                }
                                $i++;
                            }

                            // Adding prefix class to Body
                            if (count($subPartArray) <= 1) {
                                if ($subPart != 'html' && preg_match("/^html\./i", $subPart) && preg_match("/^html\#/i", $subPart) && preg_match("/^html\[/i", $subPart)) {
                                    $subPartArray[$index] = $subPartArray[$index] . " body" . $prefix;
                                }
                            } else {
                                $subPartArray[$index] = $subPartArray[$index] . " body" . $prefix;
                            }

                            // Update Selector
                            $subPart = implode(" ", $subPartArray);

                        } else {

                            // Adding prefix class to Body
                            $subPartArray[0] = "body" . $prefix . " " . $subPartArray[0];

                            // Update Selector
                            $subPart = implode(" ", $subPartArray);

                        }

                    }
                }

                if (substr_count($part, "{") == 2) {
                    $part = $mediaQuery . "\n" . implode(', ', $subParts) . "{" . $partDetails[2];
                } elseif (empty($part[0]) && $mediaQueryStarted) {
                    $mediaQueryStarted = false;
                    $part              = implode(', ', $subParts) . "{" . $partDetails[2] . "}\n"; //finish media query
                } else {
                    if (isset($partDetails[1])) {
                        # Sometimes, without this check,
                        # there is an error-notice, we don't need that..
                        $part = implode(', ', $subParts) . "{" . $partDetails[1];
                    }
                }

                unset($partDetails, $mediaQuery, $subParts); # Kill those three..

            }
            unset($part); # Kill this one as well
        }

        // Delete spaces
        $output = preg_replace('/\s+/', ' ', implode("} ", $parts));

        // Delete all other spaces
        $output = str_replace("{ ", "{", $output);
        $output = str_replace(" {", "{", $output);
        $output = str_replace("} ", "}", $output);
        $output = str_replace("; ", ";", $output);

        // Beatifull >
        $output = str_replace("{", "{\n\t", $output);
        $output = str_replace("}", "\n}\n\n", $output);
        $output = str_replace("}\n\n\n", "}\n\n", $output);
        $output = str_replace("){", "){\n", $output);
        $output = str_replace(";", ";\n\t", $output);
        $output = str_replace("\t\n}", "}", $output);
        $output = str_replace("}\n\n}", "\t}\n\n}\n\n", $output);


        # Finish with the whole new prefixed string/file in one line
        return (trim($output));

	}


	/* ---------------------------------------------------- */
	/* Getting All post meta data by prefix					*/
	/* ---------------------------------------------------- */
	// ex. stlst_get_all_post_options
	private function get_all_post_options($prefix = '', $en = false) {

		global $wpdb;
		$ret     = array();
		$options = $wpdb->get_results($wpdb->prepare("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $prefix . '%'), ARRAY_A);

		if (!empty($options)) {
			foreach ($options as $v) {
				if ($en == true) {
					$ret[$v['post_id'] . "." . $v['meta_key']] = stlst_encode(stlst_stripslashes($v['meta_value']));
				} else {
					$ret[$v['post_id'] . "." . $v['meta_key']] = stlst_stripslashes($v['meta_value']);
				}
			}
		}

		return (!empty($ret)) ? $ret : false;

	}

	/* ---------------------------------------------------- */
	/* Getting All plugin options by prefix					*/
	/* ---------------------------------------------------- */
	// ex. stlst_get_all_options()
	function get_all_options($prefix = '', $encode = false) {

		global $wpdb;
		$ret     = array();
		$options = $wpdb->get_results($wpdb->prepare("SELECT option_name,option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $prefix . '%'), ARRAY_A);

		if (!empty($options)) {
			foreach ($options as $v) {
				if (strstr($v['option_name'], 'stlst_theme') == false && strstr($v['option_name'], 'stlst_available_version') == false && strstr($v['option_name'], 'stlst_last_check_version') == false) {
					if ($encode == true) {
						$ret[$v['option_name']] = stlst_encode(stlst_stripslashes($v['option_value']));
					} else {
						$ret[$v['option_name']] = stlst_stripslashes($v['option_value']);
					}
				}
			}
		}

		return (!empty($ret)) ? $ret : false;

	}
}