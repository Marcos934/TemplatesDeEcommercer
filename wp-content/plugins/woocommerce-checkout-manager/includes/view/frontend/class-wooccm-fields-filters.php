<?php

class WOOCCM_Fields_Filter
{

  protected static $_instance;
  public $count = 0;

  public function __construct()
  {
    $this->init();
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  // Custom fields
  // ---------------------------------------------------------------------------
  public function custom_field($field = '', $key, $args, $value)
  {

    $field = '';

    if ($args['required']) {
      //$args['class'][] = 'validate-required';
      $required = '&nbsp;<abbr class="required" title="' . esc_attr__('required', 'woocommerce-checkout-manager') . '">*</abbr>';
    } else {
      $required = '&nbsp;<span class="optional">(' . esc_html__('optional', 'woocommerce-checkout-manager') . ')</span>';
    }

    if (is_string($args['label_class'])) {
      $args['label_class'] = array($args['label_class']);
    }

    //if (is_null($value)) {
    if (!$value) {
      $value = $args['default'];
    }

    // Custom attribute handling.
    $custom_attributes = array();
    $args['custom_attributes'] = array_filter((array) $args['custom_attributes'], 'strlen');

    if ($args['maxlength']) {
      $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
    }

    if (!empty($args['autocomplete'])) {
      $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
    }

    if (true === $args['autofocus']) {
      $args['custom_attributes']['autofocus'] = 'autofocus';
    }

    if ($args['description']) {
      $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
    }

    if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
      foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
        $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
      }
    }

    if (!empty($args['validate'])) {
      foreach ($args['validate'] as $validate) {
        $args['class'][] = 'validate-' . $validate;
      }
    }

    //$field           = '';
    $label_id = $args['id'];
    $sort = $args['priority'] ? $args['priority'] : '';
    $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</p>';
    switch ($args['type']) {

      case 'radio':
        $field = '';

        if (!empty($args['options'])) {

          $field .= ' <span class="woocommerce-radio-wrapper" ' . implode(' ', $custom_attributes) . '>';

          foreach ($args['options'] as $option_key => $option_text) {
            $field .= '<input type="radio" class="input-checkbox" value="' . esc_attr($option_text) . '" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '_' . esc_attr($option_key) . '"' . checked($value, $option_text, false) . ' />';
            $field .= '<label for="' . esc_attr($key) . '_' . esc_attr($option_key) . '" class="checkbox ' . implode(' ', $args['label_class']) . '">' . $option_text . '</label><br>';
          }

          $field .= ' </span>';
        }

        break;

      case 'select':

        $field = '';

        if (!empty($args['options'])) {
          $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">';
          if (!empty($args['placeholder'])) {
            $field .= '<option value="" disabled="disabled" selected="selected">' . esc_attr($args['placeholder']) . '</option>';
          }
          foreach ($args['options'] as $option_key => $option_text) {
            $field .= '<option value="' . esc_attr($option_text) . '" ' . selected($value, $option_text, false) . '>' . esc_attr($option_text) . '</option>';
          }
          $field .= '</select>';
        }

        break;

      case 'multiselect':

        $field = '';

        $value = is_array($value) ? $value : array_map('trim', (array) explode(',', $value));

        if (!empty($args['options'])) {
          $field .= '<select name="' . esc_attr($key) . '[]" id="' . esc_attr($key) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" multiple="multiple" ' . implode(' ', $custom_attributes) . '>';
          foreach ($args['options'] as $option_key => $option_text) {
            $field .= '<option value="' . esc_attr($option_text) . '" ' . selected(in_array($option_text, $value), 1, false) . '>' . esc_attr($option_text) . '</option>';
          }
          $field .= ' </select>';
        }

        break;

      case 'multicheckbox':

        $field = '';

        $value = is_array($value) ? $value : array_map('trim', (array) explode(',', $value));

        if (!empty($args['options'])) {

          $field .= ' <span class="woocommerce-multicheckbox-wrapper" ' . implode(' ', $custom_attributes) . '>';

          foreach ($args['options'] as $option_key => $option_text) {
            //$field .='<label><input type="checkbox" name="' . esc_attr($key) . '[]" value="1"' . checked(in_array($option_key, $value), 1, false) . ' /> ' . esc_attr($option_text) . '</label>';
            $field .= '<label><input type="checkbox" name="' . esc_attr($key) . '[]" value="' . esc_attr($option_text) . '"' . checked(in_array($option_text, $value), 1, false) . ' /> ' . esc_attr($option_text) . '</label>';
          }

          $field .= '</span>';
        }

        break;

      case 'file':

        $field = '';

        $field .= '<button style="width:100%" class="wooccm-file-button button alt" type="button" class="button alt" id="' . esc_attr($key) . '_button">' . esc_html($args['placeholder']) . '</button>';
        //$field .= '<input class="wooccm-file-field" type="text" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="test" />';
        $field .= '<input class="wooccm-file-field" type="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="" ' . implode(' ', $custom_attributes) . ' />';
        $field .= '<input style="display:none;" class="fileinput-button" type="file" name="' . esc_attr($key) . '_file" id="' . esc_attr($key) . '_file" multiple="multiple" />';
        $field .= '<span style="display:none;" class="wooccm-file-list"></span>';

        break;
    }

