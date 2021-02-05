<?php

class WOOCCM_Field_Compatibility
{

  protected $fields = null;
  protected $prefix = '';
  protected $table = '';
  protected $defaults = array();
  private $old_to_old_types = array(
    'heading' => 'heading',
    'text' => 'wooccmtext',
    'textarea' => 'wooccmtextarea',
    'password' => 'wooccmpassword',
    'select' => 'wooccmselect',
    'radio' => 'wooccmradio',
    'checkbox' => 'checkbox_wccm',
    //'button' => esc_html__('Button', 'woocommerce-checkout-manager'),
    'country' => 'wooccmcountry',
    'state' => 'wooccmstate',
    'multiselect' => 'multiselect',
    'multicheckbox' => 'multicheckbox',
    'datepicker' => 'datepicker',
    'timepicker' => 'time',
    'colorpicker' => 'colorpicker',
    'file' => 'wooccmupload',
  );
  private $old_to_old_args = array(
    'disabled' => null,
    'order' => null,
    'priority' => null,
    'name' => 'cow',
    'type' => null,
    'label' => null,
    'placeholder' => null,
    'default' => 'force_title2',
    'position' => null,
    'clear' => 'clear_row',
    'options' => 'option_array',
    'required' => 'checkbox',
    // Display
    // -------------------------------------------------------------------
    'show_role' => 'role_option',
    'hide_role' => 'role_option2',
    'more_product' => 'more_content',
    'show_product' => 'single_px',
    'hide_product' => 'single_p',
    'show_product_cat' => 'single_px_cat',
    'hide_product_cat' => 'single_p_cat',
    // Timing
    // -------------------------------------------------------------------
    'time_limit_start' => 'start_hour',
    'time_limit_end' => 'end_hour',
    'time_limit_interval' => 'interval_min',
    'manual_min' => null,
    'date_limit' => 'date_limit',
    'date_limit_variable_min' => 'min_before',
    'date_limit_variable_max' => 'max_after',
    'date_limit_fixed_min' => null,
    'date_limit_fixed_max' => null,
    'date_limit_days' => null,
    'single_dd' => null,
    'single_mm' => null,
    'single_yy' => null,
    'single_max_dd' => null,
    'single_max_mm' => null,
    'single_max_yy' => null,
    // Amount
    // -------------------------------------------------------------------
    'add_price' => null,
    'add_price_name' => 'fee_name',
    'add_price_total' => 'add_price_field',
    'add_price_type' => null,
    'add_price_tax' => 'tax_remove',
    'extra_class' => null,
    // Conditional
    // -------------------------------------------------------------------
    'conditional' => 'conditional_parent_use',
    'conditional_parent_key' => 'conditional_tie',
    'conditional_parent_value' => 'chosen_valt',
    // Color
    // -------------------------------------------------------------------
    'pickertype' => 'colorpickertype',
    // State
    // -------------------------------------------------------------------
    'country' => null,
    // Upload
    // -------------------------------------------------------------------
    'file_limit' => null,
    'file_types' => null,
    // Listing
    // -------------------------------------------------------------------
    'listable' => null,
    'sortable' => null,
    'filterable' => null,
  );
  private $old_args = array(
    'disabled',
    'order',
    'priority',
    'cow',
    'type',
    'label',
    'placeholder',
    'force_title2',
    'position',
    'clear_row',
    'option_array',
    'checkbox',
    'role_option',
    'role_option2',
    'more_content',
    'single_p',
    'single_px',
    'single_p_cat',
    'single_px_cat',
    'start_hour',
    'end_hour',
    'interval_min',
    'manual_min',
    'min_before',
    'max_after',
    'single_dd',
    'single_mm',
    'single_yy',
    'single_max_dd',
    'single_max_mm',
    'single_max_yy',
    'days_disabler',
    'days_disabler0',
    'days_disabler1',
    'days_disabler2',
    'days_disabler3',
    'days_disabler4',
    'days_disabler5',
    'days_disabler6',
    'add_amount',
    'fee_name',
    'add_amount_field',
    'tax_remove',
    'conditional_parent_use',
    'conditional_tie',
    'chosen_valt',
    'extra_class',
    'colorpickertype',
    'colorpickerd',
    'role_options',
    'role_options2',
    'changenamep',
    'changename',
    // New
    // -----------------------------------------------------------------------
    'country',
    'file_limit',
    'file_types',
    'listable',
    'sortable',
    'filterable',
  );
  protected static $_instance;

