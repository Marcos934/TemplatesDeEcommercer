<?php

class WOOCCM_Fields_Register
{

  protected static $_instance;

  public function __construct()
  {
    // Add keys
    // -----------------------------------------------------------------------
    add_filter('wooccm_additional_fields', array($this, 'add_keys'));
    add_filter('wooccm_billing_fields', array($this, 'add_keys'));
    add_filter('wooccm_shipping_fields', array($this, 'add_keys'));

    // Billing fields
    // -----------------------------------------------------------------------
    add_filter('woocommerce_billing_fields', array($this, 'add_billing_fields'));

    // Shipping fields
    // -----------------------------------------------------------------------
    add_filter('woocommerce_shipping_fields', array($this, 'add_shipping_fields'));

    // Additional fields
    // -----------------------------------------------------------------------
    add_filter('woocommerce_checkout_fields', array($this, 'add_additional_fields'));

    // My account
    // woocommerce 4.2 issue, the shipping and billing fields not working on my account when required field is empty
    // temporary fix excluding required fields in my account
    ///add_filter('woocommerce_address_to_edit', array($this, 'add_my_account_fields'), 10, 2);
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function add_billing_fields($fields)
  {
    return WOOCCM()->billing->get_fields();
  }

  public function add_shipping_fields($fields)
  {
    return WOOCCM()->shipping->get_fields();
  }

  public function add_my_account_fields($defaults, $load_address)
  {

    if (isset(WOOCCM()->$load_address)) {

      $fields = WOOCCM()->$load_address->get_fields();

      $keys = array_column(WOOCCM()->$load_address->get_fields(), 'key');

      foreach ($fields as $field_id => $field) {
        if (!isset($field['value'])) {

          // when country field is visible default state is set via javascript
          if (in_array("{$load_address}_country", $keys)) {
            unset($fields[$field_id]['country']);
          }          
          $fields[$field_id]['value'] = get_user_meta(get_current_user_id(), $field['key'], true);
        }
      }

      return $fields;
    }

    return $defaults;
  }

  public function add_additional_fields($fields)
  {

    $fields['additional'] = WOOCCM()->additional->get_fields();

    return $fields;
  }

  public function add_keys($fields)
  {

    $frontend_fields = array();

    foreach ($fields as $field_id => $field) {
      if (!empty($field['key']) && empty($field['disabled'])) {
        $frontend_fields[$field['key']] = $field;
      }
    }

    return $frontend_fields;
  }
}

WOOCCM_Fields_Register::instance();
