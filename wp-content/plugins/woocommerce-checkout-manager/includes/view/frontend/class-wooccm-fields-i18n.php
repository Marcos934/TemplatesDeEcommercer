<?php

class WOOCCM_Fields_i18n
{

  protected static $_instance;
  protected static $domain = 'woocommerce';

  public function __construct()
  {
    add_filter('wooccm_checkout_field_filter', array($this, 'translate_field'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function register_wpml_string($value)
  {

    if (!empty($value) && function_exists('icl_register_string')) {

      if (is_array($value)) {

        foreach ($value as $key => $name) {
          icl_register_string(WOOCCM_PLUGIN_NAME, $name, $name);
        }

        return $value;
      }

      if (is_string($value)) {
        icl_register_string(WOOCCM_PLUGIN_NAME, $value, $value);
        return $value;
      }
    }

    return $value;
  }

  public function i18n($string)
  {

    if (function_exists('icl_t')) {
      return icl_t(WOOCCM_PLUGIN_NAME, $string, $string);
    }

    return esc_html__($string, self::$domain);
  }

  public function translate($value)
  {

    if (!empty($value)) {

      if (is_array($value)) {
        foreach ($value as $key => $name) {
          if (is_string($name)) {
            $value[$key] = $this->i18n($name);
          }
        }
      }

      if (is_string($value)) {
        $value = $this->i18n($value);
      }
    }

    return $value;
  }

  public function translate_field($field)
  {

    // ii18n
    // -----------------------------------------------------------------------

    if (!empty($field['label'])) {
      $field['label'] = $this->translate($field['label']);
    }

    if (!empty($field['placeholder'])) {
      $field['placeholder'] = $this->translate($field['placeholder']);
    }

    return $field;
  }
}

WOOCCM_Fields_i18n::instance();
