<?php

class WOOCCM_Fields_Conditional
{

  protected static $_instance;

  public function __construct()
  {
    // Add field attributes
    add_filter('wooccm_checkout_field_filter', array($this, 'add_field_attributes'));
    add_action('wooccm_billing_fields', array($this, 'remove_required'));
    add_action('wooccm_shipping_fields', array($this, 'remove_required'));
    add_action('wooccm_additional_fields', array($this, 'remove_required'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function remove_required($fields)
  {

    foreach ($fields as $field_id => $field) {

      if (!empty($field['conditional']) && !empty($field['conditional_parent_key']) && ($field['conditional_parent_key'] != $field['key'])) {

        // Unset if parent is disabled
        // -----------------------------------------------------------------
        if (empty($fields[$field['conditional_parent_key']])) {
          unset($fields[$field['key']]);
          continue;
        }

        // Remove required
        // -----------------------------------------------------------------
        // On save
        if (isset($_REQUEST['woocommerce-process-checkout-nonce']) && (!isset($_POST[$field['conditional_parent_key']]) || !isset($field['conditional_parent_value']) || !array_intersect((array) $field['conditional_parent_value'], (array) $_POST[$field['conditional_parent_key']]))) {
          // Remove required attribute for hidden child fields
          $field['required'] = false;
          // Don't save hidden child fields in order
          unset($fields[$field['key']]);
          unset($_POST[$field['key']]);
        }
        // On update
        if (isset($_REQUEST['post_data']) && isset($_REQUEST['wc-ajax']) && $_REQUEST['wc-ajax'] == 'update_order_review') {

          $post_data = array();

          parse_str($_REQUEST['post_data'], $post_data);

          if (!isset($post_data[$field['conditional_parent_key']]) || !isset($field['conditional_parent_value']) || !array_intersect((array) $field['conditional_parent_value'], (array) $post_data[$field['conditional_parent_key']])) {
            // Remove field fee
            unset($fields[$field['key']]);
            unset($_POST[$field['key']]);
          }
        }
      }
    }

    return $fields;
  }

  public function add_field_attributes($field)
  {
    if (!empty($field['conditional']) && !empty($field['conditional_parent_key']) && isset($field['conditional_parent_value']) && ($field['conditional_parent_key'] != $field['name'])) {
      $field['class'][] = 'wooccm-conditional-child';
      $field['custom_attributes']['data-conditional-parent'] = $field['conditional_parent_key'];
      $field['custom_attributes']['data-conditional-parent-value'] = $field['conditional_parent_value'];
    }
    return $field;
  }
}

WOOCCM_Fields_Conditional::instance();
