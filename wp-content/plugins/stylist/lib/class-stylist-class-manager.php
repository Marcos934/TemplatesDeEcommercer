<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Stylist_Class_Manager {

	/**
	 * Was this class ever instantiated?
	 *
	 * @var bool
	 */
	// private $classes_added = false;

	/**
	 * CSS Class Name prefix.
	 * Our temoporary classes have the next formula: stl-block-7xxxxx7.
	 *
	 * @var string
	 */
	private $temp_class_name_prefix = 'stl-block-7';

	/**
	 * CSS Class Name prefix.
	 * Our permanent classes have the next formula: styled-XXXXXX.
	 *
	 * @var string
	 */
	private $styled_class_name_prefix = 'styled-';

	/**
	 * CSS Class Name suffix.
	 *
	 * @var string
	 */
	private $temp_class_name_suffix = '7';

	/**
	 * Reference to the main class.
	 *
	 * @var object
	 */
	private $core;

	/**
	 * List of posts that has temporary classes assigned to the each block.
	 *
	 * @var array
	 */
	private $posts_with_temp_classes = array();

	private $checked_cleanup_queue = false;

	/**
	 * Do all the required job on core object creation.
	 *
	 * $core @param object – Reference to the main class.
	 */
	function __construct( $core ) {
		$this->core = $core;
		$this->set_posts_with_temp_classes();

		if ( $this->core->is_editing() && $this->core->is_inframe() ) {
			add_filter( 'wp', array( $this, 'add_temporary_classes' ) );
		} elseif ( ! $this->core->is_editing() && is_admin() && function_exists( 'wp_doing_ajax' ) && ! wp_doing_ajax() ) {
			// Proceed only if there are posts to clean and we are in WP Admin
			// to not slow-down front-end.
			if ( count( $this->get_posts_with_temp_classes() ) ) {
				// add_filter( 'wp', array( $this, 'remove_temporary_classes_on_page_load' ) ); // Regular page.
				// add_filter( 'admin_init', array( $this, 'remove_temporary_classes_on_editor_load' ) ); // Editor.
				add_filter( 'admin_init', array( $this, 'check_cleanup_queue') );
			}
		}
	}

	public function set_posts_with_temp_classes() {
		$posts_with_temp_classes =  get_option( 'stylist_posts_with_temp_classes', array() );

		if ( ! $posts_with_temp_classes ) {
			$posts_with_temp_classes = array();
		}

		$this->posts_with_temp_classes = $posts_with_temp_classes;
	}

	public function get_posts_with_temp_classes() {
		return $this->posts_with_temp_classes;
	}

	public function post_has_temp_classes( $post_id ) {
		if ( in_array( $post_id, $this->posts_with_temp_classes ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function add_post_with_temp_classes( $post_id ) {
		if ( ! in_array( $post_id, $this->posts_with_temp_classes ) ) {
			$this->posts_with_temp_classes[] = $post_id;
			update_option( 'stylist_posts_with_temp_classes', $this->posts_with_temp_classes );
		}
	}

	public function remove_post_with_temp_classes( $post_id ) {
		// unset( $this->posts_with_temp_classes[$post_id] );
		if ( ( $key = array_search( $post_id, $this->posts_with_temp_classes ) ) !== false) {
			unset( $this->posts_with_temp_classes[$key] );
			// Reorder array keys after element deletion.
			$this->posts_with_temp_classes = array_values( $this->posts_with_temp_classes );
		}

		update_option( 'stylist_posts_with_temp_classes', $this->posts_with_temp_classes );
	}


	/**
	 * Adds temporary CSS classes to every Gutenberg block on the page.
	 */
	public function add_temporary_classes() {
		global $post;

		// Run the job only if the post isn't marked in the database
		// as a post with temporary classe already added.
		// if ( ! $this->post_has_temp_classes( $post->ID ) ) {
			$new_post = array();
			$new_post['post_content'] = $this->filter_wp_content( $post->post_content );
			$new_post['ID'] = $post->ID;

			wp_update_post( $new_post );
			$post->post_content = $new_post['post_content'];
			$this->add_post_with_temp_classes( $post->ID );
		// }
	}


	public function remove_temporary_classes_on_page_load( $post = false ) {
		if ( ! $post || ! isset( $post->ID ) ) {
			global $post;
		}

		// Not valid post object or can't find any temp class in the content.
		if ( ! $post || false === strpos( $post->post_content, $this->temp_class_name_prefix ) ) {
			// Looks like we have mistake in $posts_with_temp_classes. Fix it.
			if ( $post && $this->post_has_temp_classes( $post->ID ) ) {
				$this->remove_post_with_temp_classes( $post->ID );
			}
			// Exit early.
			return;
		}

		$new_post = array();
		// Remove custom added classes.
		$new_post['post_content'] = $this->remove_temporary_classes( $post->post_content );
		$new_post['ID'] = $post->ID;

		wp_update_post( $new_post );
		$post->post_content = $new_post['post_content'];

		// Remove this post from the list of posts with temp. classes.
		$this->remove_post_with_temp_classes( $post->ID );
		$this->check_cleanup_queue();
	}

	public function check_cleanup_queue() {
		// Do max one extra post cleanup per page loading.
		if ( $this->checked_cleanup_queue ) {
			return;
		}

		// Check if there are any other posts in a queue for clean-up.
		$cleanup_queue = $this->get_posts_with_temp_classes();

		if ( empty( $cleanup_queue ) ) {
			return;
		}

		$post = get_post( intval( $cleanup_queue[0] ) );
		if ( isset( $post->ID ) ) {
			$this->checked_cleanup_queue = true;
			$this->remove_temporary_classes_on_page_load( $post );
		}
	}

	public function remove_temporary_classes_on_editor_load() {
		if ( ! isset( $_GET['post'] ) ) {
			return;
		}
		$post_id = intval( esc_attr( $_GET['post'] ) );

		if ( $post_id && $this->post_has_temp_classes( $post_id ) ) {
			$post = get_post( $post_id );
			if ( isset( $post->ID ) ) {
				$this->remove_temporary_classes_on_page_load( $post );
			}
		}
	}

	public function remove_temporary_classes( $content ) {
		// Remove custom added classes.
		// https://regex101.com/r/4EMolF/1
		$re = '/(\s*' . $this->temp_class_name_prefix . '[a-z0-9]+' . $this->temp_class_name_suffix  . '\s*)/';
		$subst = ' ';
		$content = preg_replace( $re, $subst, $content );

		// Remove unwanted spaces in classes.
		// https://regex101.com/r/WDn1TC/2
		// - remove spaces form the beggining
		$re = '/class="([\s]*)([\w\d-_\s]+)"/';
		$subst = 'class="$2"';
		$content = preg_replace($re, $subst, $content);

		// https://regex101.com/r/WDn1TC/3
		// - remove spaces form the end
		$re = '/class="([\w\d-_\s]+[\w\d-_])([\s]*)"/';
		$subst = 'class="$1"';
		$content = preg_replace($re, $subst, $content);

		// Delete empty classes.
		$content = str_replace ('class=" "', '', $content );
		$content = str_replace ('{"className":" "}', '', $content );
		$content = str_replace (',"className":" "', '', $content );
		$content = str_replace ('"className":" ",', '', $content );

		return $content;
	}

	public function filter_wp_content( $wp_content ) {
		// Before processing remove empty className attribute definitions.
		$wp_content = $this->remove_empty_class_attr( $wp_content );

		$wp_blocks = explode( '<!-- wp:', $wp_content );


		foreach ( $wp_blocks as $key => $wp_block ) {
			$not_block_content = '';

			// Test if the block has a valid structure:
			// - closing <!-- /wp:xxxxxx --> comment
			// - self-closing comment /--> part
			// - columns block like: columns {"layout":"column-1"} --><div class="wp-block-columns ....
			if ( false !== strpos( $wp_block, '<!-- /wp:' ) ||
				 false !== strpos( $wp_block, '/-->' ) ||
				 false !== strpos( $wp_block, 'wp-block-column' ) ||
				 false !== strpos( $wp_block, 'class="wp-block-' ) ) {

				$wp_block = '<!-- wp:' . $wp_block;

				$closing_tag_pos = strpos( $wp_block, '<!-- /wp:' );
				$openclose_tag_pos = strpos( $wp_block, '/-->' );

				// If block code has a single (open/close) tag only:
				// try to separate extra content that isn't part of the block code.
				if ( false === $closing_tag_pos &&
				 	 false !== $openclose_tag_pos ) {

					$not_block_content = substr(
						$wp_block,
						intval( $openclose_tag_pos ) + strlen( '/-->' ),
						strlen( $wp_block ) - 1
					);

					$wp_block = str_replace( $not_block_content, '', $wp_block );
				}

				$new_class = $this->temp_class_name_prefix . substr( md5( rand() ), 0, 5 ) . $this->temp_class_name_suffix;

				/**
				 * Find className attribute to add custom class.
				 */
				$wp_block_with_class = $this->add_class( $wp_block, $new_class );

				if ( ! $wp_block_with_class ) {
					/**
					 * Attribute className not found.
					 */
					// Find position of first --> part.
					$block_tag_pos_end = strpos( $wp_block, '-->' );
					$block_tag_pos_end = intval( $block_tag_pos_end ) + strlen( '-->' );

					// Divide block string into two parts:
					// $wp_block_opening_tag: opening block tag <-- wp:... -->
					// $wp_block_content: what is left after <-- wp:... -->
					$wp_block_opening_tag = substr ( $wp_block , 0, $block_tag_pos_end);
					$wp_block_content = substr ( $wp_block , $block_tag_pos_end, strlen($wp_block)-1 );

					$wp_block_opening_tag = str_replace ('{}', '', $wp_block_opening_tag );

					$wp_block_opening_tag = $this->create_class_attr_gutenberg( $wp_block_opening_tag, $new_class );
					// Before ex: $wp_block_opening_tag = <!-- wp:shortcode -->
					// After ex: $wp_block_opening_tag = <!-- wp:shortcode {"className":"stl-block-7375aa7"} -->

					$wp_block_content = $this->create_class_attr_dom( $wp_block_content, $new_class );

					// Put it back together.
					$wp_block_with_class = $wp_block_opening_tag . $wp_block_content;
				}
				// Replace the block code with our changed.
				$wp_blocks[ $key ] = $wp_block_with_class . $not_block_content;
			}
		}
		$wp_content = implode( '', $wp_blocks );
		return $wp_content;
	}


	public function remove_empty_class_attr( $wp_content ) {
		// Before processing remove empty className attribute definitions.
		$wp_content = str_replace (',"className":""', '', $wp_content );
		$wp_content = str_replace ('"className":""', '', $wp_content );
		$wp_content = str_replace (',"className":" "', '', $wp_content );
		$wp_content = str_replace ('"className":" "', '', $wp_content );

		return $wp_content;
	}

	/**
	 * Add custom class to the exisitng className attribute.
	 */
	public function add_class( $wp_block_content, $new_class ) {
		// Find className attribute to add custom class.
		$class_attr_pos = strpos( $wp_block_content, '"className":"' );

		if ( false !== $class_attr_pos ) {
			/**
			 * ✓ Attribute className exists.
			 */
			// Start string position of className attribute value.
			$class_attr_pos = $class_attr_pos + strlen( '"className":"' );
			// End string position of className attribute value.
			$class_attr_pos_end = strpos( $wp_block_content, '"', $class_attr_pos ); // Find end of className declaration.
			// Value of the className attribute.
			$class_attr_val = substr( $wp_block_content , $class_attr_pos, intval( $class_attr_pos_end ) - intval( $class_attr_pos ) );

			// Don't procceed if our 'stl-block-7XXX' class already defined.
			if ( false !== strpos( $class_attr_val, $this->temp_class_name_prefix ) ) {
				return $wp_block_content;
			}

			// Don't procceed if our 'styled-XXX' class already defined.
			if ( false !== strpos( $class_attr_val, $this->styled_class_name_prefix ) ) {
				return $wp_block_content;
			}

			// Replace original className attribute value with our custom class added.
			$wp_block_content = str_replace(
				'className":"' . $class_attr_val . '"',
				'className":"' . $class_attr_val . ' ' . $new_class . '"',
				$wp_block_content
			);

			// Need to replace all the spaces between classes to regex spacing.
			// Users can put multiply spaces and Gutenberg will crop it accoring
			// to his taste without any particular logic.
			$class_attr_val = str_replace(
				' ',
				'\s?',
				$class_attr_val
			);

			// Replace HTML class attribute value with our custom class added.
			// https://regex101.com/r/w4uWgb/5
			$re = '/(class="[a-zA-Z0-9\-_\s]*' . $class_attr_val . '[a-zA-Z0-9\-_\s]*)"/';
			$subst = '$1 ' . $new_class . '"';
			$wp_block_content = preg_replace( $re, $subst, $wp_block_content );

		} else {

			/**
			 * Attribute className not found.
			 */
			$wp_block_content = false;
		}

		return $wp_block_content;
	}

	/**
	 * Create new className attribute and fill it with value.
	 */
	public function create_class_attr_gutenberg( $wp_block_opening_tag, $new_class ) {

		// Add className attribute.
		// Ex: <!-- wp:button {\"align\":\"center\",\"textColor\":\"#eee\"} -->
		$wp_block_attr_pos = strpos( $wp_block_opening_tag, '}' );

		if ( false !== $wp_block_attr_pos ) {
			// Other attributes already exists. Find the right position for insert.
			$wp_block_opening_tag = str_replace ('}', ',"className":""}', $wp_block_opening_tag );
		} else {
			// No atributes defined. Create a new empty one.
			$wp_block_opening_tag = str_replace ('-->', '{"className":""} -->', $wp_block_opening_tag );
		}

		// Get position of the empy className value field.
		$wp_block_attr_pos = strpos( $wp_block_opening_tag, '"}' );
		$wp_block_opening_tag = substr_replace( $wp_block_opening_tag,  $new_class, $wp_block_attr_pos, 0);

		return $wp_block_opening_tag;
	}

	/**
	 * Create new class attribute and fill it with value.
	 */
	public function create_class_attr_dom( $wp_block_content, $new_class ) {
		$block_content_parts = explode( '>', $wp_block_content );

		$valid_block_content_parts = array();

		foreach ( $block_content_parts as $key => $content_part ) {
			if ( '' !== trim( $content_part ) && ' ' !== trim( $content_part ) ) {
				$block_content_parts[ $key ] = $content_part . '>';
				if ( false === strpos( $content_part, '</' ) &&
					false === strpos( $content_part, '<!--' ) ) {
					// array_push( $valid_block_content_parts, $content_part  )
					$valid_block_content_parts[ $key ] = $content_part . '>';
				}
			}
		}
		/**
		 * Try to extract first html tag from the provided block content. Ex: <div.
		 */
		$first_tag_pos = strpos( $wp_block_content, '<' );
		$first_tag_end_pos = false;
		$extracted_tag = false;

		if ( false !== $first_tag_pos ) {
			$first_tag_end_pos = strpos( $wp_block_content, ' ', $first_tag_pos );

			if ( false !== $first_tag_end_pos ) {
				$extracted_tag = substr( $wp_block_content, $first_tag_pos, $first_tag_end_pos - $first_tag_pos );
			}
		}

		// Nothing found.
		if ( ! $extracted_tag ) {
			return $wp_block_content;
		}

		/**
		 * Validate extracted HTML tag.
		 * Second character in the extracted tag should be a letter.
		 * This will filter out cases like: <!--
		 */
		if (  ! preg_match('/[a-zA-Z]/', $extracted_tag[1] ) ) {
			return $wp_block_content;
		}

		// Split string after first tag end.
		if ( false !== $first_tag_pos ) {
			$first_tag_close = strpos( $wp_block_content, '>', $first_tag_pos );

			// String parts:
			$first_tag_full = substr ( $wp_block_content , $first_tag_pos, $first_tag_close - $first_tag_pos + 1 );
			$class_pos = strpos( $first_tag_full, 'class="' );

			// No class="" atribute found.
			if ( false === $class_pos ) {
				$closing_braket = '>';

				if ( strpos( $first_tag_full, '/>' ) ) {
					$closing_braket = '/>';
				}

				$first_tag_with_class = str_replace ( $closing_braket, ' class=""' . $closing_braket , $first_tag_full );
				$wp_block_content = str_replace( $first_tag_full, $first_tag_with_class, $wp_block_content );
				$class_pos = strpos( $first_tag_with_class, 'class="' );
			}

			$class_pos = intval($class_pos) + strlen('class="');
			// Add custom class into the string.
			$wp_block_content = substr_replace( $wp_block_content,  $new_class . ' ', $class_pos + 1, 0);
			// Remove unwanted space.
			$wp_block_content = str_replace ( $new_class .' "', $new_class .'"', $wp_block_content );
		}

		return $wp_block_content;
	}

}