    if (!empty($field)) {
      $field_html = '';

      if ($args['label'] && 'checkbox' !== $args['type']) {
        $field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
      }

      $field_html .= '<span class="woocommerce-input-wrapper">' . $field;

      if ($args['description']) {
        $field_html .= '<span class="description" id="' . esc_attr($args['id']) . '-description" aria-hidden="true">' . wp_kses_post($args['description']) . '</span>';
      }

      $field_html .= '</span>';

      $container_class = esc_attr(implode(' ', $args['class']));
      $container_id = esc_attr($args['id']) . '_field';
      $field = sprintf($field_container, $container_class, $container_id, $field_html);
    }

    return $field;
  }

  // Heading
  // ---------------------------------------------------------------------------
  public function heading_field($field = '', $key, $args, $value)
  {

    // Custom attribute handling.
    $custom_attributes = array();

    if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
      foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
        $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
      }
    }

    $sort = $args['priority'] ? $args['priority'] : '';

    $field_html = '<h3 ' . implode(' ', $custom_attributes) . '>' . esc_html($args['label']) . '</h3>';

    $container_class = esc_attr(implode(' ', $args['class']));
    $container_id = esc_attr($args['id']) . '_field';
    $field_container = '<div class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</div>';

    return sprintf($field_container, $container_class, $container_id, $field_html);
  }

  // Colorpicker
  // ---------------------------------------------------------------------------
  public function colorpicker_field($field = '', $key, $args, $value)
  {

    $args['type'] = 'text';
    $args['maxlength'] = 7;

    ob_start();

    woocommerce_form_field($key, $args, $value);

    $field = ob_get_clean();

    $field = str_replace('</p>', ' <span class="wooccmcolorpicker_container" class="spec_shootd"></span></p>', $field);

    return $field;
  }

  // Country 
  // ---------------------------------------------------------------------------
  public function country_field($field = '', $key, $args, $value)
  {

    static $instance = 0;

    if ($instance) {
      return $field;
    }

    $instance++;

    ob_start();

    if (!empty($args['default'])) {
      $value = $args['default'];
    }

    woocommerce_form_field($key, $args, $value);

    $field = ob_get_clean();

    return $field;
  }

  //  State
  // ---------------------------------------------------------------------------
  public function state_field($field = '', $key, $args, $value)
  {

    static $instance = 0;

    if ($instance) {
      return $field;
    }

    $instance++;

    ob_start();

    if (!empty($args['default'])) {
      $value = $args['default'];
    }

    woocommerce_form_field($key, $args, $value);

    $field = ob_get_clean();

    return $field;
  }

  public function hidden_field($field = '', $key, $args, $value)
  {

    static $instance = 0;

    if ($instance) {
      return $field;
    }

    $instance++;

    $field .= '<input type="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="' . esc_html($value) . '" ' . implode(' ', $args['custom_attributes']) . ' readonly="readonly" />';

    return $field;
  }

  public function init()
  {
    add_filter('woocommerce_form_field_radio', array($this, 'custom_field'), 10, 4);
    add_filter('woocommerce_form_field_multicheckbox', array($this, 'custom_field'), 10, 4);
    add_filter('woocommerce_form_field_multiselect', array($this, 'custom_field'), 10, 4);
    add_filter('woocommerce_form_field_select', array($this, 'custom_field'), 10, 4);
    add_filter('woocommerce_form_field_file', array($this, 'custom_field'), 10, 4);
    add_filter('woocommerce_form_field_heading', array($this, 'heading_field'), 10, 4);
    add_filter('woocommerce_form_field_colorpicker', array($this, 'colorpicker_field'), 10, 4);
    add_filter('woocommerce_form_field_country', array($this, 'country_field'), 10, 4);
    add_filter('woocommerce_form_field_state', array($this, 'state_field'), 10, 4);
    add_filter('woocommerce_form_field_hidden', array($this, 'hidden_field'), 10, 4);
  }
}

WOOCCM_Fields_Filter::instance();
