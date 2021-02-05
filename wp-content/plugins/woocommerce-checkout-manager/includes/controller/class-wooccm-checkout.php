<?php

class WOOCCM_Checkout_Controller
{

  protected static $_instance;

  public function __construct()
  {
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('wooccm_sections_header', array($this, 'add_header'));
    add_action('woocommerce_sections_' . WOOCCM_PREFIX, array($this, 'add_section'), 99);
    add_action('woocommerce_settings_save_' . WOOCCM_PREFIX, array($this, 'save_settings'));

    // Frontend
    // -----------------------------------------------------------------------
    add_action('woocommerce_before_checkout_form', array($this, 'add_inline_scripts'));
    add_action('woocommerce_checkout_fields', array($this, 'order_notes'));
    add_action('woocommerce_before_checkout_form', array($this, 'add_checkout_form_before_message'));
    add_action('woocommerce_after_checkout_form', array($this, 'add_checkout_form_after_message'));
    add_action('woocommerce_enable_order_notes_field', array($this, 'remove_order_notes'));

    // Compatibility
    // -----------------------------------------------------------------------
    add_filter('default_option_wooccm_checkout_force_shipping_address', array($this, 'additional_info'));
    add_filter('default_option_wooccm_checkout_force_create_account', array($this, 'auto_create_wccm_account'));
    add_filter('default_option_wooccm_checkout_remove_order_notes', array($this, 'notesenable'));
    add_filter('default_option_wooccm_checkout_order_notes_label', array($this, 'noteslabel'));
    add_filter('default_option_wooccm_checkout_order_notes_placeholder', array($this, 'notesplaceholder'));
    add_filter('default_option_wooccm_checkout_checkout_form_before_message', array($this, 'text1'));
    add_filter('default_option_wooccm_checkout_checkout_form_after_message', array($this, 'text2'));
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function enqueue_scripts()
  {

    if (is_checkout() || is_account_page()) {

      WOOCCM()->register_scripts();

      $i18n = substr(get_user_locale(), 0, 2);

      wp_enqueue_style('wooccm');

      // Colorpicker
      // ---------------------------------------------------------------------
      wp_enqueue_style('wp-color-picker');
      wp_enqueue_script('wp-color-picker');

      // Farbtastic
      // ---------------------------------------------------------------------
      wp_enqueue_style('farbtastic');
      wp_enqueue_script('farbtastic');

      // Dashicons
      // ---------------------------------------------------------------------
      wp_enqueue_style('dashicons');


      // Checkout
      // ---------------------------------------------------------------------
      wp_enqueue_script('wooccm-checkout');
    }
  }

  public function add_inline_scripts()
  {

    if (get_option('wooccm_checkout_force_shipping_address', 'no') == 'yes') {
?>
      <style>
        .woocommerce-shipping-fields h3:first-child input {
          display: none !important;
        }

        .woocommerce-shipping-fields .shipping_address {
          display: block !important;
        }
      </style>
    <?php
    }

    if (get_option('wooccm_checkout_force_create_account', 'no') == 'yes') {
    ?>
      <style>
        div.create-account {
          display: block !important;
        }

        p.create-account {
          display: none !important;
        }
      </style>
      <script>
        jQuery(document).ready(function(e) {
          jQuery("input#createaccount").prop('checked', 'checked');
        });
      </script>
    <?php
    }
  }

  // Frontend
  // -------------------------------------------------------------------------
  public function order_notes($fields)
  {

    $options = get_option('wccs_settings');

    if ($label = get_option('wooccm_checkout_order_notes_label', false)) {
      $fields['order']['order_comments']['label'] = $label;
    }

    if ($placeholder = get_option('wooccm_checkout_order_notes_placeholder', false)) {
      $fields['order']['order_comments']['placeholder'] = $placeholder;
    }

    if (get_option('wooccm_checkout_remove_order_notes', 'no') === 'yes') {
      unset($fields['order']['order_comments']);
    }

    return $fields;
  }

  public function remove_order_notes($value)
  {

    if (get_option('wooccm_checkout_remove_order_notes', 'no') === 'yes') {
      return false;
    }

    return $value;
  }

  function add_checkout_form_before_message($param)
  {

    if ($text = get_option('wooccm_checkout_checkout_form_before_message', false)) {

      wc_get_template('notices/notice.php', array(
        'messages' => array_filter((array) $text),
        'notices' => array(0 =>  array(
          'notice' => $text
        ))
      ));
    }
  }

  function add_checkout_form_after_message($param)
  {

    if ($text = get_option('wooccm_checkout_checkout_form_after_message', false)) {

      wc_get_template('notices/notice.php', array(
        'messages' => array_filter((array) $text),
        'notices' => array(0 =>  array(
          'notice' => $text
        ))
      ));
    }
  }

  // Admin    
  // ---------------------------------------------------------------------------

  public function get_settings()
  {

    return array(
      array(
        'type' => 'title',
        'id' => 'section_title'
      ),
      array(
        'name' => esc_html__('Force shipping address', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Force show shipping checkout fields.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_force_shipping_address',
        'type' => 'select',
        'class' => 'chosen_select',
        'options' => array(
          'yes' => esc_html__('Yes', 'woocommerce-checkout-manager'),
          'no' => esc_html__('No', 'woocommerce-checkout-manager'),
        ),
        'default' => 'no',
      ),
      array(
        'name' => esc_html__('Force create an account', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Force create an account for guests users.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_force_create_account',
        'type' => 'select',
        'class' => 'chosen_select',
        'options' => array(
          'yes' => esc_html__('Yes', 'woocommerce-checkout-manager'),
          'no' => esc_html__('No', 'woocommerce-checkout-manager'),
        ),
        'default' => 'no',
      ),
      array(
        'name' => esc_html__('Remove order notes', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Remove order notes from checkout page.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_remove_order_notes',
        'type' => 'select',
        'class' => 'chosen_select',
        'options' => array(
          'yes' => esc_html__('Yes', 'woocommerce-checkout-manager'),
          'no' => esc_html__('No', 'woocommerce-checkout-manager'),
        ),
        'default' => 'no',
      ),
      array(
        'name' => esc_html__('Order notes label', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_order_notes_label',
        'type' => 'text',
        'placeholder' => esc_attr__('Order notes', 'woocommerce-checkout-manager'),
      ),
      array(
        'name' => esc_html__('Order notes placeholder', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_order_notes_placeholder',
        'type' => 'text',
        'placeholder' => esc_attr__('Notes about your order, e.g. special notes for delivery.', 'woocommerce-checkout-manager'),
      ),
      array(
        'name' => esc_html__('Add message before checkout', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_checkout_form_before_message',
        'type' => 'textarea',
        'placeholder' => ''
      ),
      array(
        'name' => esc_html__('Add message after checkout', 'woocommerce-checkout-manager'),
        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
        'id' => 'wooccm_checkout_checkout_form_after_message',
        'type' => 'textarea',
        'placeholder' => ''
      ),
      // thankyou
      // -------------------------------------------------------------------------
      //array(
      //        'name' => esc_html__('Add thankyou custom fields', 'woocommerce-checkout-manager'),
      //        'desc_tip' => esc_html__('Show the selected fields in the thankyou page.', 'woocommerce-checkout-manager'),
      //        'id' => 'wooccm_checkout_thankyou_custom_fields',
      //        'type' => 'select',
      //        'class' => 'chosen_select',
      //        'options' => array(
      //            'yes' => esc_html__('Yes', 'woocommerce-checkout-manager'),
      //            'no' => esc_html__('No', 'woocommerce-checkout-manager'),
      //        ),
      //        'default' => 'no',
      //    ),
      //array(
      //        'name' => esc_html__('Add thankyou custom fields title', 'woocommerce-checkout-manager'),
      //        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
      //        'id' => 'wooccm_checkout_thankyou_custom_fields_text',
      //        'type' => 'text',
      //        'placeholder' => esc_html__('Checkout extra', 'woocommerce-checkout-manager')
      //    ),
      // upload
      // -------------------------------------------------------------------------
      //array(
      //        'name' => esc_html__('Add upload files limit', 'woocommerce-checkout-manager'),
      //        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
      //        'id' => 'wooccm_checkout_upload_files_limit',
      //        'type' => 'number',
      //        'placeholder' => 4
      //    ),
      //array(
      //        'name' => esc_html__('Add upload files types', 'woocommerce-checkout-manager'),
      //        'desc_tip' => esc_html__('Add custom title for the custom fields table in the thankyou page.', 'woocommerce-checkout-manager'),
      //        'id' => 'wooccm_checkout_upload_files_types',
      //        'type' => 'text',
      //        'placeholder' => 'jpg,gif,png'
      //    ),
      array(
        'type' => 'sectionend',
        'id' => 'section_end'
      )
    );
  }

  public function add_header()
  {
    global $current_section;
    ?>
    <li><a href="<?php echo admin_url('admin.php?page=wc-settings&tab=wooccm&section'); ?>" class="<?php echo ($current_section == '' ? 'current' : ''); ?>"><?php esc_html_e('Checkout', 'woocommerce-checkout-manager'); ?></a> | </li>
<?php
  }

  public function add_section()
  {

    global $current_section;

    if ('' == $current_section) {

      $settings = $this->get_settings();

      include_once(WOOCCM_PLUGIN_DIR . 'includes/view/backend/pages/checkout.php');
    }
  }

  public function save_settings()
  {

    global $current_section;

    if ('' == $current_section) {

      woocommerce_update_options($this->get_settings());
    }
  }

  // Compatibility
  // ---------------------------------------------------------------------------

  public function additional_info($value)
  {

    $options = get_option('wccs_settings');

    if (!empty($options['checkness']['additional_info'])) {
      return 'yes';
    }

    if (!empty($options['checkness']['show_shipping_fields'])) {
      return 'yes';
    }

    return $value;
  }

  public function auto_create_wccm_account($value)
  {

    $options = get_option('wccs_settings');

    if (!empty($options['checkness']['auto_create_wccm_account'])) {
      return 'yes';
    }

    return $value;
  }

  public function notesenable($value)
  {

    $options = get_option('wccs_settings');

    if (!empty($options['checkness']['notesenable'])) {
      return 'yes';
    }

    return $value;
  }

  public function noteslabel($value)
  {

    $options = get_option('wccs_settings');

    if ($text = @$options['checkness']['noteslabel']) {
      return $text;
    }

    return $value;
  }

  public function notesplaceholder($value)
  {

    $options = get_option('wccs_settings');

    if ($text = @$options['checkness']['notesplaceholder']) {
      return $text;
    }

    return $value;
  }

  public function text1($value)
  {

    $options = get_option('wccs_settings');

    if ($text = @$options['checkness']['text1']) {
      return $text;
    }

    return $value;
  }

  public function text2($value)
  {

    $options = get_option('wccs_settings');

    if ($text = @$options['checkness']['text2']) {
      return $text;
    }

    return $value;
  }
}

WOOCCM_Checkout_Controller::instance();
