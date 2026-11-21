<?php
  function demo_app(){
    return false;
  }
  function app_version(){
    return 2.91;
  }
  function required_php_version(){
    return 7.4;
  }

  function string_to_number($number=0)
  {
    return str_replace(",", "", $number);
  }
  function app_token(){
    return 'rwo1e4lncf8tsmixpzy305va9dkj2g';
  }

  function smtp(){
    if(special_access()){
      return true;//true or false to activate
    }
  }

  function app_front_tag_line(){
    $site_rec = get_site_details();
    return '<div class="col-xl-8 col-lg-7 col-md-12 bg" style="background-color: #001cb0">
                <div class="info">
                    <h1>'.$site_rec->site_name.'</h1>
                    <p>POS, Inventory, Accounting, Multi Warehouses, Multi User</p>
                </div>
            </div>';
  }
  function store_demo_logo(){
    return 'uploads/no_logo/yourlogo.png';
  }
  function get_site_logo(){
    $CI =& get_instance();
    return $CI->db->query("select logo from db_sitesettings")->row()->logo;
  }
  function sql_mode(){
    $CI =& get_instance();
    $q1 = $CI->db->query("SELECT @@sql_mode AS sql_mode")->row();
    return $q1->sql_mode;
  }
  function is_sql_full_group_by_enabled(){
    $sql_mode = sql_mode();
    $sql_mode = strtoupper($sql_mode);

    $mode = 'ONLY_FULL_GROUP_BY';
    return (strpos($sql_mode, $mode) !== false) ? show_sql_mode_page() : false;
  }

  function show_sql_mode_page(){
    $CI =& get_instance();
    if(!$CI->db->query(" SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))")){
      show_error("Please make sure your database should not be enabled with SQL_FULL_GROUP_BY, For More information Click on Given link: <a href='".base_url()."/help/#full_group_by' target='_blank'>Click here to check!</a>(Full Group By Check)", 403, $heading = "SQL_FULL_GROUP_BY ENABLED!!");
    }else{
      return true;
    }
  }
  
  function decimals(){
    $CI =& get_instance();
    return $CI->session->userdata('decimals');
  }

  function sales_invoice_currency_code($store_id='') {
    $CI =& get_instance();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $currency = $CI->db->select('c.currency_code')->from('db_store s')
      ->join('db_currency c', 'c.id=s.currency_id', 'left')->where('s.id', $store_id)->get()->row();
    return !empty($currency->currency_code) ? trim($currency->currency_code) : '';
  }
  function store_number_format($value=0,$comma=true){
    return ($comma) ? number_format($value,decimals()) : number_format($value,decimals(),".","");
  }

  function round_off_amount($value=0){
    return is_enabled_round_off() ? round((float)$value) : (float)$value;
  }

  function store_total_format($value=0,$comma=true){
    return store_number_format(round_off_amount($value),$comma);
  }

  function show_number_to_words_sales(){
    $store = get_store_details();
    return !isset($store->number_to_words) || $store->number_to_words === 'Yes';
  }

  function show_number_to_words_pos(){
    $store = get_store_details();
    return !isset($store->number_to_words_pos) || $store->number_to_words_pos === 'Yes';
  }

  function qty_decimal(){
    $CI =& get_instance();
    return $CI->session->userdata('qty_decimals');
  }

  function format_qty($value){
    return number_format($value,qty_decimal(),".","");
  }

  function system_fromatted_date($date=''){
  $CI =& get_instance();
    if ($CI->session->userdata('view_date')=='dd/mm/yyyy') {
      return date('Y-m-d',strtotime(str_replace('/', '-', $date)));
    }
    elseif($CI->session->userdata('view_date')=='mm/dd/yyyy'){
      return date("Y-m-d",strtotime($date));
    }
    else{
      return date("Y-m-d",strtotime($date));
    }
  }
	function show_date($date=''){
	$CI =& get_instance();
    if ($CI->session->userdata('view_date')=='dd/mm/yyyy') {
      return date('d/m/Y',strtotime(str_replace('/', '-', $date)));
    }
    elseif($CI->session->userdata('view_date')=='mm/dd/yyyy'){
      return date("m/d/Y",strtotime($date));
    }
    else{
      return date("d-m-Y",strtotime($date));
    }
  }
  function show_time($time=''){
    if(empty($time)){
      return $time;
    }
    $CI =& get_instance();
    if($CI->session->userdata('view_time')=='24') {
      return date('h:i',strtotime($time));
    }
    else{
      return date('h:i a',strtotime($time));
    }
  }

  function return_item_image_thumb($path=''){
    return str_replace(".", "_thumb.", $path);
  }

  /*Find the change return show in pos or not*/
  function change_return_status(){
    $CI =& get_instance();
    return $CI->db->select('change_return')->where("id",get_current_store_id())->get('db_store')->row()->change_return;
  }

  function get_change_return_amount($sales_id){
    $CI =& get_instance();
    return $CI->db->select('coalesce(sum(change_return),0) as change_return_amount')->where('sales_id',$sales_id)->get('db_salespayments')->row()->change_return_amount;
  }

  function get_invoice_format_id(){
    $CI =& get_instance();
    return $CI->db->select('sales_invoice_format_id')->where('id',get_current_store_id())->get('db_store')->row()->sales_invoice_format_id;
  }
  function get_pos_invoice_format_id(){
    $CI =& get_instance();
    return $CI->db->select('pos_invoice_format_id')->where('id',get_current_store_id())->get('db_store')->row()->pos_invoice_format_id;
  }
  function is_enabled_round_off(){
    $CI =& get_instance();
    $round_off=$CI->db->select('round_off')->where('id',get_current_store_id())->get('db_store')->row()->round_off;
    if($round_off==1){
      return true;
    }
    return false;
  }
  function is_negative_stock_allowed($store_id=null){
    $CI =& get_instance();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    if(!$CI->db->field_exists('allow_negative_stock', 'db_store')){
      return false;
    }
    $row = $CI->db->select('allow_negative_stock')->where('id',$store_id)->get('db_store')->row();
    return $row && (int)$row->allow_negative_stock === 1;
  }

  /**
   * Ensure sales.customer_trn_no column exists.
   */
  function ensure_sales_customer_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('customer_trn_no', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `customer_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * TASK-18 & TASK-19: Ensure sales table has invoice_type, payment_type, card_type, customer_mobile, customer_email
   */
  function ensure_sales_task18_fields(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('invoice_type', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `invoice_type` VARCHAR(50) NULL DEFAULT 'Cash'");
    }
    if(!$CI->db->field_exists('payment_type', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `payment_type` VARCHAR(50) NULL DEFAULT 'Cash'");
    }
    if(!$CI->db->field_exists('card_type', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `card_type` VARCHAR(50) NULL DEFAULT NULL");
    }
    if(!$CI->db->field_exists('customer_mobile', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `customer_mobile` VARCHAR(50) NULL DEFAULT NULL");
    }
    if(!$CI->db->field_exists('customer_email', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `customer_email` VARCHAR(100) NULL DEFAULT NULL");
    }
    if(!$CI->db->field_exists('payment_terms', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `payment_terms` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /** TASK-24: Ensure sales invoices have their own address field. */
  function ensure_sales_task24_fields(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('address', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `address` VARCHAR(500) NULL DEFAULT NULL");
    }
  }

  /** TASK-27: WhatsApp No on sales invoice */
  function ensure_sales_task27_fields(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('whatsapp_no', 'db_sales')){
      $CI->db->query("ALTER TABLE `db_sales` ADD `whatsapp_no` VARCHAR(50) NULL DEFAULT NULL");
    }
  }

  /**
   * Ensure sales return.customer_trn_no column exists.
   */
  function ensure_sales_return_customer_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('customer_trn_no', 'db_salesreturn')){
      $CI->db->query("ALTER TABLE `db_salesreturn` ADD `customer_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * Ensure quotation.customer_trn_no column exists.
   */
  function ensure_quotation_customer_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('customer_trn_no', 'db_quotation')){
      $CI->db->query("ALTER TABLE `db_quotation` ADD `customer_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * Ensure delivery note.customer_trn_no column exists.
   */
  function ensure_deliverynote_customer_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('customer_trn_no', 'db_deliverynote')){
      $CI->db->query("ALTER TABLE `db_deliverynote` ADD `customer_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * Resolve customer TRN (tax_number / gstin).
   */
  function get_customer_trn_no($customer_id=''){
    $CI =& get_instance();
    $customer_id = (int)$customer_id;
    if($customer_id <= 0){
      return '';
    }
    $row = $CI->db->select('tax_number,gstin')->where('id', $customer_id)->get('db_customers')->row();
    if(empty($row)){
      return '';
    }
    if(!empty($row->tax_number)){
      return $row->tax_number;
    }
    if(!empty($row->gstin)){
      return $row->gstin;
    }
    return '';
  }

  /**
   * Ensure purchase.supplier_trn_no column exists.
   */
  function ensure_purchase_supplier_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('supplier_trn_no', 'db_purchase')){
      $CI->db->query("ALTER TABLE `db_purchase` ADD `supplier_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * Ensure purchase return.supplier_trn_no column exists.
   */
  function ensure_purchase_return_supplier_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('supplier_trn_no', 'db_purchasereturn')){
      $CI->db->query("ALTER TABLE `db_purchasereturn` ADD `supplier_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * Ensure orders.supplier_trn_no column exists.
   */
  function ensure_order_supplier_trn_no(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('supplier_trn_no', 'db_orders')){
      $CI->db->query("ALTER TABLE `db_orders` ADD `supplier_trn_no` VARCHAR(100) NULL DEFAULT NULL");
    }
  }

  /**
   * Resolve supplier TRN (tax_number / gstin).
   */
  function get_supplier_trn_no($supplier_id=''){
    $CI =& get_instance();
    $supplier_id = (int)$supplier_id;
    if($supplier_id <= 0){
      return '';
    }
    $row = $CI->db->select('tax_number,gstin')->where('id', $supplier_id)->get('db_suppliers')->row();
    if(empty($row)){
      return '';
    }
    if(!empty($row->tax_number)){
      return $row->tax_number;
    }
    if(!empty($row->gstin)){
      return $row->gstin;
    }
    return '';
  }

  /**
   * Ensure PDF part-number popup setting column exists.
   * Default Yes (1) so existing popup behavior continues.
   */
  function ensure_pdf_part_number_popup_setting(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('pdf_part_number_popup', 'db_store')){
      $CI->db->query("ALTER TABLE `db_store` ADD `pdf_part_number_popup` TINYINT(1) NOT NULL DEFAULT 1");
    }
  }

  /**
   * Ensure store location URL column exists (Google Maps / place link for invoice QR).
   */
  function ensure_location_url_setting(){
    static $done = false;
    if($done){
      return;
    }
    $done = true;

    $CI =& get_instance();
    // list_fields() is cached; bust it so a prior call does not miss a newly-added column
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
    if($CI->db->field_exists('location_url', 'db_store')){
      return;
    }
    try {
      $CI->db->query("ALTER TABLE `db_store` ADD `location_url` TEXT NULL");
    } catch (Throwable $e) {
      // Ignore duplicate-column races from stale cache / concurrent requests
    }
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
  }

  /**
   * Ensure WhatsApp number column exists (used to generate WhatsApp QR for invoices).
   */
  function ensure_whatsapp_number_setting(){
    static $done = false;
    if($done){
      return;
    }
    $done = true;

    $CI =& get_instance();
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
    if($CI->db->field_exists('whatsapp_number', 'db_store')){
      return;
    }
    try {
      $CI->db->query("ALTER TABLE `db_store` ADD `whatsapp_number` VARCHAR(50) NULL DEFAULT NULL");
    } catch (Throwable $e) {
      // Ignore duplicate-column races from stale cache / concurrent requests
    }
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
  }

  /**
   * Digits-only WhatsApp number for wa.me links.
   */
  function normalize_whatsapp_number($number){
    return preg_replace('/[^0-9]/', '', (string)$number);
  }

  /**
   * Shorten Google Maps / location URLs for scannable QR codes.
   * Long place URLs become too dense at small PDF size and fail to scan.
   */
  function shorten_location_url_for_qr($url){
    $url = trim(html_entity_decode((string)$url, ENT_QUOTES, 'UTF-8'));
    if($url === ''){
      return '';
    }
    // Prefer precise place coordinates: !3dLAT!4dLNG
    if(preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $m)){
      return 'https://maps.google.com/?q='.$m[1].','.$m[2];
    }
    // Fallback: @lat,lng from maps URL
    if(preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $m)){
      return 'https://maps.google.com/?q='.$m[1].','.$m[2];
    }
    // Drop heavy query string if still very long
    if(strlen($url) > 180){
      $parts = parse_url($url);
      if(!empty($parts['scheme']) && !empty($parts['host'])){
        $path = isset($parts['path']) ? $parts['path'] : '';
        return $parts['scheme'].'://'.$parts['host'].$path;
      }
    }
    return $url;
  }

  /**
   * Location URL for invoice QR: prefer store location_url, else Google Maps from address.
   */
  function get_store_location_url_for_qr($store=null){
    if(empty($store) || !is_object($store)){
      $store = get_store_details();
    }
    if(empty($store)){
      return '';
    }
    if(!empty($store->location_url)){
      $url = shorten_location_url_for_qr($store->location_url);
      if($url !== ''){
        return $url;
      }
      // If shorten failed, still try raw URL
      $raw = trim(html_entity_decode((string)$store->location_url, ENT_QUOTES, 'UTF-8'));
      if($raw !== ''){
        return $raw;
      }
    }
    // Fallback when Location QR is enabled but Location URL field is empty
    $parts = array();
    if(!empty($store->address)){
      $parts[] = trim(html_entity_decode((string)$store->address, ENT_QUOTES, 'UTF-8'));
    }
    foreach(array('city', 'state', 'country') as $field){
      if(!empty($store->$field)){
        $val = trim(html_entity_decode((string)$store->$field, ENT_QUOTES, 'UTF-8'));
        if($val !== ''){
          $parts[] = $val;
        }
      }
    }
    if(empty($parts) && !empty($store->store_name)){
      $parts[] = trim(html_entity_decode((string)$store->store_name, ENT_QUOTES, 'UTF-8'));
    }
    if(empty($parts)){
      return '';
    }
    return 'https://maps.google.com/?q='.rawurlencode(implode(', ', $parts));
  }

  /**
   * Generate WhatsApp QR PNG from phone number and save under uploads/store/.
   * Returns relative path (e.g. uploads/store/wa_qr_1.png) or empty string on failure.
   */
  function generate_store_whatsapp_qr_image($whatsapp_number, $store_id=0){
    $wa_no = normalize_whatsapp_number($whatsapp_number);
    if($wa_no === ''){
      return '';
    }

    $barcode_file = APPPATH.'libraries/tcpdf/tcpdf_barcodes_2d.php';
    if(!file_exists($barcode_file)){
      return '';
    }
    if(!class_exists('TCPDF2DBarcode', false)){
      require_once $barcode_file;
    }

    try {
      $barcode = new TCPDF2DBarcode('https://wa.me/'.$wa_no, 'QRCODE,M');
      $png = $barcode->getBarcodePngData(6, 6);
    } catch (Exception $e) {
      return '';
    }

    if(empty($png)){
      return '';
    }

    $dir = FCPATH.'uploads/store/';
    if(!is_dir($dir)){
      @mkdir($dir, 0755, true);
    }

    $filename = 'wa_qr_'.(int)$store_id.'.png';
    $relative = 'uploads/store/'.$filename;
    if(@file_put_contents($dir.$filename, $png) === false){
      return '';
    }
    return $relative;
  }

  /**
   * Whether PDF Options popup (With/Without Part Number) should show.
   */
  function is_pdf_part_number_popup_enabled($store_id=null){
    $CI =& get_instance();
    ensure_pdf_part_number_popup_setting();
    // Item Label = Barcode No: the Barcode No column always shows on PDFs, so no With/Without popup
    if(get_item_barcode_label_key($store_id) === 'barcode_no'){
      return false;
    }
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $row = $CI->db->select('pdf_part_number_popup')->where('id',$store_id)->get('db_store')->row();
    if(empty($row) || !isset($row->pdf_part_number_popup)){
      return true;
    }
    return (int)$row->pdf_part_number_popup === 1;
  }

  /**
   * Ensure item barcode label setting column exists.
   * Values: part_no | barcode_no (default part_no).
   */
  function ensure_item_barcode_label_setting(){
    static $done = false;
    if($done){
      return;
    }
    $done = true;

    $CI =& get_instance();
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
    if($CI->db->field_exists('item_barcode_label', 'db_store')){
      return;
    }
    try {
      $CI->db->query("ALTER TABLE `db_store` ADD `item_barcode_label` VARCHAR(20) NOT NULL DEFAULT 'part_no'");
    } catch (Throwable $e) {
      // Ignore duplicate-column races
    }
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
  }

  /**
   * Raw setting value: part_no | barcode_no
   */
  function get_item_barcode_label_key($store_id=null){
    $CI =& get_instance();
    ensure_item_barcode_label_setting();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $row = $CI->db->select('item_barcode_label')->where('id',$store_id)->get('db_store')->row();
    if(empty($row) || empty($row->item_barcode_label) || $row->item_barcode_label !== 'barcode_no'){
      return 'part_no';
    }
    return 'barcode_no';
  }

  /**
   * Display label for custom_barcode field (Part No / Barcode No).
   */
  function get_item_barcode_label($store_id=null){
    return (get_item_barcode_label_key($store_id) === 'barcode_no') ? 'Barcode No' : 'Part No';
  }

  /**
   * Ensure invoice declaration text column exists on db_store.
   */
  function ensure_invoice_declaration_setting(){
    static $done = false;
    if($done){
      return;
    }
    $done = true;

    $CI =& get_instance();
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
    if($CI->db->field_exists('invoice_declaration', 'db_store')){
      return;
    }
    $default = "We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct";
    try {
      $CI->db->query("ALTER TABLE `db_store` ADD `invoice_declaration` TEXT NULL");
      $CI->db->query("UPDATE `db_store` SET `invoice_declaration` = ".$CI->db->escape($default)." WHERE `invoice_declaration` IS NULL OR `invoice_declaration` = ''");
    } catch (Throwable $e) {
      // Ignore duplicate-column races
    }
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
  }

  /**
   * Ensure Paid Seal setting column exists. Default No (0).
   */
  function ensure_paid_seal_setting(){
    static $done = false;
    if($done){
      return;
    }
    $done = true;

    $CI =& get_instance();
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
    if($CI->db->field_exists('paid_seal', 'db_store')){
      return;
    }
    try {
      $CI->db->query("ALTER TABLE `db_store` ADD `paid_seal` TINYINT(1) NOT NULL DEFAULT 0");
    } catch (Throwable $e) {
      // Ignore duplicate-column races
    }
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
  }

  /**
   * Whether Paid Seal should show on PDF.
   */
  function is_paid_seal_enabled($store_id=null){
    $CI =& get_instance();
    ensure_paid_seal_setting();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $row = $CI->db->select('paid_seal')->where('id',$store_id)->get('db_store')->row();
    if(empty($row) || !isset($row->paid_seal)){
      return false;
    }
    return (int)$row->paid_seal === 1;
  }

  /**
   * Absolute path to Paid Seal image (prefer transparent/white bg stamp).
   */
  function get_paid_seal_image_path(){
    $candidates = array(
      FCPATH.'uploads/paid2.png',
      FCPATH.'uploads/paid.png',
      FCPATH.'uploads/paid3.jpeg',
    );
    foreach($candidates as $path){
      if(is_file($path)){
        return $path;
      }
    }
    return '';
  }

  /**
   * Whether Paid Seal box should render (setting Yes + Paid status + image exists).
   */
  function should_show_paid_seal($payment_status=null){
    if(!is_paid_seal_enabled()){
      return false;
    }
    if($payment_status !== null && strcasecmp(trim((string)$payment_status), 'Paid') !== 0){
      return false;
    }
    return get_paid_seal_image_path() !== '';
  }

  /**
   * TCPDF-friendly img src for Paid Seal (@base64).
   */
  function get_paid_seal_img_src(){
    $path = get_paid_seal_image_path();
    if($path === ''){
      return '';
    }
    $data = @file_get_contents($path);
    if($data === false || $data === ''){
      return '';
    }
    return '@'.base64_encode($data);
  }

  /**
   * Ensure db_tax.is_default exists (only one default tax % per store).
   */
  function ensure_tax_is_default_column(){
    $CI =& get_instance();
    if(!$CI->db->field_exists('is_default', 'db_tax')){
      $CI->db->query("ALTER TABLE `db_tax` ADD `is_default` TINYINT(1) NOT NULL DEFAULT 0");
    }
  }

  /**
   * Ensure store default item-tax columns exist (Inclusive/Exclusive + tax %).
   */
  function ensure_store_item_tax_defaults(){
    $CI =& get_instance();
    static $done = false;
    if($done){
      return;
    }
    $done = true;
    if(!$CI->db->field_exists('default_tax_type', 'db_store')){
      $CI->db->query("ALTER TABLE `db_store` ADD `default_tax_type` VARCHAR(20) NOT NULL DEFAULT 'Inclusive'");
    } else {
      // Keep system default as Inclusive
      $CI->db->query("ALTER TABLE `db_store` MODIFY `default_tax_type` VARCHAR(20) NOT NULL DEFAULT 'Inclusive'");
      // Empty values -> Inclusive
      $CI->db->group_start()
        ->where('default_tax_type', '')
        ->or_where('default_tax_type IS NULL', null, false)
        ->group_end()
        ->update('db_store', array('default_tax_type' => 'Inclusive'));
    }
    if(!$CI->db->field_exists('default_tax_id', 'db_store')){
      $CI->db->query("ALTER TABLE `db_store` ADD `default_tax_id` INT(11) NULL DEFAULT NULL");
    }
    ensure_tax_is_default_column();
  }

  /**
   * Mark one tax as store default; clears other defaults for that store.
   */
  function set_default_tax_for_store($tax_id, $store_id='', $is_default=1){
    $CI =& get_instance();
    ensure_tax_is_default_column();
    ensure_store_item_tax_defaults();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $tax_id = (int)$tax_id;
    $is_default = ((int)$is_default === 1) ? 1 : 0;

    if($is_default === 1){
      $CI->db->where('store_id', $store_id)->update('db_tax', array('is_default' => 0));
      $CI->db->where('id', $tax_id)->where('store_id', $store_id)->update('db_tax', array('is_default' => 1));
      $CI->db->where('id', $store_id)->update('db_store', array('default_tax_id' => $tax_id));
    } else {
      $CI->db->where('id', $tax_id)->where('store_id', $store_id)->update('db_tax', array('is_default' => 0));
      $store = $CI->db->select('default_tax_id')->where('id', $store_id)->get('db_store')->row();
      if(!empty($store) && (int)$store->default_tax_id === $tax_id){
        $CI->db->where('id', $store_id)->update('db_store', array('default_tax_id' => null));
      }
    }
  }

  /**
   * Store default tax type / tax id for new items.
   * Tax % default comes from Tax module (is_default=1).
   * @return object {default_tax_type, default_tax_id}
   */
  function get_store_item_tax_defaults($store_id=''){
    $CI =& get_instance();
    ensure_store_item_tax_defaults();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $row = $CI->db->select('default_tax_type, default_tax_id')->where('id', $store_id)->get('db_store')->row();
    $defaults = (object) array(
      'default_tax_type' => 'Inclusive',
      'default_tax_id' => '',
    );
    if(!empty($row)){
      if(!empty($row->default_tax_type) && in_array($row->default_tax_type, array('Inclusive','Exclusive'), true)){
        $defaults->default_tax_type = $row->default_tax_type;
      } else {
        // Empty/null -> Inclusive, and persist it
        $CI->db->where('id', $store_id)->update('db_store', array('default_tax_type' => 'Inclusive'));
        $defaults->default_tax_type = 'Inclusive';
      }
    }
    // Prefer Tax module default (only one per store)
    $CI->db->select('id');
    $CI->db->where('store_id', $store_id);
    $CI->db->where('status', 1);
    $CI->db->where('is_default', 1);
    $CI->db->where('group_bit IS NULL', null, false);
    $CI->db->order_by('id', 'asc');
    $CI->db->limit(1);
    $tax_default = $CI->db->get('db_tax')->row();
    if(!empty($tax_default)){
      $defaults->default_tax_id = (int)$tax_default->id;
    } elseif(!empty($row) && !empty($row->default_tax_id)){
      $defaults->default_tax_id = (int)$row->default_tax_id;
    }
    return $defaults;
  }
  function numberTowords($num)
  {
    $CI =& get_instance();
          $ones = array(
          '0'=> $CI->lang->line('Zero'),
          '1'=> $CI->lang->line('One'),
          '2'=> $CI->lang->line('Two') ,
          '3'=> $CI->lang->line('Three') ,
          '4'=> $CI->lang->line('Four') ,
          '5'=> $CI->lang->line('Five') ,
          '6'=> $CI->lang->line('Six') ,
          '7'=> $CI->lang->line('Seven') ,
          '8'=> $CI->lang->line('Eight') ,
          '9'=> $CI->lang->line('Nine') ,
          '10'=> $CI->lang->line('Ten') ,
          '11'=> $CI->lang->line('Eleven') ,
          '12'=> $CI->lang->line('Twelve') ,
          '13'=> $CI->lang->line('Thirteen') ,
          '14'=> $CI->lang->line('Fouteen') ,
          '15'=> $CI->lang->line('Fifteen') ,
          '16'=> $CI->lang->line('Sixteen') ,
          '17'=> $CI->lang->line('Seventeen') ,
          '18'=> $CI->lang->line('Eighteen') ,
          '19'=> $CI->lang->line('Nineteen') ,
          "014" => "FOURTEEN"
          );
          $tens = array( 
          '0'=> $CI->lang->line('Zero'),
          '1'=> $CI->lang->line('Ten') ,
          '2'=> $CI->lang->line('Twenty') ,
          '3'=> $CI->lang->line('Thirty') ,
          '4'=> $CI->lang->line('Fourty') ,
          '5'=> $CI->lang->line('Fifty') ,
          '6'=> $CI->lang->line('Sixty') ,
          '7'=> $CI->lang->line('Seventy') ,
          '8'=> $CI->lang->line('Eighty') ,
          '9'=> $CI->lang->line('Ninty') ,
          ); 
          $hundreds = array( 
          $CI->lang->line('Hundred'),
          $CI->lang->line('Thousand') ,
          $CI->lang->line('Million') ,
          $CI->lang->line('Billion') ,
          $CI->lang->line('Trillion') ,
          $CI->lang->line('Quadrillion') ,
          ); /*limit t quadrillion */

            $num = number_format($num,2,".",","); 
            $num_arr = explode(".",$num); 
            $wholenum = $num_arr[0]; 
            $decnum = $num_arr[1]; 
            $whole_arr = array_reverse(explode(",",$wholenum)); 
            krsort($whole_arr,1); 
            $rettxt = ""; 

  foreach($whole_arr as $key => $i){
    
          while(substr($i,0,1)=="0")
              $i=substr($i,1,5);
         
                if($i < 20){  
                  if(isset($ones[$i])){
                    $rettxt .= $ones[$i]; 
                  }
                  }elseif($i < 100){ 
                    if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
                    if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
                  }else{ 
                    if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
                    if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
                    if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
                  } 
                  if($key > 0){ 
                    $rettxt .= " ".$hundreds[$key]." "; 
                  }
  }//foreach

      if($decnum > 0){
            $rettxt .= " and ";
          if($decnum < 20){
            $rettxt .= $ones[$decnum];
          }elseif($decnum < 100){
            $rettxt .= $tens[substr($decnum,0,1)];
            $rettxt .= " ".$ones[substr($decnum,1,1)];
          }
      }
      return $rettxt;
  }//function end

  /******************************************/
  function convert_number($number) 
    {
        if (($number < 0) || ($number > 999999999)) 
        {
            throw new Exception("Number is out of range");
        }
        $giga = floor($number / 1000000);
        // Millions (giga)
        $number -= $giga * 1000000;
        $kilo = floor($number / 1000);
        // Thousands (kilo)
        $number -= $kilo * 1000;
        $hecto = floor($number / 100);
        // Hundreds (hecto)
        $number -= $hecto * 100;
        $deca = floor($number / 10);
        // Tens (deca)
        $n = $number % 10;
        // Ones
        $result = "";
        if ($giga) 
        {
            $result .= convert_number($giga) .  "Million";
        }
        if ($kilo) 
        {
            $result .= (empty($result) ? "" : " ") .convert_number($kilo) . " Thousand";
        }
        if ($hecto) 
        {
            $result .= (empty($result) ? "" : " ") .convert_number($hecto) . " Hundred";
        }
        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
        if ($deca || $n) {
            if (!empty($result)) 
            {
                $result .= " and ";
            }
            if ($deca < 2) 
            {
                $result .= $ones[$deca * 10 + $n];
            } else {
                $result .= $tens[$deca];
                if ($n) 
                {
                    $result .= "-" . $ones[$n];
                }
            }
        }
        if (empty($result)) 
        {
            $result = "zero";
        }
        return $result;
    }
  /******************************************/

  function no_to_words($no){ 
    $number_to_words_format = 'Default';
    
    if($number_to_words_format=='Indian'){
      return indianCurrency($no);
    }
    else{
      return convert_number($no);
    }


    $CI =& get_instance();
     $words = array('0'=> '' ,
                    '1'=> $CI->lang->line('One'),
                    '2'=> $CI->lang->line('Two') ,
                    '3'=> $CI->lang->line('Three') ,
                    '4'=> $CI->lang->line('Four') ,
                    '5'=> $CI->lang->line('Five') ,
                    '6'=> $CI->lang->line('Six') ,
                    '7'=> $CI->lang->line('Seven') ,
                    '8'=> $CI->lang->line('Eight') ,
                    '9'=> $CI->lang->line('Nine') ,
                    '10'=> $CI->lang->line('Ten') ,
                    '11'=> $CI->lang->line('Eleven') ,
                    '12'=> $CI->lang->line('Twelve') ,
                    '13'=> $CI->lang->line('Thirteen') ,
                    '14'=> $CI->lang->line('Fourteen') ,
                    '15'=> $CI->lang->line('Fifteen') ,
                    '16'=> $CI->lang->line('Sixteen') ,
                    '17'=> $CI->lang->line('Seventeen') ,
                    '18'=> $CI->lang->line('Eighteen') ,
                    '19'=> $CI->lang->line('Nineteen') ,
                    '20'=> $CI->lang->line('Twenty') ,
                    '30'=> $CI->lang->line('Thirty') ,
                    '40'=> $CI->lang->line('Fourty') ,
                    '50'=> $CI->lang->line('Fifty') ,
                    '60'=> $CI->lang->line('Sixty') ,
                    '70'=> $CI->lang->line('Seventy') ,
                    '80'=> $CI->lang->line('Eighty') ,
                    '90'=> $CI->lang->line('Ninty') ,
                    '100'=> $CI->lang->line('Hundred &') ,
                    '1000'=> $CI->lang->line('Thousand') ,
                    '100000'=> $CI->lang->line('Lakh') ,
                    '10000000'=> $CI->lang->line('Crore') ,
                  );
      if($no == 0)
        return ' ';
      else {
      $novalue='';
      $highno=$no;
      $remainno=0;
      $value=100;
      $value1=1000;       
          while($no>=100)    {
            if(($value <= $no) &&($no  < $value1))    {
            $novalue=$words["$value"];
            $highno = (int)($no/$value);
            $remainno = $no % $value;
            break;
            }
            $value= $value1;
            $value1 = $value * 100;
          }       
          if(array_key_exists("$highno",$words))
            return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
          else {
           $unit=$highno%10;
           $ten =(int)($highno/10)*10;            
           return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
           }
      }
  }

  /**
   * Convert number to Arabic words (Tafqeet)
   */
  if (!function_exists('no_to_words_arabic')) {
  function no_to_words_arabic($number, $currency = 'ريال سعودي', $fraction = 'هللة') {
    if ($number == 0) {
      return 'صفر ' . $currency;
    }

    $number = (float)$number;
    $parts = explode('.', number_format($number, 2, '.', ''));
    $whole = (int)$parts[0];
    $decimal = isset($parts[1]) ? (int)$parts[1] : 0;

    $convert_group = function($n) {
      $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
      $tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
      $hundreds = ['', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

      $str = '';
      if ($n >= 100) {
        $h = (int)($n / 100);
        $str .= $hundreds[$h];
        $n %= 100;
        if ($n > 0) $str .= ' و ';
      }

      if ($n > 0) {
        if ($n < 20) {
          $str .= $ones[$n];
        } else {
          $u = $n % 10;
          $t = (int)($n / 10);
          if ($u > 0) {
            $str .= $ones[$u] . ' و ' . $tens[$t];
          } else {
            $str .= $tens[$t];
          }
        }
      }
      return $str;
    };

    $result = '';
    if ($whole >= 1000000000) {
      $b = (int)($whole / 1000000000);
      $result .= $convert_group($b) . ' مليار ';
      $whole %= 1000000000;
      if ($whole > 0) $result .= ' و ';
    }
    if ($whole >= 1000000) {
      $m = (int)($whole / 1000000);
      if ($m == 1) {
        $result .= 'مليون ';
      } elseif ($m == 2) {
        $result .= 'مليونان ';
      } elseif ($m >= 3 && $m <= 10) {
        $result .= $convert_group($m) . ' ملايين ';
      } else {
        $result .= $convert_group($m) . ' مليون ';
      }
      $whole %= 1000000;
      if ($whole > 0) $result .= ' و ';
    }
    if ($whole >= 1000) {
      $th = (int)($whole / 1000);
      if ($th == 1) {
        $result .= 'ألف ';
      } elseif ($th == 2) {
        $result .= 'ألفان ';
      } elseif ($th >= 3 && $th <= 10) {
        $result .= $convert_group($th) . ' آلاف ';
      } else {
        $result .= $convert_group($th) . ' ألف ';
      }
      $whole %= 1000;
      if ($whole > 0) $result .= ' و ';
    }
    if ($whole > 0) {
      $result .= $convert_group($whole);
    }

    $final = trim($result) . ' ' . $currency;
    if ($decimal > 0) {
      $final .= ' و ' . $convert_group($decimal) . ' ' . $fraction;
    }
    return $final;
  }
  } // end function_exists no_to_words_arabic

 
  function get_current_store_id(){
    $CI =& get_instance();
    return $CI->session->userdata('store_id');
  }

  function get_customer_store_id($customer_id){
    $CI =& get_instance();
    return $CI->db->select('store_id')->from('db_customers')->where('id',$customer_id)->get()->row()->store_id;
  }
  function get_customer_details($customer_id){
    $CI =& get_instance();
    $customer = $CI->db->select('db_customers.*','db_country.country')->from('db_customers')->where('db_customers.id',$customer_id)->join('db_country', 'db_customers.country_id = db_country.id', 'left')->get()->row();
    
    $country = $CI->db->select('db_country.country')->from('db_country')->where('db_country.id',$customer->country_id)->get()->row();
    
    $customer->country = $country != null ? $country->country : '';
    
    return $customer;
  }
  function get_shipping_address_details($id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_shippingaddress')->where('id',$id)->get()->row();
  }
  function get_supplier_details($supplier_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_suppliers')->where('id',$supplier_id)->get()->row();
  }
  function get_supplier_store_id($supplier_id){
    $CI =& get_instance();
    return $CI->db->select('store_id')->from('db_suppliers')->where('id',$supplier_id)->get()->row()->store_id;
  }

  function get_count_id($table,$store_id=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    return $CI->db->select('(coalesce(max(count_id),0)+1) as count_id')->where('store_id',$store_id)->get($table)->row()->count_id;
  }

  //Manuall count continue
  function get_last_count_id($table,$store_id=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    // Sales and POS share db_sales, so keep one sequence for both entry points.
    // New/legacy stores whose sequence is below 1001 must start at 1001.
    $next_count_expression = ($table === 'db_sales')
      ? 'GREATEST(coalesce(max(count_id),0)+1, 1001) as count_id'
      : '(coalesce(max(count_id),0)+1) as count_id';

    $CI->db->select($next_count_expression, false)->where('store_id',$store_id)->order_by('id','desc')->limit(1)->from($table);
    //echo $CI->db->get_compiled_select();exit;

    return $CI->db->get()->row()->count_id;
  }

  /*Warehouse*/
  function warehouse_count(){
    $CI =& get_instance();
    return $CI->db->select('count(*) as warehouse_count')->where('store_id',get_current_store_id())->where('status',1)->get('db_warehouse')->row()->warehouse_count;
  }
  function get_store_warehouse_id(){
    $CI =& get_instance();
    $row = $CI->db->select('id')->where('store_id',get_current_store_id())->where('warehouse_type','System')->get('db_warehouse')->row();
    return ($row) ? $row->id : null;
  }
  /*end*/
  function get_only_init_code($value,$store_id=''){
    return get_init_code($value,null,$only_code_flag=true);
  }

  function get_init_code($value,$store_id='',$only_code_flag=false){
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();

    $CI =& get_instance();
    if($value=='department')
      $CI->db->select("department_init");
    if($value=='category')
      $CI->db->select("category_init");
    if($value=='subcategory')
      $CI->db->select("subcategory_init");
    if($value=='item')
      $CI->db->select("item_init");
    if($value=='supplier')
      $CI->db->select("supplier_init");
    if($value=='purchase')
      $CI->db->select("purchase_init");
    if($value=='purchase_return')
      $CI->db->select("purchase_return_init");
    if($value=='customer')
      $CI->db->select("customer_init");
    if($value=='sales')
      $CI->db->select("sales_init");
    if($value=='sales_return')
      $CI->db->select("sales_return_init");
    if($value=='expense')
      $CI->db->select("expense_init");
    if($value=='accounts')
      $CI->db->select("accounts_init");
    /*if($value=='journal')
      $CI->db->select("journal_init");*/
    if($value=='quotation')
      $CI->db->select("quotation_init");
    if($value=='money_transfer')
      $CI->db->select("money_transfer_init");
    if($value=='sales_payment')
      $CI->db->select("sales_payment_init");
    if($value=='sales_return_payment')
      $CI->db->select("sales_return_payment_init");
    if($value=='purchase_payment')
      $CI->db->select("purchase_payment_init");
    if($value=='purchase_return_payment')
      $CI->db->select("purchase_return_payment_init");
     if($value=='expense_payment')
      $CI->db->select("expense_payment_init");
    if($value=='custadvance')
      $CI->db->select("cust_advance_init");

    $query = $CI->db->where('id',$store_id)->get('db_store')->row();
    if($value=='department'){
      $maxid=get_count_id('db_department');
      return $query->department_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='category'){
      $maxid=get_count_id('db_category');
      return $query->category_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='subcategory'){
      $maxid=get_count_id('db_subcategory');
      return $query->subcategory_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }

    if($value=='item'){
      $maxid=get_count_id('db_items');
      return $query->item_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='supplier'){
      $maxid=get_count_id('db_suppliers');
      return $query->supplier_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase'){
      $maxid=get_count_id('db_purchase');
      return $query->purchase_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase_return'){
      $maxid=get_count_id('db_purchasereturn');
      return $query->purchase_return_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='customer'){
      $maxid=get_count_id('db_customers');
      return $query->customer_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales'){
      $maxid=get_count_id('db_sales');
      return ($only_code_flag) ? $query->sales_init : $query->sales_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales_return'){
      $maxid=get_count_id('db_salesreturn');
      return $query->sales_return_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='expense'){
      $maxid=get_count_id('db_expense');
      return $query->expense_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='accounts'){
      $maxid=get_count_id('ac_accounts');
      return $query->accounts_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
   /* if($value=='journal'){
      $maxid=get_count_id('ac_journal');
      //return $query->accounts_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
      return str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }*/
    if($value=='quotation'){
      $maxid=get_count_id('db_quotation');
      return $query->quotation_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='money_transfer'){
      $maxid=get_count_id('ac_moneytransfer');
      return $query->money_transfer_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales_payment'){
      $maxid=get_count_id('db_salespayments');
      return $query->sales_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='sales_return_payment'){
      $maxid=get_count_id('db_salespaymentsreturn');
      return $query->sales_return_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase_payment'){
      $maxid=get_count_id('db_purchasepayments');
      return $query->purchase_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='purchase_return_payment'){
      $maxid=get_count_id('db_purchasepaymentsreturn');
      return $query->purchase_return_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='expense_payment'){
      $maxid=get_count_id('db_expense');
      return $query->expense_payment_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='custadvance'){
      $maxid=get_count_id('db_custadvance');
      return $query->cust_advance_init.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='order'){
      $maxid=get_count_id('db_orders');
      $prefix = "OR/" . date('Y') . "/";
      return $prefix.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
    if($value=='order_payment'){
      $maxid=get_count_id('db_orderpayments');
      $prefix = "OP/" . date('Y') . "/";
      return $prefix.str_pad($maxid, 4, '0', STR_PAD_LEFT);
    }
  }
  function get_store_name($id=''){
    if(empty($id)){ return true;}
    $CI =& get_instance();
    if(empty($id)){
      $id=get_current_store_id();
    }
    $q1 = $CI->db->select('store_name')->where('id',$id)->get('db_store');
    if($q1->num_rows()>0){
      return $q1->row()->store_name;
    }
    else{
      return null;
    }
  }
  function get_role_name(){
      $CI =& get_instance();
      return $CI->session->userdata('role_name');
  }
  function store_admin_id(){
    return 2;
  }
  function is_store_admin(){
    $CI =& get_instance();
    if($CI->session->userdata('role_id')==store_admin_id()){
      return true;
    }
    return false;
  }
  function is_admin(){
    if(strtoupper(get_role_name())==strtoupper('admin')){
      return true;
    }
    return false;
  }
  function is_user(){
    return is_admin();
  }
  function set_status_of_table($col_id,$status,$table){
    $CI =& get_instance();
    $CI->db->where("id",$col_id);
    //if not admin
    if(!is_admin()){
      $CI->db->where("store_id",get_current_store_id());
    }
    $CI->db->set("status",$status);
    $query1=$CI->db->update($table);
        if ($query1){
            return true;
        }
        return false;
  }

  function get_walk_in_customer_name(){
    return 'Walk-in customer';
  }
  
  function get_warehouse_name($id){
    $CI =& get_instance();
    return $CI->db->select('warehouse_name')->where('id',$id)->get('db_warehouse')->row()->warehouse_name;
  }
  
  function get_warehouse($id = null){
      if($id == null) $id = get_store_warehouse_id();
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',$id)->get('db_warehouse')->row();
  }
  
  function get_total_qty_of_warehouse_item($item_id,$warehouse_id='',$store_id=''){
    if(empty($warehouse_id)){
      $warehouse_id= get_store_warehouse_id();
    }
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    $CI =& get_instance();
    /*Sum purchase quantity of purchase entry*/
    $purchase_qty=$CI->db->query("SELECT COALESCE(SUM(a.purchase_qty), 0) AS purchase_qty FROM 
                              db_purchaseitems AS a,
                              db_purchase AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`purchase_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id and b.purchase_status='Received'")->row()->purchase_qty;

    /*Sum purchase quantity of purchase entry*/
    $purchase_return_qty=$CI->db->query("SELECT COALESCE(SUM(a.return_qty), 0) AS purchase_return_qty FROM 
                              db_purchaseitemsreturn AS a,
                              db_purchasereturn AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`return_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id")->row()->purchase_return_qty;

    /*Sum sales quantity of sales entry*/
    $sales_qty=$CI->db->query("SELECT COALESCE(SUM(a.sales_qty), 0) AS sales_qty FROM 
                              db_salesitems AS a,
                              db_sales AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`sales_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id")->row()->sales_qty;

    /*Sum sales return quantity of invoice*/
    $sales_return_qty=$CI->db->query("SELECT COALESCE(SUM(a.return_qty), 0) AS sales_return_qty FROM 
                              db_salesitemsreturn AS a,
                              db_salesreturn AS b
                              WHERE 
                              a.`item_id`=$item_id AND a.`return_id`=b.id AND 
                              b.`store_id`=$store_id AND b.`warehouse_id`=$warehouse_id")->row()->sales_return_qty;


    $stock_entry_qty=$CI->db->query("SELECT COALESCE(SUM(adjustment_qty),0) AS adjustment_qty FROM db_stockadjustmentitems 
                              WHERE 
                              store_id=$store_id AND 
                              warehouse_id=$warehouse_id AND
                              item_id=$item_id")->row()->adjustment_qty;
    /*Add Stock Transfer*/
    $stocktransfer_qty_add=$CI->db->query("SELECT COALESCE(SUM(transfer_qty),0) AS stocktransfer_qty FROM db_stocktransferitems 
                              WHERE 
                              store_id=$store_id AND 
                              warehouse_to=$warehouse_id AND
                              item_id=$item_id")->row()->stocktransfer_qty;
    /*Deduct Stock from warerhouse*/
    $stocktransfer_qty_deduct=$CI->db->query("SELECT COALESCE(SUM(transfer_qty),0) AS stocktransfer_qty FROM db_stocktransferitems 
                              WHERE 
                              store_id=$store_id AND 
                              warehouse_from=$warehouse_id AND
                              item_id=$item_id")->row()->stocktransfer_qty;
    
    return ($stock_entry_qty + $purchase_qty + $stocktransfer_qty_add - $stocktransfer_qty_deduct + $sales_return_qty - $purchase_return_qty)-$sales_qty;
  }
  function update_warehousewise_items_qty($item_id,$warehouse_id,$store_id){
    $CI =& get_instance();
    //If item id exist
      $CI->db->where("store_id",$store_id)->where("warehouse_id",$warehouse_id)->where('item_id',$item_id)->delete("db_warehouseitems");
      $available_qty = get_total_qty_of_warehouse_item($item_id,$warehouse_id,$store_id);
      if($available_qty>0){
        $info=array(  'store_id'      =>  $store_id,
                      'warehouse_id'  =>  $warehouse_id,
                      'item_id'       =>  $item_id,
                      'available_qty' =>  $available_qty,
         );
        $q1 = $CI->db->insert('db_warehouseitems', $info);
        if(!$q1){
          return false;
        }
      }      
    return true;
  }

  function update_warehousewise_items_qty_by_store($store_id='',$item_ids=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
      $q3=$CI->db->select("id")->where("store_id",$store_id)->get("db_warehouse");
      foreach($q3->result() as $res3) {
        $warehouse_id = $res3->id;
        if(!empty($item_ids)){
          $CI->db->where("id in ($item_ids)");
        }
        $CI->db->where("service_bit!=1");
        $q1=$CI->db->select("id")->where('store_id',$store_id)->get("db_items");  
        foreach($q1->result() as $res1) {
            $q1 = update_warehousewise_items_qty($res1->id,$warehouse_id,$store_id);
            if(!$q1){
              return false;
            }
        }//items foreach
      }//Warehouse foreach
    return true;
  }

  function get_in_comma_delimited($two_array){
    if(!is_array($two_array)){
      $two_array = array(array($two_array));
    }
    $unique_array = array_unique($two_array,SORT_REGULAR);

    $tmpArr = array();
    foreach ($unique_array as $sub) {
      $tmpArr[] = implode(',', $sub);
    }
    $item_ids = implode(',', $tmpArr);
    return $item_ids;
  }

   function update_warehouse_items($two_array){
  
    $item_ids = get_in_comma_delimited($two_array);

    /*Update items in all warehouses of the item*/
    $q7=update_warehousewise_items_qty_by_store(null,$item_ids);
    if(!$q7){
      return false;
    }
    return true;
  }


  function total_items_of_warehouse($warehouse_id,$store_id=''){
    $CI =& get_instance();
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    
    return $CI->db->select("count(*) as total_items")->where("warehouse_id",$warehouse_id)->where("store_id",$store_id)->get("db_warehouseitems")->row()->total_items;
  }
  function total_available_qty_items_of_warehouse($warehouse_id='',$store_id='',$item_id=''){
    $CI =& get_instance();
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    if(!empty($item_id)){
      $CI->db->where("item_id",$item_id);
    }
    if(!empty($warehouse_id)){
      $CI->db->where("warehouse_id in ($warehouse_id)");
    }
    $CI->db->select("COALESCE(sum(available_qty),0) as available_qty")->where("store_id",$store_id)->from("db_warehouseitems");
    //echo $CI->db->get_compiled_select();exit;
    return $CI->db->get()->row()->available_qty;
  }
  function total_worth_of_warehouse_items($warehouse_id,$store_id=''){
    $CI =& get_instance();
    if(empty($store_id)){
      $store_id= get_current_store_id();
    }
    $CI->db->select("COALESCE(sum(available_qty),0) as available_qty,item_id")->where("warehouse_id",$warehouse_id)->where("store_id",$store_id)->from("db_warehouseitems")->group_by("item_id");
    $q1 = $CI->db->get();
    $tot_sales_price=0;
      foreach ($q1->result() as $res1) {
        $item_price = $CI->db->select("coalesce((sales_price),0) as sales_price")->where("id",$res1->item_id)->get("db_items")->row()->sales_price;
        $tot_sales_price+=$item_price*$res1->available_qty;
      }
    return $tot_sales_price;
  }

  function get_total_stocktranfer_items($stocktransfer_id){
    $CI =& get_instance();
    return $CI->db->select("count(item_id) as tot_items")->where("store_id",get_current_store_id())->where("stocktransfer_id",$stocktransfer_id)->get("db_stocktransferitems")->row()->tot_items;
  }
  function get_total_stocktranfer_items_qty($stocktransfer_id){
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(transfer_qty),0) as transfer_qty")->where("store_id",get_current_store_id())->where("stocktransfer_id",$stocktransfer_id)->get("db_stocktransferitems")->row()->transfer_qty;
  }

  function get_current_user_id(){
    $CI =& get_instance();
    return $CI->session->userdata('inv_userid');
  }

  function get_paid_cob($customer_id){//Customer Opening Balance Paid Total
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(payment),0) as payment")
            ->where("store_id",get_current_store_id())
            ->where("customer_id",$customer_id)
            ->where("short_code","OPENING BALANCE PAID")
            ->get("db_salespayments")->row()->payment;
  }
  function get_paid_sob($supplier_id){//supplier Opening Balance Paid Total
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(payment),0) as payment")
            ->where("store_id",get_current_store_id())
            ->where("supplier_id",$supplier_id)
            ->where("short_code","OPENING BALANCE PAID")
            ->get("db_purchasepayments")->row()->payment;
  }
  function get_account_name($id){
    if(empty($id)) {return "";}
    $CI =& get_instance();
    return $CI->db->select("account_name")->where("store_id",get_current_store_id())->where("id",$id)->get("ac_accounts")->row()->account_name;
  }
  function get_seller_points($item_id){
    $CI =& get_instance();
    return $CI->db->select("seller_points")->where("id",$item_id)->get("db_items")->row()->seller_points;
  }
  function get_item_name($item_id){
    $CI =& get_instance();
    return $CI->db->select("item_name")->where("id",$item_id)->get("db_items")->row()->item_name;
  }
  function get_current_store_language(){
    $CI =& get_instance();
    return $CI->db->select("language_id")->where("id",get_current_store_id())->get("db_store")->row()->language_id;
  }

  function get_price_level_price($customer_id,$price){
    if(empty($customer_id)) return $price;
    $CI =& get_instance();
    $q1=$CI->db->select("price_level_type,price_level")->where("store_id",get_current_store_id())->where("id",$customer_id)->get("db_customers")->row();
    if($q1 && $q1->price_level!=0){
      return ($q1->price_level_type=='Increase') ? $price + ($price*$q1->price_level)/100 : $price - ($price*$q1->price_level)/100;
    }
    else{
      return $price;
    }
  }
  /*Customer Calculate Opening Balance of the invoice, before and after*/
  function calculate_ob_of_customer($sales_id,$customer_id){
    /*
    Note: Run this Function after customer and sales record updates
    */

    /*$CI =& get_instance();
    //Sales grand total & paid amount
    $CI->db->select("coalesce(sum(grand_total)) as grand_total, coalesce(sum(paid_amount)) as paid_amount");
    $CI->db->from("db_sales");
    $CI->db->where("id",$sales_id);
    $q1 = $CI->db->get()->row(); 
    $grand_total = $q1->grand_total;
    $paid_amount = $q1->paid_amount;
    
    //Pending invoice payment + Opening balance
    $CI->db->select("coalesce(sum(sales_due),0)+coalesce(sum(opening_balance),0) as tot");
    $CI->db->from("db_customers");
    $CI->db->where("id",$customer_id);
    $invoice_ob = $CI->db->get()->row()->tot; //Current
    
    //Update Sales invoice
    $customer_previous_due = $invoice_ob - ($grand_total - $paid_amount); //Previous
    $CI->db->set("customer_previous_due",$customer_previous_due);
    $CI->db->set("customer_total_due",$invoice_ob);
    $CI->db->where("id",$sales_id);
    $q3 = $CI->db->update("db_sales");
    if(!$q3){
      return false;
    }
    return true;*/
  }
  
  function get_privileged_warehouses_ids(){
    $CI =& get_instance();
    //Find the previllaged wareshouses to the user
    $CI->db->select("warehouse_id")->where("user_id",get_current_user_id())->from("db_userswarehouses");
    $q3 = $CI->db->get();
    $privileged_warehouses = array();
    foreach ($q3->result() as $res3) {
      $privileged_warehouses[] = $res3->warehouse_id;
    }
    $privileged_warehouses = implode(',', $privileged_warehouses);
    return $privileged_warehouses;
  }
   function calculate_inclusive($amount,$tax){
  $tot = ($amount/(($tax/100)+1)/10);
    return number_format($tot,2,".","");
  }
  function calculate_exclusive($amount,$tax){
    $tot = (($amount*$tax)/(100));
    return number_format($tot,2,".","");
  }
  
  //08-09-2020
  function get_profile_picture(){
    $CI =& get_instance();
    $profile_picture = $CI->db->select('profile_picture')->where("id",$CI->session->userdata('inv_userid'))->get('db_users')->row()->profile_picture;
    if(!empty($profile_picture)){
      $profile_picture = base_url($profile_picture);
    }
    else{
      $profile_picture = base_url("theme/dist/img/avatar5.png");
    }
    return $profile_picture;
  }

  function get_sales_id_of_quotation($quotation_id){
    $CI =& get_instance();
    return $CI->db->select('id')->where('quotation_id',$quotation_id)->get('db_sales')->row()->id;
  }
  function get_quotation_code($quotation_id){
    $CI =& get_instance();
    return $CI->db->select('quotation_code')->where('id',$quotation_id)->get('db_quotation')->row()->quotation_code;
  }
  function get_sales_code($sales_id){
    $CI =& get_instance();
    return $CI->db->select('sales_code')->where('id',$sales_id)->get('db_sales')->row()->sales_code;
  }
  function get_sales_return_code($return_id){
    $CI =& get_instance();
    return $CI->db->select('return_code')->where('id',$return_id)->get('db_salesreturn')->row()->return_code;
  }
  function get_sales_details($sales_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_sales')->where('id',$sales_id)->get()->row();
  }
  function get_sales_return_details($return_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_salesreturn')->where('id',$return_id)->get()->row();
  }
  function get_quotation_details($quotation_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_quotation')->where('id',$quotation_id)->get()->row();
  }
  function get_deliverynote_details($deliverynote_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_deliverynote')->where('id',$deliverynote_id)->get()->row();
  }
  function get_purchase_details($purchase_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_purchase')->where('id',$purchase_id)->get()->row();
  }
  function get_order_details($order_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_orders')->where('id',$order_id)->get()->row();
  }
  function get_purchase_return_details($return_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_purchasereturn')->where('id',$return_id)->get()->row();
  }
  function get_state_details($state_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_states')->where('id',$state_id)->get()->row();
  }
  function get_country_details($country_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_country')->where('id',$country_id)->get()->row();
  }
  function get_tax_details($tax_id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_tax')->where('id',$tax_id)->get()->row();
  }
  function is_it_belong_to_store($table,$rec_id){
    $CI =& get_instance();
    $store_id = get_current_store_id();
    return $CI->db->select('count(*) as tot_rec')->where('id',$rec_id)->where('store_id',$store_id)->get($table)->row()->tot_rec;
  }
  function get_coupon_master_details($id){
    $CI =& get_instance();
    return $CI->db->select("*")
            ->from("db_coupons")
            ->where('store_id',get_current_store_id())
            ->where("id=",$id)->get()->row();
  }
  function get_customer_coupon_details($id){
    $CI =& get_instance();
    return $CI->db->select("*")
            ->from("db_customer_coupons")
            ->where('store_id',get_current_store_id())
            ->where("id=",$id)->get()->row();
  }
  function get_customer_coupon_details_by_coupon_code($code){
    $CI =& get_instance();
    return $CI->db->select("*")
            ->where('store_id',get_current_store_id())
            ->where("upper(code)",strtoupper($code))
            ->from("db_customer_coupons")->get();
  }
  function get_item_details($item_id){
    $CI =& get_instance();
    return $CI->db->select("*")
            ->from("db_items")
            ->where('store_id',get_current_store_id())
            ->where("id=",$item_id)->get()->row();
  }
  function get_brand_details($brand_id){
    $CI =& get_instance();
    return $CI->db->select("*")
            ->from("db_brands")
            ->where('store_id',get_current_store_id())
            ->where("id=",$brand_id)->get()->row();
  }

  function get_country($country_id=''){
    $CI =& get_instance();
    if(trim($country_id) == '') { return null; }
    $Q1 = $CI->db->select("*")
            ->from("db_country")
            ->where("id=",$country_id)->get();
    if($Q1->num_rows()>0){
      return $Q1->row()->country;
    }
    return null;
  }

  function get_state($state_id=''){
    $CI =& get_instance();
    if(trim($state_id) == '') { return null; }
    $Q1 = $CI->db->select("*")
            ->from("db_states")
            ->where("id=",$state_id)->get();
    if($Q1->num_rows()>0){
      return $Q1->row()->country;
    }
    return null;
  }
  function get_sales_payment_details($id){
    $CI =& get_instance();
    return $CI->db->select('*')->from('db_salespayments')->where('id',$id)->get()->row();
  }
  function permissions($permissions=''){
    $CI =& get_instance();
    //If he the Admin
    if($CI->session->userdata('inv_userid')==1){
      return true;
    }
    $tot=$CI->db->query('SELECT count(*) as tot FROM db_permissions where permissions="'.$permissions.'" and role_id='.$CI->session->userdata('role_id'))->row()->tot;
    if($tot==1){
      return true;
    }
     return false;
  }

  function get_sales_tax_total($sales_id){
    $CI =& get_instance();
    return $CI->db->select("coalesce(sum(tax_amt),0) as tax_total")
            ->where("store_id",get_current_store_id())
            ->where("sales_id",$sales_id)
            ->get("db_salesitems")->row()->tax_total;
  }


  function get_current_subcription_id($store_id=''){
    $CI =& get_instance();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();

    
    $subscription_id = $CI->db->select('current_subscriptionlist_id as subscription')->where('id',$store_id)->get('db_store')->row()->subscription;
    if(!$subscription_id){
      return false;
    }
    return $subscription_id;
  }
  function get_subscription_rec($sub_id){
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',$sub_id)->get('db_subscription')->row();
  }

  function get_package_details($package_id){
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',$package_id)->get('db_package')->row();
  }

  function get_tot_table_rec($table,$store_id=''){
    $CI =& get_instance();
    //Subscription details
    $subscription_id = get_current_subcription_id();
    $subscribed_on = get_subscription_rec($subscription_id)->subscription_date;

    //Query
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    $CI->db->select('count(*) as count_id');
    $CI->db->where('store_id',$store_id);
    $CI->db->where('created_date>=',$subscribed_on);
    $CI->db->where('store_id',$store_id);
    return $CI->db->get($table)->row()->count_id;
  }

  function validate_subscription(){
    if(is_admin()){
      return true;
    }
    if(!store_module()){
      return true;
    }
    $subscription_id = get_current_subcription_id();
    if(empty($subscription_id)){
      echo "This store don't have any subscrtions!!";exit;
    }

    $expire_date = get_subscription_rec($subscription_id)->expire_date;
    if($expire_date<date('Y-m-d')){
      echo "Store Subscription expired!!";exit;
    }
    return true;

  }
  function validate_package_offers($column,$table_name){

    if(!store_module()){
      return true;
    }
    if(empty($column)){
      echo "Missing!! Package Validation";exit;
    }

    validate_subscription();
    
    $CI =& get_instance();
    $sub_id = get_current_subcription_id();
    if(!$sub_id && !is_admin() ){
      echo $CI->lang->line('subscription_msg_1');exit;
    }
    else{
      if($column=='max_invoices'){
        if(get_tot_table_rec($table_name) >= get_subscription_rec($sub_id)->max_invoices){
          echo $CI->lang->line('max_invoices_used');exit;
        }  
      }
      if($column=='max_items'){
        if(get_tot_table_rec($table_name) >= get_subscription_rec($sub_id)->max_items){
          echo $CI->lang->line('max_items_used');exit;
        }  
      }
      if($column=='max_warehouses'){
        if(get_tot_table_rec($table_name) >= get_subscription_rec($sub_id)->max_warehouses){
          echo $CI->lang->line('max_warehouses_used');exit;
        }  
      }
      if($column=='max_users'){
        $store_admin_count =1;
        if((get_tot_table_rec($table_name)-$store_admin_count) >= get_subscription_rec($sub_id)->max_users){
          echo $CI->lang->line('max_users_used');exit;
        }  
      }
      
    }
  }

  function date_difference($start_date,$end_date){
    // Declare two dates 
    $start_date = strtotime(date("Y-m-d",strtotime($start_date))); 
    $end_date = strtotime(date("Y-m-d",strtotime($end_date)));   
    // Get the difference and divide into  
    // total no. seconds 60/60/24 to get  
    // number of days 
    return ($end_date - $start_date)/60/60/24; 
  }


  function strip_tags_content($text) {

    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    
 }

  function get_invoice_terms(){
    $invoice_terms = (get_store_details()->invoice_terms);
    return strip_tags_content($invoice_terms);
  }

  function get_invoice_terms_for_pos(){
    $invoice_terms = get_store_details()->invoice_terms;
    $invoice_terms = html_entity_decode($invoice_terms);
    $invoice_terms = str_replace("<br>","##n##",$invoice_terms);
    $invoice_terms = str_replace("<br />","##n##",$invoice_terms);
    $invoice_terms = strip_tags($invoice_terms);
    $invoice_terms = str_replace("##n##","\n",$invoice_terms);
    return $invoice_terms;
  }

  function get_site_details(){
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',1)->get('db_sitesettings')->row();
  }

  /**
   * Optional Arabic store name/address for bilingual invoice header.
   */
  function ensure_store_arabic_header_fields(){
    ensure_store_profile_columns();
  }

  /**
   * Ensure store profile columns used by settings/invoice (bank fields, QR flags, Arabic header).
   */
  function ensure_store_profile_columns(){
    static $done = false;
    if($done){
      return;
    }
    $done = true;
    $CI =& get_instance();
    $cols = array(
      'store_name_arabic' => "ALTER TABLE `db_store` ADD `store_name_arabic` VARCHAR(255) NULL DEFAULT NULL",
      'address_arabic'    => "ALTER TABLE `db_store` ADD `address_arabic` TEXT NULL",
      'cr_no'             => "ALTER TABLE `db_store` ADD `cr_no` VARCHAR(100) NULL DEFAULT NULL",
      'bank_name'         => "ALTER TABLE `db_store` ADD `bank_name` VARCHAR(255) NULL DEFAULT NULL",
      'account_name'      => "ALTER TABLE `db_store` ADD `account_name` VARCHAR(255) NULL DEFAULT NULL",
      'account_number'    => "ALTER TABLE `db_store` ADD `account_number` VARCHAR(100) NULL DEFAULT NULL",
      'iban_number'       => "ALTER TABLE `db_store` ADD `iban_number` VARCHAR(100) NULL DEFAULT NULL",
      'routing_number'    => "ALTER TABLE `db_store` ADD `routing_number` VARCHAR(100) NULL DEFAULT NULL",
      'show_website_qr'   => "ALTER TABLE `db_store` ADD `show_website_qr` TINYINT(1) NOT NULL DEFAULT 1",
      'show_location_qr'  => "ALTER TABLE `db_store` ADD `show_location_qr` TINYINT(1) NOT NULL DEFAULT 1",
      'show_whatsapp_qr'  => "ALTER TABLE `db_store` ADD `show_whatsapp_qr` TINYINT(1) NOT NULL DEFAULT 1",
      'show_invoice_qr'   => "ALTER TABLE `db_store` ADD `show_invoice_qr` TINYINT(1) NOT NULL DEFAULT 1",
      'show_zatca_qr'     => "ALTER TABLE `db_store` ADD `show_zatca_qr` TINYINT(1) NOT NULL DEFAULT 1",
    );
    foreach($cols as $field => $sql){
      if(isset($CI->db->data_cache['field_names']['db_store'])){
        unset($CI->db->data_cache['field_names']['db_store']);
      }
      if($CI->db->field_exists($field, 'db_store')){
        continue;
      }
      try {
        $CI->db->query($sql);
      } catch (Throwable $e) {
        // ignore duplicate-column races
      }
    }
    if(isset($CI->db->data_cache['field_names']['db_store'])){
      unset($CI->db->data_cache['field_names']['db_store']);
    }
  }

  function get_store_details($store_id=''){
    $CI =& get_instance();
    ensure_location_url_setting();
    ensure_whatsapp_number_setting();
    ensure_store_profile_columns();
    $store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    return $CI->db->select('*')->where('id',$store_id)->get('db_store')->row();
  }

  function get_super_admin_bank_details($store_id=''){
    $CI =& get_instance();
    return $CI->db->select('*')->where('id',1)->get('db_bankdetails')->row();
  }

  /**
   * Build bank details HTML for invoices/PDFs from store profile fields.
   * Prefers structured fields (bank_name, account_*, iban, routing); falls back to bank_details text.
   */
  function format_store_bank_details_html($store=null){
    if(empty($store) || !is_object($store)){
      $store = get_store_details();
    }
    if(empty($store)){
      return '';
    }

    $lines = array();
    $bank_name = isset($store->bank_name) ? trim((string)$store->bank_name) : '';
    $account_name = isset($store->account_name) ? trim((string)$store->account_name) : '';
    $account_number = isset($store->account_number) ? trim((string)$store->account_number) : '';
    $iban_number = isset($store->iban_number) ? trim((string)$store->iban_number) : '';
    $routing_number = isset($store->routing_number) ? trim((string)$store->routing_number) : '';

    $rows = array();
    if($bank_name !== ''){
      $rows[] = array('Bank Name', $bank_name);
    }
    if($account_name !== ''){
      $rows[] = array('Account Name', $account_name);
    }
    if($account_number !== ''){
      $rows[] = array('Account Number', $account_number);
    }
    if($iban_number !== ''){
      $rows[] = array('IBAN', $iban_number);
    }
    if($routing_number !== ''){
      $rows[] = array('Routing Number', $routing_number);
    }

    // Aligned label : value rows (same column for all values)
    if(!empty($rows)){
      $html = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">';
      foreach($rows as $row){
        $html .= '<tr>'
          . '<td style="width:32%; font-weight:bold; white-space:nowrap; vertical-align:top;">'.html_escape($row[0]).'</td>'
          . '<td style="width:4%; font-weight:bold; vertical-align:top;">:</td>'
          . '<td style="width:64%; vertical-align:top;">'.html_escape($row[1]).'</td>'
          . '</tr>';
      }
      $html .= '</table>';
      return $html;
    }

    $bank_details = isset($store->bank_details) ? trim((string)$store->bank_details) : '';
    if($bank_details !== ''){
      return nl2br(html_escape($bank_details));
    }
    return '';
  }

  /**
   * Whether store has any bank detail to show on PDF.
   */
  function store_has_bank_details($store=null){
    return format_store_bank_details_html($store) !== '';
  }

  function get_user_details($user_id=''){
    $CI =& get_instance();
    $user_id = (!empty($user_id)) ? $user_id : get_current_user_id();
    return $CI->db->select('*')->where('id',$user_id)->get('db_users')->row();
  }

  function check_credit_limit_with_invoice($customer_id,$sales_id){
    $credit_limit = get_customer_details($customer_id)->credit_limit;
    $balance = get_customer_details($customer_id)->sales_due;
    $sales_details = get_sales_details($sales_id);
    //$balance = $sales_details->grand_total -$sales_details->paid_amount;

    if( $credit_limit!=-1 && $balance>$credit_limit){
      //if($balance>$credit_limit){
          echo 'This Customer Credit Limit exceeds! Credit Limit :'.store_number_format($credit_limit)."\nCrossing Credit Amount(Previous+Current Invoice) :".store_number_format($balance);
        exit;
      //}
    }
    return true;
  }

  function xss_html_filter($input){
        $CI =& get_instance();
        return $CI->security->xss_clean(html_escape($input));
    }
  function kmb($n, $precision = 2) {
      if ($n < 900) {
        // Default
         $n_format = number_format($n);
        } else if ($n < 900000) {
        // Thausand
        $n_format = number_format($n / 1000, $precision). 'K';
        } else if ($n < 900000000) {
        // Million
        $n_format = number_format($n / 1000000, $precision). 'M';
        } else if ($n < 900000000000) {
        // Billion
        $n_format = number_format($n / 1000000000, $precision). 'B';
        } else {
        // Trillion
        $n_format = number_format($n / 1000000000000, $precision). 'T';
    }
    return $n_format;
  }

  #----------------------------------------------------------------
  //Show only last 4 digits
  function getTruncatedCCNumber($ccNum){
        return str_replace(range(0,9), "*", substr($ccNum, 0, -4)) .  substr($ccNum, -4);
  }

  #----------------------------------------------------------------
  function cheque_name(){
    return "Cheque";
  }
  function cash_name(){
    return "Cash";
  }
  #----------------------------------------------------------------

  function gst_number(){
    return true;
  }
  function vat_number(){
    return true;
  }

  /**
   * Branch TRN (vat_no). Empty TRN means tax should not be processed.
   */
  function get_branch_trn($store_id=''){
    $CI =& get_instance();
    $store_id = empty($store_id) ? get_current_store_id() : (int)$store_id;
    $row = $CI->db->select('vat_no')->where('id', $store_id)->get('db_store')->row();
    if(empty($row) || !isset($row->vat_no)){
      return '';
    }
    return trim((string)$row->vat_no);
  }

  /**
   * True when branch TRN is set; false => sales/sales return/purchase must not process tax.
   */
  function is_branch_tax_enabled($store_id=''){
    return get_branch_trn($store_id) !== '';
  }
  function pan_number(){
    return true;
  }
  
  /*Module*/
  
  function warehouse_module(){
    return true;//true or false
  }
  function accounts_module(){
    return true;//true or false
  } 
  function service_module(){
    return true;
  }

  /**
   * ZATCA (Fatoora) E-Invoice TLV Tag generator
   */
  function get_zatca_tlv_tag($tag, $value) {
    $value = (string)$value;
    return chr($tag) . chr(strlen($value)) . $value;
  }

  /**
   * Generate ZATCA (Saudi E-Invoice) QR Code PNG Base64 Data String for TCPDF
   */
  function get_zatca_qr_code_image($seller_name, $vat_number, $invoice_datetime, $total_amount, $tax_amount, $pixel_w = 4, $pixel_h = 4) {
    if (empty($seller_name)) {
      return '';
    }

    // Same data and format as the rapidsma_ksa invoices (salla/zatca): TLV tags 1-5, Base64 encoded.
    // Tag 3 is the invoice date as YYYY-MM-DD.
    $formatted_date = !empty($invoice_datetime) ? date('Y-m-d', strtotime($invoice_datetime)) : date('Y-m-d');

    // Tag 2 is the store's own TRN / VAT number exactly as saved in the branch settings (digits only).
    // It must never be replaced by a made-up number.
    $clean_vat = preg_replace('/[^0-9]/', '', (string)$vat_number);

    $tlv = get_zatca_tlv_tag(1, $seller_name)
         . get_zatca_tlv_tag(2, $clean_vat)
         . get_zatca_tlv_tag(3, $formatted_date)
         . get_zatca_tlv_tag(4, number_format((float)$total_amount, 2, '.', ''))
         . get_zatca_tlv_tag(5, number_format((float)$tax_amount, 2, '.', ''));

    $base64_tlv = base64_encode($tlv);

    $tcpdf_barcodes_file = APPPATH . 'libraries/tcpdf/tcpdf_barcodes_2d.php';
    if (file_exists($tcpdf_barcodes_file)) {
      if (!class_exists('TCPDF2DBarcode', false)) {
        require_once($tcpdf_barcodes_file);
      }
      try {
        $barcode = new TCPDF2DBarcode($base64_tlv, 'QRCODE,M');
        $pngData = $barcode->getBarcodePngData($pixel_w, $pixel_h);
        if (!empty($pngData)) {
          if (function_exists('imagecreatefromstring') && function_exists('imagecreatetruecolor')) {
            $src_img = @imagecreatefromstring($pngData);
            if ($src_img !== false) {
              $src_w = imagesx($src_img);
              $src_h = imagesy($src_img);
              $margin = (int)($pixel_w * 4);
              $dst_w = $src_w + ($margin * 2);
              $dst_h = $src_h + ($margin * 2);
              $dst_img = imagecreatetruecolor($dst_w, $dst_h);
              $white = imagecolorallocate($dst_img, 255, 255, 255);
              imagefilledrectangle($dst_img, 0, 0, $dst_w, $dst_h, $white);
              imagecopy($dst_img, $src_img, $margin, $margin, 0, 0, $src_w, $src_h);
              ob_start();
              imagepng($dst_img);
              $borderedPng = ob_get_clean();
              imagedestroy($src_img);
              imagedestroy($dst_img);
              if (!empty($borderedPng)) {
                $pngData = $borderedPng;
              }
            }
          }
          return '@' . base64_encode($pngData);
        }
      } catch (Exception $e) {
        return '';
      }
    }
    return '';
  }

  /**
   * Generate ZATCA (Saudi E-Invoice) QR Code PNG Base64 Data URI for HTML / Dompdf
   */
  function get_zatca_qr_code_data_uri($seller_name, $vat_number, $invoice_datetime, $total_amount, $tax_amount, $pixel_w = 4, $pixel_h = 4) {
    $img = get_zatca_qr_code_image($seller_name, $vat_number, $invoice_datetime, $total_amount, $tax_amount, $pixel_w, $pixel_h);
    if (!empty($img) && strpos($img, '@') === 0) {
      return 'data:image/png;base64,' . substr($img, 1);
    }
    return $img;
  }

  /**
   * Clean HTML before Dompdf (PHP 8): strip AdminLTE/external CSS and height:auto
   * which leave non-numeric sizes and cause "string + float" TypeErrors.
   */
  function sanitize_html_for_dompdf($html) {
    if ($html === null || $html === '') {
      return '';
    }
    $html = preg_replace('/<link\b[^>]*>/i', '', $html);
    $html = preg_replace('/@import\s+[^;]+;/i', '', $html);
    $html = preg_replace('/height\s*:\s*auto\s*;?/i', '', $html);
    $html = preg_replace('/\bheight\s*=\s*(["\']?)auto\1/i', '', $html);
    $html = preg_replace('/width\s*:\s*auto\s*;?/i', '', $html);
    return $html;
  }

