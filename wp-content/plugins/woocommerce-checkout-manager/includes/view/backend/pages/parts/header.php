<div class="wrap about-wrap full-width-layout">

  <h1><?php esc_html_e('Suggestions', 'woocommerce-checkout-manager'); ?></h1>

  <p class="about-text"><?php printf(esc_html__('Thanks for using our product! We recommend these extensions that will add new features to stand out your business and improve your sales.', 'woocommerce-checkout-manager'), WOOCCM_PLUGIN_NAME); ?></p>

  <p class="about-text">
    <?php printf('<a href="%s" target="_blank">%s</a>', WOOCCM_PURCHASE_URL, esc_html__('Purchase', 'woocommerce-checkout-manager')); ?></a> |
    <?php printf('<a href="%s" target="_blank">%s</a>', WOOCCM_DOCUMENTATION_URL, esc_html__('Documentation', 'woocommerce-checkout-manager')); ?></a>
  </p>

  <?php printf('<a href="%s" target="_blank"><div style="
               background: #006bff url(%s) no-repeat;
               background-position: top center;
               background-size: 130px 130px;
               color: #fff;
               font-size: 14px;
               text-align: center;
               font-weight: 600;
               margin: 5px 0 0;
               padding-top: 120px;
               height: 40px;
               display: inline-block;
               width: 140px;
               " class="wp-badge">%s</div></a>', 'https://quadlayers.com/?utm_source=wooccm_admin', plugins_url('/assets/backend/img/quadlayers.jpg', WOOCCM_PLUGIN_FILE), esc_html__('QuadLayers', 'woocommerce-checkout-manager')); ?>

</div>

<?php
if (isset($GLOBALS['submenu'][WOOCCM_PREFIX])) {
  if (is_array($GLOBALS['submenu'][WOOCCM_PREFIX])) {
?>
    <div class="wrap about-wrap full-width-layout qlwrap">
      <h2 class="nav-tab-wrapper">
        <?php
        foreach ($GLOBALS['submenu'][WOOCCM_PREFIX] as $tab) {
          if (strpos($tab[2], '.php') !== false)
            continue;
        ?>
          <a href="<?php echo admin_url('admin.php?page=' . esc_attr($tab[2])); ?>" class="nav-tab<?php echo (isset($_GET['page']) && $_GET['page'] == $tab[2]) ? ' nav-tab-active' : ''; ?>"><?php echo $tab[0]; ?></a>
        <?php
        }
        ?>
      </h2>
    </div>
<?php
  }
}