  public function __construct()
  {

    if (false === get_option('wooccm_billing', false) && $fields = $this->get_fields_new('wccs_settings3', 'billing')) {
      update_option('wooccm_billing', $fields);
    }

    if (false === get_option('wooccm_shipping', false) && $fields = $this->get_fields_new('wccs_settings2', 'shipping')) {
      update_option('wooccm_shipping', $fields);
    }

    if (false === get_option('wooccm_additional', false) && $fields = $this->get_fields_new('wccs_settings', 'additional')) {
      update_option('wooccm_additional', $fields);
    }
  }

  public static function instance()
  {

    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function replace_keys($array = array(), $replace = array())
  {

    foreach ($array as $key => $value) {

      if (array_key_exists($key, $replace)) {

        $array[$replace[$key]] = $value;

        unset($array[$key]);
      }
    }

    return $array;
  }

  function replace_value($value = '', $replace = array())
  {
    if (array_key_exists($value, $replace)) {
      return $replace[$value];
    }

    return $value;
  }

  function string_to_array($value)
  {

    if (!empty($value) && !is_array($value)) {
      if (strpos($value, '||') !== false) {
        $value = explode('||', $value);
      } elseif (strpos($value, ',') !== false) {
        $value = explode(',', $value);
      } else {
        $value = (array) $value;
      }
    }

    return $value;
  }

  function array_to_string($value)
  {

    if (is_array($value)) {
      if (count($value)) {
        $value = implode('||', $value);
      } else {
        $value = @$value[0];
      }
    }

    return $value;
  }

  function get_new_args($field = array(), $prefix = '')
  {

    $replace = array_flip(array_filter($this->old_to_old_args));

    $field = $this->replace_keys($field, $replace);

    $field = wp_parse_args($field, WOOCCM()->$prefix->get_args());

    return $field;
  }

  function get_new_type($type = '')
  {

    $replace = array_flip(array_filter($this->old_to_old_types));

    $type = $this->replace_value($type, $replace);

    return $type;
  }

  function new_panel_compatibility($field_id, $field = array(), $fields = array(), $prefix = '')
  {

    $field = $this->get_new_args($field, $prefix);

    $field = WOOCCM()->$prefix->sanitize_field($field_id, $field, $fields);

    // options compatibility
    if (!empty($field['options']) && is_string($field['options'])) {

      if (strpos($field['options'], '||') !== false) {

        $options = explode('||', $field['options']);

        if (is_array($options)) {

          $field['options'] = array();
          //          $i = 0;
          foreach ($options as $key => $label) {
            $field['options'][] = array(
              'label' => $label,
              'add_price_total' => 0,
              'add_price_type' => 'fixed',
              'add_price_tax' => 0,
              'default' => ''
            );

            if (!empty($field['conditional_parent_value']) && $field['conditional_parent_value'] == $label) {
              $field['conditional_parent_value'] = $label;
            }

            //            $i++;
          }
        }
      }
    } else {
      $field['options'] = array();
    }

    if (!empty($field['conditional_parent_key']) && isset($field['conditional_parent_value'])) {
      if ($parent_id = WOOCCM()->$prefix->get_field_id($fields, 'key', $field['conditional_parent_key'])) {
        if (isset($fields[$parent_id]) && !empty($fields[$parent_id]['options'])) {
          $labels = array_column($fields[$parent_id]['options'], 'label');

          if (isset($labels[$field['conditional_parent_value']])) {

            $field['conditional_parent_value'] = $labels[$field['conditional_parent_value']];
          }
        }
      }
    }

    if ($field['type'] == 'colorpicker' && !empty($field['colorpickerd'])) {
      $field['default'] = $field['colorpickerd'];
    }

    $field['type'] = $this->get_new_type($field['type']);

    $field['show_role'] = $this->string_to_array($field['show_role']);
    $field['hide_role'] = $this->string_to_array($field['hide_role']);
    $field['show_product'] = $this->string_to_array($field['show_product']);
    $field['hide_product'] = $this->string_to_array($field['hide_product']);
    $field['show_product_cat'] = $this->string_to_array($field['show_product_cat']);
    $field['hide_product_cat'] = $this->string_to_array($field['hide_product_cat']);
    $field['add_price_tax'] = !@$field['add_amount_tax'];

    // Days
    if (!empty($field['days_disabler'])) {

      $field['date_limit_days'] = array();

      for ($day_index = 0; $day_index <= 6; $day_index++) {

        if (!empty($field['days_disabler' . $day_index])) {
          $field['date_limit_days'][strval($day_index)] = strval($day_index);
          unset($field['days_disabler' . $day_index]);
        }
      }
    }

    // Dates
    if (!empty($field['single_yy']) && !empty($field['single_mm']) && !empty($field['single_dd'])) {
      $field['date_limit_fixed_min'] = $field['single_yy'] . '-' . $field['single_mm'] . '-' . $field['single_dd'];
      unset($field['single_yy']);
      unset($field['single_mm']);
      unset($field['single_dd']);
    } else {
      $field['date_limit_fixed_min'] = '';
    }
    if (!empty($field['single_max_yy']) && !empty($field['single_max_mm']) && !empty($field['single_max_dd'])) {
      $field['date_limit_fixed_max'] = $field['single_max_yy'] . '-' . $field['single_max_mm'] . '-' . $field['single_max_dd'];
      unset($field['single_max_yy']);
      unset($field['single_max_mm']);
      unset($field['single_max_dd']);
    } else {
      $field['date_limit_fixed_max'] = '';
    }

    return $field;
    //return WOOCCM()->$prefix->sanitize_field_data(array_intersect_key($field, array_flip(array_keys(WOOCCM()->$prefix->get_args()))));
  }

  protected function get_fields_new($name, $prefix = '')
  {

    if ($fields = $this->get_option_old($name, $prefix)) {

      //$defaults = WOOCCM()->$prefix->get_default_fields();

      //$defaults_keys = array_column($defaults, 'key');

      foreach ($fields as $field_id => $field) {

        $field = $this->new_panel_compatibility($field_id, $field, $fields, $prefix);

        //        if (count($defaults) && $default_id = @max(array_keys(array_column($defaults, 'key'), $field['key']))) {
        /*if (count($defaults) && $default_id = WOOCCM()->$prefix->get_field_id($defaults, 'key', $field['key'])) {

          if (isset($defaults[$default_id])) {

            unset($field['type']);

            unset($field['class']);

            $field = wp_parse_args($field, $defaults[$default_id]);
          }
        }*/

        $fields[$field_id] = $field;
      }

      return $fields;
    }

    return false;
  }

  // Core
  // -------------------------------------------------------------------------

  protected function get_option_old($name, $prefix)
  {

    if ($fields = get_option($name)) {

      // Compatibility with 4.x
      // ---------------------------------------------------------------------
      if (array_key_exists("{$prefix}_buttons", $fields)) {
        $fields = $fields["{$prefix}_buttons"];
      }

      // Additional compatibility with 4.x
      // ---------------------------------------------------------------------
      if ('wccs_settings' == $name) {
        $fields = (array) @$fields['buttons'];
      }
    }

    return $fields;
  }
}

WOOCCM_Field_Compatibility::instance();
