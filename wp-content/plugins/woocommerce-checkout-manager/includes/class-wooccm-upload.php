<?php

class WOOCCM_Upload
{

  protected static $_instance;

  public function __construct()
  {
    add_action('wp_ajax_wooccm_order_attachment_update', array($this, 'ajax_delete_attachment'));
    add_action('wp_ajax_nopriv_wooccm_order_attachment_update', array($this, 'ajax_delete_attachment'));

    // Checkout
    // -----------------------------------------------------------------------
    add_action('wp_ajax_wooccm_checkout_attachment_upload', array($this, 'ajax_checkout_attachment_upload'));
    add_action('wp_ajax_nopriv_wooccm_checkout_attachment_upload', array($this, 'ajax_checkout_attachment_upload'));
    add_action('woocommerce_checkout_update_order_meta', array($this, 'update_attachment_ids'), 99);
    //  }
  }

  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  protected function process_uploads($files, $key, $post_id = 0)
  {

    if (!function_exists('media_handle_upload')) {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

    $attachment_ids = array();

    add_filter('upload_dir', function ($param) {
      $param['path'] = sprintf('%s/wooccm_uploads', $param['basedir']);
      $param['url'] = sprintf('%s/wooccm_uploads', $param['baseurl']);
      return $param;
    }, 10);

    foreach ($files['name'] as $id => $value) {

      if ($files['name'][$id]) {

        $_FILES[$key] = array(
          'name' => $files['name'][$id],
          'type' => $files['type'][$id],
          'tmp_name' => $files['tmp_name'][$id],
          'error' => $files['error'][$id],
          'size' => $files['size'][$id]
        );

        if (!is_wp_error($attachment_id = media_handle_upload($key, $post_id))) {
          $attachment_ids[] = $attachment_id;
        } else {
          wc_add_notice($attachment_id->get_error_message(), 'error');
          wp_send_json_error($attachment_id->get_error_message());
        }
      }
    }

    return $attachment_ids;
  }

  public function ajax_delete_attachment()
  {

    if (!empty($_REQUEST) && check_admin_referer('wooccm_upload', 'nonce')) {

      $array1 = explode(',', sanitize_text_field(isset($_REQUEST['all_attachments_ids']) ? $_REQUEST['all_attachments_ids'] : ''));
      $array2 = explode(',', sanitize_text_field(isset($_REQUEST['delete_attachments_ids']) ? $_REQUEST['delete_attachments_ids'] : ''));

      if (empty($array1) || empty($array2)) {
        wp_send_json_error(esc_html__('No attachment selected.', 'woocommerce-checkout-manager'));
      }

      $attachment_ids = array_diff($array1, $array2);

      if (!empty($attachment_ids)) {

        foreach ($attachment_ids as $key => $attachtoremove) {

          // Check the Attachment exists...
          if (get_post_status($attachtoremove) == false)
            continue;

          // Check the Attachment is associated with an Order
          $post_parent = get_post_field('post_parent', $attachtoremove);

          if (empty($post_parent)) {
            continue;
          } else {
            if (get_post_type($post_parent) <> 'shop_order')
              continue;
          }
          wp_delete_attachment($attachtoremove);
        }
      }

      wp_send_json_success('Deleted successfully.', 'woocommerce-checkout-manager');
    }
  }

  public function ajax_checkout_attachment_upload()
  {

    if (check_admin_referer('wooccm_upload', 'nonce') && isset($_FILES['wooccm_checkout_attachment_upload'])) {

      $files = $_FILES['wooccm_checkout_attachment_upload'];

      if (empty($files)) {
        wc_add_notice(esc_html__('No uploads were recognised. Files were not uploaded.', 'woocommerce-checkout-manager'), 'error');
        wp_send_json_error();
      }

      if (count($attachment_ids = $this->process_uploads($files, 'wooccm_checkout_attachment_upload'))) {
        wp_send_json_success($attachment_ids);
      }
      wc_add_notice(esc_html__('Unknow error.', 'woocommerce-checkout-manager'), 'error');
      wp_send_json_error();
    }
  }

  public function update_attachment_ids($order_id = 0)
  {

    require_once(ABSPATH . 'wp-admin/includes/image.php');

    if (count($checkout = WC()->checkout->get_checkout_fields())) {

      foreach ($checkout as $field_type => $fields) {

        foreach ($fields as $key => $field) {

          if (isset($field['type']) && $field['type'] == 'file') {

            if ($attachments = get_post_meta($order_id, sprintf('_%s', $key), true)) {

              if ($attachments = (array) explode(',', $attachments)) {

                foreach ($attachments as $image_id) {

                  wp_update_post(array('ID' => $image_id, 'post_parent' => $order_id));

                  wp_update_attachment_metadata($image_id, wp_generate_attachment_metadata($image_id, get_attached_file($image_id)));
                }
              }
            }
          }
        }
      }
    }
  }
}

WOOCCM_Upload::instance();
