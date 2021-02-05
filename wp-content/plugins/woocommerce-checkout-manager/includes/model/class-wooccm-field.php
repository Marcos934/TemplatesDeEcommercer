<?php

include_once('class-wooccm-model.php');

class WOOCCM_Field extends WOOCCM_Model
{
  protected $prefix = '';
  protected $table = '';
  protected $defaults = array();

  protected function order_fields($a, $b)
  {

    if (!isset($a['order']) || !isset($b['order']))
      return 0;

    if ($a['order'] == $b['order'])
      return 0;

    return ($a['order'] < $b['order']) ? -1 : 1;
  }

  protected function duplicated_name($name, $fields)
  {

    if (!empty($fields)) {
      if (is_array($fields)) {
        foreach ($fields as $item) {
          if (isset($item['name']) && $item['name'] == $name) {
            return true;
          }
        }
      }
    }

    return false;
  }

  public function get_field_id($fields, $key = 'key', $value)
  {

    if (count($fields)) {

      foreach ($fields as $id => $field) {
        if ($field[$key] == $value) {
          return $id;
        }
      }
    }

    return 0;
  }

  public function get_name($field_id)
  {
    return WOOCCM_PREFIX . $field_id;
  }

  public function get_key($prefix = '', $name)
  {
    return sprintf("%s_%s", $prefix, $name);
  }

  public function get_conditional_types()
  {
    $fields = self::get_types();

    unset($fields['heading']);
    unset($fields['button']);

    return array_keys($fields);
  }

  public function get_option_types()
  {
    return array(
      'multicheckbox',
      'multiselect',
      'select',
      'radio'
    );
  }

  public function get_multiple_types()
  {
    return array(
      'multicheckbox',
      'multiselect',
    );
  }

  public function get_template_types()
  {
    return array(
      'heading',
      'message',
      'button',
      'file',
      //        'country',
      //        'state'
    );
  }

  public function get_disabled_types()
  {
    return apply_filters('wooccm_fields_disabled_types', array(
      'message',
      'button',
    ));
  }

  public function get_types()
  {

    return apply_filters('wooccm_fields_types', array(
      'heading' => 'Heading',
      'email'   => 'Email',
      'tel'   => 'Phone',
      'message' => 'Message',
      'button' => 'Button',
      'text' => 'Text',
      'textarea' => 'Textarea',
      'password' => 'Password',
      'select' => 'Select',
      'radio' => 'Radio',
      'checkbox' => 'Checkbox',
      'time' => 'Timepicker',
      'date' => 'Datepicker',
      'number' => 'Number',
      'country' => 'Country',
      'state' => 'State',
      'multiselect' => 'Multiselect',
      'multicheckbox' => 'Multicheckbox',
      'colorpicker' => 'Colorpicker',
      'file' => 'File',
    ));
  }

  function get_args()
  {

    return array(
      'id' => null,
      'key' => '',
      'name' => '',
      'type' => 'text',
      'disabled' => false,
      'order' => null,
      'priority' => null,
      'label' => '',
      'placeholder' => '',
      'description' => '',
      'default' => '',
      'position' => '',
      'clear' => false,
      'options' => array(
        0 => array(
          'label' => esc_html__('Option', 'woocommerce-checkout-manager'),
          'add_price_total' => 0,
          'add_price_type' => 'fixed',
          'add_price_tax' => 0,
          'default' => '',
          'order' => 0
        )
      ),
      'required' => false,
      'message_type' => 'info',
      'button_type' => '',
      'button_link' => '',
      'class' => array(),
      // Input/Textarea
      'maxlength' => null,
      // Display
      // -------------------------------------------------------------------
      'show_cart_minimum' => 0,
      'show_cart_maximun' => 0,
      'show_role' => array(),
      'hide_role' => array(),
      'more_product' => false,
      'show_product' => array(),
      'hide_product' => array(),
      'show_product_cat' => array(),
      'hide_product_cat' => array(),
      'hide_account' => false,
      'hide_checkout' => false,
      'hide_email' => false,
      'hide_order' => false,
      'hide_invoice' => false,
      // Pickers
      // -------------------------------------------------------------------
      'time_format_ampm'  => true, 
      'time_limit_start' => null,
      'time_limit_end' => null,
      'time_limit_interval' => null,
      'date_limit' => 'fixed',
      'date_format' => '',
      'date_limit_variable_min' => -1,
      'date_limit_variable_max' => 1,
      'date_limit_fixed_min' => date('Y-m-d'),
      'date_limit_fixed_max' => date('Y-m-d'),
      'date_limit_days' => array(),
      // Price
      // -------------------------------------------------------------------
      'add_price' => false,
      'add_price_name' => '',
      'add_price_total' => null,
      'add_price_type' => 'fixed',
      'add_price_tax' => false,
      'extra_class' => '',
      // Conditional
      // -------------------------------------------------------------------
      'conditional' => false,
      'conditional_parent_key' => '',
      'conditional_parent_value' => '',
      // State
      // -------------------------------------------------------------------
      'country' => '',
      // Select 2
      // -------------------------------------------------------------------
      'select2' => false,
      'select2_allowclear' => false,
      'select2_selectonclose' => false,
      'select2_closeonselect' => false,
      'select2_search' => false,
      // Upload
      // -------------------------------------------------------------------
      'file_limit' => 1,
      'file_types' => array(),
      // Color
      // -------------------------------------------------------------------
      'pickertype' => '',
      // Listing
      // -------------------------------------------------------------------
      'listable' => false,
      'sortable' => false,
      'filterable' => false,
      'max' => '',
      'min' => '',
    );
  }

