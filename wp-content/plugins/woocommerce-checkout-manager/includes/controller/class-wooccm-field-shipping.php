<?php

class WOOCCM_Field_Controller_Shipping extends WOOCCM_Field_Controller
{

  protected static $_instance;
  public $shipping;

  public function __construct()
  {
    include_once(WOOCCM_PLUGIN_DIR . 'includes/model/class-wooccm-field-shipping.php');

    add_action('wooccm_sections_header', array($this, 'add_header'));
    add_action('woocommerce_sections_' . WOOCCM_PREFIX, array($this, 'add_section'), 99);
    add_filter('woocommerce_admin_shipping_fields', array($this, 'add_admin_shipping_fields'), 999);

  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  // Admin
  // ---------------------------------------------------------------------------

  public function add_header()
  {
    global $current_section;
?>
    <li><a href="<?php echo admin_url('admin.php?page=wc-settings&tab=wooccm&section=shipping'); ?>" class="<?php echo ($current_section == 'shipping' ? 'current' : ''); ?>"><?php esc_html_e('Shipping', 'woocommerce-checkout-manager'); ?></a> | </li>
<?php
  }

  public function add_section()
  {

    global $current_section, $wp_roles, $wp_locale;

    if ('shipping' == $current_section) {

      $fields = WOOCCM()->shipping->get_fields();
      $defaults = WOOCCM()->shipping->get_defaults();
      $types = WOOCCM()->shipping->get_types();
      $conditionals = WOOCCM()->shipping->get_conditional_types();
      $option = WOOCCM()->billing->get_option_types();
      $multiple = WOOCCM()->billing->get_multiple_types();
      $template = WOOCCM()->billing->get_template_types();
      $disabled = WOOCCM()->billing->get_disabled_types();
      $product_categories = $this->get_product_categories();

      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/backend/pages/shipping.php');
    }
  }

  function add_admin_shipping_fields($shipping_fields)
  {

    if (!$fields = WOOCCM()->shipping->get_fields()) {
      return $shipping_fields;
    }

    $template = WOOCCM()->shipping->get_template_types();

    foreach ($fields as $field_id => $field) {

      if (!isset($field['name'])) {
        continue;
      }

      if (isset($shipping_fields[$field['name']])) {
        continue;
      }

      if (in_array($field['name'], $template)) {
        continue;
      }

      if (!isset($field['type']) || $field['type'] != 'textarea') {
        $field['type'] = 'text';
      }

      $shipping_fields[$field['name']] = $field;
      $shipping_fields[$field['name']]['id'] = sprintf('_%s', (string) $field['key']);
      $shipping_fields[$field['name']]['label'] = $field['label'];
      $shipping_fields[$field['name']]['name'] = $field['key'];
      $shipping_fields[$field['name']]['value'] = null;
      $shipping_fields[$field['name']]['class'] = join(' ', $field['class']);
      //$shipping_fields[$field['name']]['wrapper_class'] = 'wooccm-premium';
    }


    return $shipping_fields;
  }
  
}

WOOCCM_Field_Controller_Shipping::instance();
