<?php

class WOOCCM_Checkout_Premium_Controller
{

  protected static $_instance;

  public function __construct()
  {
    add_action('wooccm_sections_header', array($this, 'add_header'));
    add_action('admin_menu', array($this, 'add_menu'));
    add_action('admin_head', array($this, 'remove_menu'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function remove_menu()
  {
?>
    <style>
      li.toplevel_page_wooccm {
        display: none;
      }
    </style>
  <?php
  }

  // Admin    
  // -------------------------------------------------------------------------

  function add_page()
  {
    include_once(WOOCCM_PLUGIN_DIR . 'includes/view/backend/pages/premium.php');
  }

  function add_header()
  {
  ?>
    <li><a href="<?php echo admin_url('admin.php?page=' . WOOCCM_PREFIX); ?>"><?php echo esc_html__('Premium', 'woocommerce-checkout-manager'); ?></a></li> |
<?php
  }

  function add_menu()
  {
    add_menu_page(WOOCCM_PLUGIN_NAME, WOOCCM_PLUGIN_NAME, 'manage_woocommerce', WOOCCM_PREFIX, array($this, 'add_page'));
    add_submenu_page(WOOCCM_PREFIX, esc_html__('Premium', 'woocommerce-checkout-manager'), esc_html__('Premium', 'woocommerce-checkout-manager'), 'manage_woocommerce', WOOCCM_PREFIX, array($this, 'add_page'));
  }
}

WOOCCM_Checkout_Premium_Controller::instance();
