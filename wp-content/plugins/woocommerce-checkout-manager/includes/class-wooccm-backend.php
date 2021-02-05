<?php

class WOOCCM_Field_Admin
{

  protected static $_instance;

  public function __construct()
  {

    add_action('wp_ajax_wooccm_select_search_products', array($this, 'ajax_select_search_products'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('admin_menu', array($this, 'add_menu_page'));
    add_filter('woocommerce_settings_tabs_array', array($this, 'add_tab'), 50);
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function ajax_select_search_products()
  {

    if (current_user_can('manage_woocommerce') && check_ajax_referer('search-products', 'security') && isset($_REQUEST['term'])) {

      if (empty($term) && isset($_GET['term'])) {
        $term = (string) wc_clean(wp_unslash($_GET['term']));
      }

      if (empty($term)) {
        wp_die();
      }

      if (!empty($_GET['limit'])) {
        $limit = absint($_GET['limit']);
      } else {
        $limit = absint(apply_filters('woocommerce_json_search_limit', 30));
      }

      $include_ids = !empty($_GET['include']) ? array_map('absint', (array) wp_unslash($_GET['include'])) : array();
      $exclude_ids = !empty($_GET['exclude']) ? array_map('absint', (array) wp_unslash($_GET['exclude'])) : array();
      $selected_ids = !empty($_GET['selected']) ? array_map('absint', (array) wp_unslash($_GET['selected'])) : array();

      $include_variations = false;

      $data_store = WC_Data_Store::load('product');
      $ids = $data_store->search_products($term, '', (bool) $include_variations, false, $limit, $include_ids, $exclude_ids + $selected_ids);

      $product_objects = array_filter(array_map('wc_get_product', $ids), 'wc_products_array_filter_readable');
      $products = array();

      foreach ($product_objects as $product_object) {
        $formatted_name = $product_object->get_formatted_name();
        $managing_stock = $product_object->managing_stock();

        if ($managing_stock && !empty($_GET['display_stock'])) {
          $stock_amount = $product_object->get_stock_quantity();
          /* Translators: %d stock amount */
          $formatted_name .= ' &ndash; ' . sprintf(esc_html__('Stock: %d', 'woocommerce'), wc_format_stock_quantity_for_display($stock_amount, $product_object));
        }

        $products[$product_object->get_id()] = rawurldecode($formatted_name);
      }

      wp_send_json(apply_filters('woocommerce_json_search_found_products', $products));
    }
  }

  public function enqueue_scripts()
  {

    $screen = get_current_screen();

    $admin = include_once(WOOCCM_PLUGIN_DIR . 'assets/backend/js/admin.asset.php');

    wp_register_style('wooccm-admin', plugins_url('assets/backend/css/admin.css', WOOCCM_PLUGIN_FILE), array(), WOOCCM_PLUGIN_VERSION, 'all');

    wp_register_script('jquery-serializejson', plugins_url('/assets/backend/jquery-serializejson/jquery-serializejson' . WOOCCM::is_min() . '.js', WOOCCM_PLUGIN_FILE), array('jquery'), WOOCCM_PLUGIN_VERSION, true);

    wp_register_script('wooccm-admin', plugins_url('/assets/backend/js/admin.js', WOOCCM_PLUGIN_FILE), $admin['dependencies'], $admin['version'], true);
    
    if ((isset($_GET['tab']) && $_GET['tab'] === WOOCCM_PREFIX) || in_array($screen->id, array(/* 'product', 'edit-product', */'shop_order', 'edit-shop_order'))) {
      wp_enqueue_style('wooccm-admin');
      wp_enqueue_script('wooccm-admin');
      wp_localize_script('wooccm-admin', 'wooccm_admin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wooccm_search_field'),
      ));
    }
  }

  public function add_tab($settings_tabs)
  {
    $settings_tabs[WOOCCM_PREFIX] = esc_html__('Checkout', 'woocommerce-checkout-manager');
    return $settings_tabs;
  }

  public function add_menu_page()
  {
    add_submenu_page('woocommerce', esc_html__('Checkout', 'woocommerce-checkout-manager'), esc_html__('Checkout', 'woocommerce-checkout-manager'), 'manage_woocommerce', admin_url('admin.php?page=wc-settings&tab=' . sanitize_title(WOOCCM_PREFIX)));
  }
}

WOOCCM_Field_Admin::instance();
