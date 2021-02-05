<?php

include_once(WOOCCM_PLUGIN_DIR . 'includes/controller/class-wooccm-controller.php');

class WOOCCM_Field_Controller extends WOOCCM_Controller
{

  protected static $_instance;
  public $billing;

  public function __construct()
  {

    include_once(WOOCCM_PLUGIN_DIR . 'includes/controller/class-wooccm-field-billing.php');
    include_once(WOOCCM_PLUGIN_DIR . 'includes/controller/class-wooccm-field-shipping.php');
    include_once(WOOCCM_PLUGIN_DIR . 'includes/controller/class-wooccm-field-additional.php');

    if (!is_admin()) {
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-register.php');
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-additional.php');
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-disable.php');
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-conditional.php');
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-handler.php');
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-i18n.php');
      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/frontend/class-wooccm-fields-filters.php');
    }

    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('wp_ajax_wooccm_load_parent', array($this, 'ajax_load_parent'));
    add_action('wp_ajax_wooccm_load_field', array($this, 'ajax_load_field'));
    add_action('wp_ajax_wooccm_save_field', array($this, 'ajax_save_field'));
    add_action('wp_ajax_wooccm_delete_field', array($this, 'ajax_delete_field'));
    add_action('wp_ajax_wooccm_reset_fields', array($this, 'ajax_reset_fields'));
    add_action('wp_ajax_wooccm_change_field_attribute', array($this, 'ajax_change_field_attribute'));
    add_action('wp_ajax_wooccm_toggle_field_attribute', array($this, 'ajax_toggle_field_attribute'));
    add_action('woocommerce_settings_save_' . WOOCCM_PREFIX, array($this, 'save_field_order'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function enqueue_scripts()
  {

    global $current_section;

    $admin_field = include_once(WOOCCM_PLUGIN_DIR . 'assets/backend/js/admin-field.asset.php');

    wp_register_script('wooccm-admin-field', plugins_url('assets/backend/js/admin-field.js', WOOCCM_PLUGIN_FILE), $admin_field['dependencies'],  $admin_field['dependencies'], true);

    wp_localize_script('wooccm-admin-field', 'wooccm_field', array(
      'ajax_url' => admin_url('admin-ajax.php?section=' . $current_section),
      'nonce' => wp_create_nonce('wooccm_field'),
      'args' => WOOCCM()->billing->get_args(),
      'message' => array(
        'remove' => esc_html__('Are you sure you want to remove this field?', 'woocommerce-checkout-manager'),
        'reset' => esc_html__('Are you sure you want to reset this fields?', 'woocommerce-checkout-manager')
      )
    ));

    if (isset($_GET['tab']) && $_GET['tab'] === WOOCCM_PREFIX) {
      wp_enqueue_style('media-views');
      wp_enqueue_script('wooccm-admin-field');
    }
  }

  public function get_product_categories()
  {

    $args = array(
      'taxonomy' => 'product_cat',
      'orderby' => 'id',
      'order' => 'ASC',
      'hide_empty' => true,
      'fields' => 'all'
    );

    return get_terms($args);
  }

  // Ajax
  // ---------------------------------------------------------------------------

  public function ajax_toggle_field_attribute()
  {

    if (
      current_user_can('manage_woocommerce') &&
      check_ajax_referer('wooccm_field', 'nonce') &&
      isset($_REQUEST['section']) &&
      isset($_REQUEST['field_id']) &&
      isset($_REQUEST['field_attr'])
    ) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        $field_id = wc_clean(wp_unslash($_REQUEST['field_id']));
        $attr = wc_clean(wp_unslash($_REQUEST['field_attr']));

        if ($field = WOOCCM()->$section->get_field($field_id)) {

          $value = $field[$attr] = !(bool) @$field[$attr];

          WOOCCM()->$section->update_field($field);

          parent::success_ajax($value);
        }
      }
    }

    parent::error_reload_page();
  }

  public function ajax_change_field_attribute()
  {

    if (
      current_user_can('manage_woocommerce') &&
      check_ajax_referer('wooccm_field', 'nonce') &&
      isset($_REQUEST['section']) &&
      isset($_REQUEST['field_id']) &&
      isset($_REQUEST['field_attr']) &&
      isset($_REQUEST['field_value'])
    ) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        $field_id = wc_clean(wp_unslash($_REQUEST['field_id']));
        $attr = wc_clean(wp_unslash($_REQUEST['field_attr']));

        if ($field = WOOCCM()->$section->get_field($field_id)) {

          $value = $field[$attr] = wc_clean(wp_unslash($_REQUEST['field_value']));

          $field = WOOCCM()->$section->update_field($field);

          parent::success_ajax($value);
        }
      }
    }

