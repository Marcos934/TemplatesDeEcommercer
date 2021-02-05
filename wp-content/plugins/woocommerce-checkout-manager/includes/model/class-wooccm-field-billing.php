<?php

if (!class_exists('WOOCCM_Field_Billing')) {

  include_once( WOOCCM_PLUGIN_DIR . 'includes/model/class-wooccm-field.php' );

  class WOOCCM_Field_Billing extends WOOCCM_Field {

    protected static $_instance;
    //protected $fields = array();
    protected $prefix = 'billing';
    protected $table = 'wooccm_billing';
    protected $defaults = array(
        'country',
        'first_name',
        'last_name',
        'company',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'email',
        'phone'
    );

    public static function instance() {
      if (is_null(self::$_instance)) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }

  }

}