  public function get_defaults()
  {
    return $this->get_default_fields();
  }

  public function get_default_fields()
  {

    $fields = array();

    if ($this->prefix !== 'additional') {

      $prefix = sprintf('%s_', $this->prefix);

      //$filters = WOOCCM_Fields_Register::instance();
      //fix nesting level
      //remove_filter('woocommerce_' . $prefix . 'fields', array($filters, 'add_' . $prefix . 'fields'));
      remove_all_filters('woocommerce_' . $prefix . 'fields');

      $i = 0;
      foreach (WC()->countries->get_address_fields('', $prefix) as $key => $field) {

        $field['id'] = $i;
        $field['key'] = $key;
        $field['name'] = str_replace($prefix, '', $key);

        $fields[$i] = $field;
        $i++;
      }
    }

    return $fields;
  }

  public function get_fields()
  {

    // (is_array($fields = $this->get_items())) {

    if (count($fields = $this->get_items())) {

      foreach ($fields as $field_id => $field) {

        $fields[$field_id] = apply_filters('wooccm_checkout_field_filter', $this->sanitize_field($field_id, $field, $fields), $field_id);
      }

      uasort($fields, array(__CLASS__, 'order_fields'));

      $fields = apply_filters('wooccm_' . $this->prefix . '_fields', $fields);
    }
    //}

    return $fields;
  }

  public function update_fields($fields)
  {

    if (is_array($fields)) {

      foreach ($fields as $field_id => $field) {
        if (!array_key_exists('name', $field)) {
          return false;
        }
      }

      //reorder array based on ids
      ksort($fields);

      if ($this->save_items($fields)) {
        return $fields;
      }
    }

    return false;
  }

  public function delete_fields()
  {
    $this->delete();
    $this->save_items($this->get_defaults());
  }

  // Field
  // ---------------------------------------------------------------------------

  public function add_field($field_data)
  {
    return $this->add_item($field_data);
  }

  public function get_field($field_id)
  {
    return $this->get_item($field_id);
  }

  public function update_field($field_data)
  {
    return $this->update_item($field_data);
  }

  public function delete_field($field_id)
  {
    return $this->delete_item($field_id);
  }

  // Sanitize
  public function sanitize_field($field_id, $field, $fields)
  {

    $field['id'] = $field_id;

    if (empty($field['name'])) {

      $field['name'] = $this->get_name($field_id);

      if ($this->duplicated_name($field['name'], $fields)) {
        $field['name'] .= 'b';
      }
    }

    $field['key'] = $this->get_key($this->prefix, $field['name']);

    if (empty($field['position']) && is_array($field['class'])) {
      $position = array_intersect((array) $field['class'], array('form-row-wide', 'form-row-first', 'form-row-last'));
      if (isset($position[0])) {
        $field['position'] = $position[0];
      } else {
        $field['position'] = 'form-row-wide';
      }
    }

    if (empty($field['order'])) {
      $field['order'] = $field_id + 1;
    }

    if (!empty($field['conditional_parent_key'])) {

      if (strpos($field['conditional_parent_key'], $this->prefix) === false) {
        $field['conditional_parent_key'] = sprintf('%s_%s', $this->prefix, $field['conditional_parent_key']);
      }

      if ($field['conditional_parent_key'] == $field['key']) {
        $field['conditional_parent_key'] = '';
      }
    }

    if (is_array($field['options']) && count($field['options']) > 1) {
      uasort($field['options'], array(__CLASS__, 'order_fields'));
    }

    return wp_unslash($field);
  }
}
