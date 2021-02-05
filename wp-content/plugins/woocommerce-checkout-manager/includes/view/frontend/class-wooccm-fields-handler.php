<?php

class WOOCCM_Fields_Handler
{

  protected static $_instance;

  public function __construct()
  {

    // Prepare
    add_filter('wooccm_checkout_field_filter', array($this, 'add_field_filter'));

    // Add field classes
    add_filter('wooccm_checkout_field_filter', array($this, 'add_field_classes'));

    // Remove fields
    // -----------------------------------------------------------------------
    add_filter('woocommerce_checkout_fields', array($this, 'remove_checkout_fields'));

    // Fix address_2 field
    // -----------------------------------------------------------------------
    //add_filter('default_option_woocommerce_checkout_address_2_field', array($this, 'woocommerce_checkout_address_2_field'));
    // Fix address fields priority, required, placeholder, label
    //    add_filter('woocommerce_get_country_locale', '__return_empty_array');
    add_filter('woocommerce_get_country_locale_default', array($this, 'remove_fields_priority'));
    add_filter('woocommerce_get_country_locale_base', array($this, 'remove_fields_priority'));

    // Fix required country notice when shipping address is activated
    // -----------------------------------------------------------------------
    if (is_account_page()) {
      add_filter('woocommerce_checkout_posted_data', array($this, 'remove_address_fields'));
    }

    // Clear session
    add_action('woocommerce_checkout_posted_data', array($this, 'posted_data'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function posted_data($data)
  {

    if (count($fields = WC()->session->wooccm['fields'])) {

      foreach ($fields as $key => $field) {

        switch ($field['type']) {

          case 'multicheckbox':

            $data[$key] = isset($_POST[$key]) ? implode(', ', wc_clean(wp_unslash($_POST[$key]))) : '';

            break;

          case 'checkbox':

            if (!empty($_POST[$key])) {
              $data[$key] = esc_html__('Yes', 'woocommerce-checkout-manager');
            }

            //            else {
            //              $data[$key] = esc_html__('No', 'woocommerce-checkout-manager');
            //            }

            break;
        }
      }
    }

    return $data;
  }

  public function add_field_filter($field)
  {

    if (isset(WC()->session)) {
      $session_data = WC()->session->wooccm;
    }

    // keep attr id = attr name
    // -------------------------------------------------------------------------
    unset($field['id']);

    switch ($field['type']) {

      case 'select':
      case 'radio':

        if (!empty($field['options'])) {
          if (is_array($field['options'])) {
            $field['add_price_total'] = array_column($field['options'], 'add_price_total');
            $field['add_price_type'] = array_column($field['options'], 'add_price_type');
            $field['add_price_tax'] = array_column($field['options'], 'add_price_tax');
            $field['options'] = array_column($field['options'], 'label');
          }
        } else {
          $field['disabled'] = true;
        }

        break;

      case 'multiselect':
      case 'multicheckbox':

        if (!empty($field['options'])) {
          if (is_array($field['options'])) {
            $field['add_price_total'] = array_column($field['options'], 'add_price_total');
            $field['add_price_type'] = array_column($field['options'], 'add_price_type');
            $field['add_price_tax'] = array_column($field['options'], 'add_price_tax');
            $field['default'] = array_column($field['options'], 'default');
            $field['options'] = array_column($field['options'], 'label');
          }
        } else {
          $field['disabled'] = true;
        }

        break;

      case 'heading':
        $field['required'] = false;
        break;
    }

    // Priority
    // -----------------------------------------------------------------------
    if (isset($field['order'])) {
      $field['priority'] = $field['order'] * 10;
    }

    if (isset(WC()->session)) {
      $session_data['fields'][$field['key']] = $field;
      WC()->session->wooccm = $session_data;
    }

    return $field;
  }

  public function add_field_classes($field)
  {

    // Position
    // -----------------------------------------------------------------------
    if (!empty($field['position'])) {
      $field['class'] = array_diff($field['class'], array('form-row-wide', 'form-row-first', 'form-row-last'));
      $field['class'][] = $field['position'];
    }

    // WOOCCM
    // -----------------------------------------------------------------------

    $field['class'][] = 'wooccm-field';
    $field['class'][] = 'wooccm-field-' . $field['name'];

    // Type
    // -----------------------------------------------------------------------
    if (!empty($field['type'])) {
      $field['class'][] = 'wooccm-type-' . $field['type'];
    }

    // Color
    // -----------------------------------------------------------------------
    if (!empty($field['type']) && $field['type'] == 'colorpicker') {
      $field['class'][] = 'wooccm-colorpicker-' . $field['pickertype'];
    }

    // Extra
    // -----------------------------------------------------------------------
    if (!empty($field['extra_class'])) {
      $field['class'][] = $field['extra_class'];
    }

    // Clearfix
    // -----------------------------------------------------------------------
    if (!empty($field['clear'])) {
      $field['class'][] = 'wooccm-clearfix';
    }

    // Required
    // -----------------------------------------------------------------------

    if (isset($field['required'])) {

      $required = (int) $field['required'];

      $field['custom_attributes']['data-required'] = $required;

      if ($required) {
        $field['input_class'][] = 'wooccm-required-field';
      }
    }

    // Number
    if ($field['type'] == 'number') {
      if ($field['max']) {
        $field['custom_attributes']['max'] = (int) $field['max'];
      }
      if ($field['min']) {
        $field['custom_attributes']['min'] = (int) $field['min'];
      }
    }

    // Text/Textarea
    if ($field['type'] == 'text' || $field['type'] == 'textarea') {
      if ($field['maxlength']) {
        $field['custom_attributes']['maxlength'] = (int) $field['maxlength'];
      }
    }

    return $field;
  }

  public function remove_checkout_fields($fields)
  {

    foreach ($fields as $key => $type) {

      if (is_array($type)) {
        if (count($type)) {
          foreach ($type as $field_id => $field) {
            // Remove disabled
            // -------------------------------------------------------------------
            if (!empty($field['disabled'])) {
              unset($fields[$key][$field_id]);
            }
          }
        }
      }
    }

    // Fix for required address field
    if (get_option('woocommerce_ship_to_destination') == 'billing_only') {
      unset($fields['shipping']);
    }

    return $fields;
  }

  //function woocommerce_checkout_address_2_field($option) {
  //  return 'required';
  //}

  public function remove_fields_priority($fields)
  {

    foreach ($fields as $key => $field) {
      unset($fields[$key]['label']);
      unset($fields[$key]['placeholder']);
      unset($fields[$key]['priority']);
      unset($fields[$key]['required']);
      unset($fields[$key]['class']);
    }

    return $fields;
  }

  public function remove_address_fields($data)
  {

    $remove = array(
      'shipping_country',
      'shipping_address_1',
      'shipping_city',
      'shipping_state',
      'shipping_postcode'
    );

    foreach ($remove as $key) {
      if (empty($data[$key])) {
        unset($data[$key]);
      }
    }

    return $data;
  }
}

WOOCCM_Fields_Handler::instance();
