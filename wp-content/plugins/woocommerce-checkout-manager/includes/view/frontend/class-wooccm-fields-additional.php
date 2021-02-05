<?php

class WOOCCM_Fields_Additional
{

  protected static $_instance;

  public function __construct()
  {

    add_action('woocommerce_checkout_process', array($this, 'add_required_notice'));

    // Compatibility
    // -----------------------------------------------------------------------
    add_filter('default_option_wooccm_additional_position', array($this, 'position'));

    // Additional fields
    // -----------------------------------------------------------------------

    switch (get_option('wooccm_additional_position', 'before_order_notes')) {

      case 'before_billing_form':
        add_action('woocommerce_before_checkout_billing_form', array($this, 'add_additional_fields'));
        break;

      case 'after_billing_form':
        add_action('woocommerce_after_checkout_billing_form', array($this, 'add_additional_fields'));
        break;

      case 'before_order_notes':
        add_action('woocommerce_before_order_notes', array($this, 'add_additional_fields'));
        break;

      case 'after_order_notes':
        add_action('woocommerce_after_order_notes', array($this, 'add_additional_fields'));
        break;
    }
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function add_required_notice()
  {

    if (count($fields = WC()->checkout->get_checkout_fields('additional'))) {

      foreach ($fields as $key => $field) {

        if (!empty($field['required']) && empty($field['disabled']) && !isset($_POST[$key])) {

          $message = sprintf(esc_html__('%s is a required field.', 'woocommerce-checkout-manager'), '<strong>' . esc_html($field['label']) . '</strong>');

          wc_add_notice($message, 'error');
        }
      }
    }
  }

  //  function add_order_meta($order_id = 0, $data) {
  //
  //    if (count($fields = WC()->checkout->get_checkout_fields('additional'))) {
  //
  //      foreach ($fields as $key => $field) {
  //
  //        if (!empty($data[$key])) {
  //
  //          $value = $data[$key];
  //
  //          if ($field['type'] == 'textarea') {
  //            update_post_meta($order_id, sprintf('_%s', $key), wp_kses($value, false));
  //          } else if (is_array($value)) {
  //            update_post_meta($order_id, sprintf('_%s', $key), implode(',', array_map('sanitize_text_field', $value)));
  //          } else {
  //            update_post_meta($order_id, sprintf('_%s', $key), sanitize_text_field($value));
  //          }
  //        }
  //      }
  //    }
  //  }

  function add_additional_fields($checkout)
  {
?>
    <div class="wooccm-additional-fields">
      <?php
      if (count($fields = WC()->checkout->get_checkout_fields('additional'))) {

        foreach ($fields as $key => $field) {

          if (empty($field['disabled'])) {

            woocommerce_form_field($key, $field, $checkout->get_value($key));
          }
        }
      }
      ?>
      <div class="wooccm-clearfix"></div>
    </div>
<?php
  }

  function position($position = 'before_order_notes')
  {


    $options = get_option('wccs_settings');

    if (!empty($options['checkness']['position'])) {

      $positon = sanitize_text_field($options['checkness']['position']);

      switch ($position) {
        case 'before_shipping_form':
          $position = 'after_billing_form';
          break;

        case 'after_shipping_form':
          $position = 'before_order_notes';
          break;
      }
    }

    return $position;
  }
}

WOOCCM_Fields_Additional::instance();
