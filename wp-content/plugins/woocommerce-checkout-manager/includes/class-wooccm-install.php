<?php

class WOOCCM_Install {

  public static function install() {

    // Check if we are not already running this routine.
    if ('yes' === get_transient('wooccm_installing')) {
      return;
    }

    // If we made it till here nothing is running yet, lets set the transient now.
    set_transient('wooccm_installing', 'yes', MINUTE_IN_SECONDS * 10);
    set_transient('wooccm-first-rating', true, MONTH_IN_SECONDS);

    //wooccm_install();
  }

  public static function update() {

    if (!get_option('wooccm_billing', false)) {
      update_option(WOOCCM()->billing->get_fields());
    }

    if (!get_option('wooccm_shipping', false)) {
      update_option(WOOCCM()->shipping->get_fields());
    }

    if (!get_option('wooccm_additional', false)) {
      update_option(WOOCCM()->additional->get_fields());
    }
  }

  public static function old_panel_compatibility($field_id, $field = array()) {

    $field = $this->get_old_args($field);

    $field = wp_parse_args($field, array_fill_keys($this->old_args, null));

    if (!is_numeric($field['order'])) {
      $field['order'] = $field_id + 1;
    }

    $field['type'] = $this->get_old_type($field['type']);

    if (empty($field['position']) && isset($field['class'])) {
      if ($position = $this->array_to_string(array_intersect((array) $field['class'], array('form-row-wide', 'form-row-first', 'form-row-last')))) {
        $field['position'] = $position;
      }
    }

    $field['role_option'] = $this->array_to_string($field['role_option']);
    $field['role_option2'] = $this->array_to_string($field['role_option2']);
    //$field['option_array'] = $this->array_to_string($field['option_array']);
    $field['single_p'] = $this->array_to_string($field['single_p']);
    $field['single_px'] = $this->array_to_string($field['single_px']);
    $field['single_p_cat'] = $this->array_to_string($field['single_p_cat']);
    $field['single_px_cat'] = $this->array_to_string($field['single_px_cat']);
    $field['tax_remove'] = !$field['tax_remove'];

    // Days
    if (is_array($field['days_disabler'])) {
      foreach ($field['days_disabler'] as $day_index => $day) {
        $field['days_disabler' . strval($day_index)] = 1;
      }
      $field['days_disabler'] = 1;
      unset($field['date_limit_days']);
    }

    // Dates
    if (!empty($field['date_limit_fixed_min'])) {

      $min = explode('-', $field['date_limit_fixed_min']);

      $field['single_yy'] = $min[0];
      $field['single_mm'] = $min[1];
      $field['single_dd'] = $min[2];
    }

    if (!empty($field['date_limit_fixed_max'])) {

      $max = explode('-', $field['date_limit_fixed_max']);

      $field['single_max_yy'] = $max[0];
      $field['single_max_mm'] = $max[1];
      $field['single_max_dd'] = $max[2];
    }

    return array_intersect_key($field, array_flip($this->old_args));
  }

}