    parent::error_reload_page();
  }

  public function ajax_save_field()
  {

    if (isset($_REQUEST['field_data']) && current_user_can('manage_woocommerce') && check_ajax_referer('wooccm_field', 'nonce', false)) {
      $field_data = json_decode(stripslashes($_REQUEST['field_data']), true);
      if (is_array($field_data)) {
        if (isset($field_data['id'])) {

          unset($field_data['show_product_selected']);
          unset($field_data['hide_product_selected']);

          return parent::success_ajax($this->save_modal_field($field_data));
        } else {
          return parent::success_ajax($this->add_modal_field($field_data));
        }
      }
    }

    return parent::error_reload_page();
  }

  public function ajax_delete_field()
  {

    if (
      current_user_can('manage_woocommerce') &&
      check_ajax_referer('wooccm_field', 'nonce') &&
      isset($_REQUEST['field_id'])
    ) {

      $field_id = wc_clean(wp_unslash($_REQUEST['field_id']));

      if ($this->delete_field($field_id)) {

        parent::success_ajax($field_id);
      }
    }

    parent::error_reload_page();
  }

  public function ajax_reset_fields()
  {

    if (
      current_user_can('manage_woocommerce') &&
      check_ajax_referer('wooccm_field', 'nonce') &&
      isset($_REQUEST['section'])
    ) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        WOOCCM()->$section->delete_fields();

        parent::success_ajax();
      }
    }

    parent::error_reload_page();
  }

  public function ajax_load_field()
  {

    if (
      current_user_can('manage_woocommerce') &&
      check_ajax_referer('wooccm_field', 'nonce') &&
      isset($_REQUEST['field_id'])
    ) {

      $field_id = wc_clean(wp_unslash($_REQUEST['field_id']));

      if ($field = $this->get_modal_field($field_id)) {
        parent::success_ajax($field);
      }

      parent::error_ajax(esc_html__('Undefined field id', 'woocommerce-checkout-manager'));
    }

    parent::error_reload_page();
  }

  // Modal
  // ---------------------------------------------------------------------------

  public function get_modal_field($field_id)
  {

    if (array_key_exists('section', $_REQUEST)) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        if ($fields = WOOCCM()->$section->get_fields()) {

          if (isset($fields[$field_id])) {

            $field = $fields[$field_id];

            if (!empty($field['show_product'])) {
              $field['show_product_selected'] = array_filter(array_combine((array) $field['show_product'], array_map('get_the_title', (array) $field['show_product'])));
            } else {
              $field['show_product_selected'] = array();
            }
            if (!empty($field['hide_product'])) {
              $field['hide_product_selected'] = array_filter(array_combine((array) $field['hide_product'], array_map('get_the_title', (array) $field['hide_product'])));
            } else {
              $field['hide_product_selected'] = array();
            }

            if (!empty($field['conditional_parent_key']) && $field['conditional_parent_key'] != $field['key']) {

              //              $parent_id = @max(array_keys(array_column($fields, 'key'), $field['conditional_parent_key']));
              $parent_id = WOOCCM()->$section->get_field_id($fields, 'key', $field['conditional_parent_key']);

              if (isset($fields[$parent_id])) {
                $field['parent'] = $fields[$parent_id];
              }
            }

            //don't remove empty attr because previus data remain
            //$field = array_filter($field);

            return $field;
          }
        }
      }
    }
  }

  public function ajax_load_parent()
  {

    if (!empty($_REQUEST['conditional_parent_key'])) {

      $key = $_REQUEST['conditional_parent_key'];

      if (array_key_exists('section', $_REQUEST)) {

        $section = wc_clean(wp_unslash($_REQUEST['section']));

        if (isset(WOOCCM()->$section)) {

          if ($fields = WOOCCM()->$section->get_fields()) {

            $parent_id = WOOCCM()->$section->get_field_id($fields, 'key', $key);

            if (isset($fields[$parent_id])) {
              parent::success_ajax($fields[$parent_id]);
            }
          }
        }
      }
    }
  }

  // Save
  // ---------------------------------------------------------------------------

  public function save_modal_field($field_data)
  {

    if (array_key_exists('section', $_REQUEST)) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        $field_data = wp_parse_args($field_data, WOOCCM()->$section->get_args());

        // don't override
        //unset($field_data['order']);
        //unset($field_data['required']);
        //unset($field_data['position']);
        //unset($field_data['disabled']);

        return WOOCCM()->$section->update_field($field_data);
      }
    }
  }

  public function add_modal_field($field_data)
  {
    if (array_key_exists('section', $_REQUEST)) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        return WOOCCM()->$section->add_field($field_data);
      }
    }
  }

  public function delete_field($field_id)
  {

    if (array_key_exists('section', $_REQUEST)) {

      $section = wc_clean(wp_unslash($_REQUEST['section']));

      if (isset(WOOCCM()->$section)) {

        return WOOCCM()->$section->delete_field($field_id);
      }
    }
  }

  function save_field_order()
  {

    global $current_section;

    if (in_array($current_section, array('billing', 'shipping', 'additional'))) {

      $section = wc_clean(wp_unslash($current_section));

      if (array_key_exists('field_order', $_POST)) {

        $field_order = wc_clean(wp_unslash($_POST['field_order']));

        if (is_array($field_order) && count($field_order) > 0) {

          if (isset(WOOCCM()->$section)) {

            $fields = WOOCCM()->$section->get_fields();

            $loop = 1;

            foreach ($field_order as $field_id) {

              if (isset($fields[$field_id])) {

                $fields[$field_id]['order'] = $loop;

                $loop++;
              }
            }

            WOOCCM()->$section->update_fields($fields);
          }
        }
      }
    }
  }
}

WOOCCM_Field_Controller::instance();
