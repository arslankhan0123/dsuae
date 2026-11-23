<?php
//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2013-05-14
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML tables and table headers
 * @author Nicola Asuni
 * @since 2009-03-20
 */

// Include the main TCPDF library (search for installation path).
//require_once('tcpdf_include.php');

include dirname(__DIR__).'/examples/tcpdf_include.php';

class MyPDF extends TCPDF {

    protected $last_page_flag = false;
    
    public $_invoice_name = "Invoice Name";

    //public $_QRCODE = null;

    public $CI = null;

    public $store =array();
    
    public $warehouse =array();

    public $_document_name = '';

    public $_document_number = '';

    public $_rtl = false;

    public $page_title = 'Default Invoice';

    public $_invoice_format = 'Defult';

    public $_app_language = 'English';

    /** Y position where company header text ends — used to kill bottom gap */
    public $header_bottom_y = 0;

    /** Y position where TAX INVOICE title box ends */
    public $title_bottom_y = 0;

    /** When set, Footer draws this left of "Page X of Y" just above the address line */
    public $footer_generate_text = '';

    public function __construct()
    {
        $store_data = get_store_details();
        $format_str = (!empty($store_data->pdf_format)) ? $store_data->pdf_format : 'A4 Format';
        $page_format = ($format_str == 'A5 Format') ? 'A5' : 'A4';
        
        parent::__construct('P', 'mm', $page_format);
        //Do your magic here

        $this->CI =& get_instance();

        $wh_id = null;
        if(!empty($this->sales_id)){
            $sales_rec = get_sales_details($this->sales_id);
            if(!empty($sales_rec) && !empty($sales_rec->warehouse_id)){
                $wh_id = $sales_rec->warehouse_id;
            }
        } else if(!empty($this->return_id)){
            $return_rec = get_sales_return_details($this->return_id);
            if(!empty($return_rec) && !empty($return_rec->warehouse_id)){
                $wh_id = $return_rec->warehouse_id;
            }
        }
        $this->store = get_store_details($wh_id);
        
        $this->warehouse = get_warehouse();
        
        $this->_document_settings();
    }

    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }

    public function _set_header()
    {   

        $customer = $this->customer;//array()

        $store = $this->store;//array()
        
        $warehouse = $this->warehouse;

        //Customer Records
        $state = (!empty($customer->state_id)) ? get_state_details($customer->state_id) : '';
        $customer_state_name = (!empty($state)) ? $state->state : $store->state;

        $w = 100;
        $h = 40;

        $custmer_details = '<span style="color:rgb(65, 59, 212);font-style:italic;">'.$this->CI->lang->line('bill_to').':</span>';
        $custmer_details .= "<br><b>".$this->CI->lang->line('name')." :</b> ".$customer->customer_name;
        $custmer_details .= "<br><b>".$this->CI->lang->line('address')." :</b> ".$customer->address;
        $custmer_details .= "<br><b>".$this->CI->lang->line('postcode')." :</b> ".$customer->postcode;
        $custmer_details .= "<br><b>".$this->CI->lang->line('mobile')." :</b> ".$warehouse->mobile;
        $custmer_details .= "<br><b>".$this->CI->lang->line('email')." :</b> ".$warehouse->email;
        $custmer_details .= "<br><b>".$this->CI->lang->line('gst_number')." :</b> ".$customer->gst_no; 

        $this->writeHTMLCell($w, $h, $x ='6', $y='52', $custmer_details, 1, 0, 1, true, 'J', true);
        return $this;
    } 

    public function _rtl($rtl=false)
    {
        $this->_rtl = $rtl;

        //Download fonts from :https://www.fontmirror.com/
        
        /*$path = dirname(__DIR__).'/fonts/Garet Book 300.ttf';
        $strBNFont = TCPDF_FONTS::addTTFfont($path, 'TrueTypeUnicode', '', 32);
        
        if($strBNFont){
            echo "Installation succed";
        }
        else{
            echo "Failed to install font";
        }
        exit;*/

        if($rtl == true){
            // set some language dependent data:
            $lg = Array();
            $lg['a_meta_charset'] = 'UTF-8';
            $lg['a_meta_dir'] = 'rtl';
            $lg['a_meta_language'] = 'fa';
            $lg['w_page'] = 'page';

            // set some language-dependent strings (optional)
            $this->setLanguageArray($lg);
        }
        else{
            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $this->setLanguageArray($l);
            }
        }
    }

    public function get_font_name()
    {   
        /**
         * 1. English
         * 2. Russian
         * 3. Spanish
         * 4. Arabic
         * 5. Bangla
         * 6. French
         * */

        $lang_array = array();
        /**
         * English Fonts:
         * 
         * helvetica 
        */
        
        // DejaVu supports Latin + Arabic (helvetica shows Arabic as ???)
        $lang_array['English'] = 'dejavusans';

        /**
         * Arabic Fonts:
         * 
         * aefurat - GOOD
         * aealarabiya
         */
        
        $lang_array['Arabic'] = 'aefurat';

        /**
         * Bangla Fonts:
         * 
         * nikoshgrameem300 -GOOD
         * nikoshlightban300
         * nikosh400
         * 
         */

        $lang_array['Bangla'] = 'nikoshgrameem300';

        /**
         * Russian Fonts:
         * 
         * garetbook300
         * 
         * */
        $lang_array['Russian'] = 'garetbook300';


        if(strtoupper($this->_app_language)==strtoupper('Bangla')){
            return $lang_array['Bangla'];
        }
        else if(strtoupper($this->_app_language)==strtoupper('Arabic')){
            return $lang_array['Arabic'];
        }
        else if(strtoupper($this->_app_language)==strtoupper('Russian')){
            return $lang_array['Russian'];
        }
        else{
            return $lang_array['English'];
        }
    }
    public function _get_invoice_title()
    {
        // // Set font
        // $this->setFont($this->get_font_name(), '', 15);

        // // Title
        // $this->writeHTMLCell(0, 50, $x ='', $y='8', strtoupper($this->_invoice_name), $border = 0, 1, 0, true, 'C', true);
        // $this->Ln();
        return $this;
    }

    public function _get_logo()
    {
        // Logo is rendered in the center column of _get_company_details()
        return $this;
    }

    /**
     * Format store mobile for PDF header, e.g. +971555164774 → + 971 55 516 4774
     */
    protected function _format_header_mobile($raw)
    {
        $raw = trim((string)$raw);
        if ($raw === '') {
            return '';
        }

        $parts = preg_split('/\s*[,;\/|]\s*/', $raw);
        $formatted = array();
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            $has_plus = (strpos($part, '+') !== false);
            $digits = preg_replace('/\D+/', '', $part);
            if ($digits === '') {
                $formatted[] = $part;
                continue;
            }

            // GCC-style: +CCC + 9 local digits → + CCC XX XXX XXXX
            $gcc = array('971', '966', '973', '974', '968', '965');
            $out = null;
            foreach ($gcc as $cc) {
                if (strpos($digits, $cc) === 0 && strlen($digits) === strlen($cc) + 9) {
                    $local = substr($digits, strlen($cc));
                    $out = '+ ' . $cc . ' ' . substr($local, 0, 2) . ' ' . substr($local, 2, 3) . ' ' . substr($local, 5, 4);
                    break;
                }
            }
            if ($out === null && strlen($digits) === 12 && $has_plus) {
                // Generic +CCCXXXXXXXXX (3+9)
                $out = '+ ' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 2) . ' ' . substr($digits, 5, 3) . ' ' . substr($digits, 8, 4);
            }
            if ($out === null && strlen($digits) === 9) {
                $out = substr($digits, 0, 2) . ' ' . substr($digits, 2, 3) . ' ' . substr($digits, 5, 4);
                if ($has_plus) {
                    $out = '+ ' . $out;
                }
            }
            if ($out === null) {
                $out = ($has_plus ? '+ ' : '') . $digits;
            }
            $formatted[] = $out;
        }

        return !empty($formatted) ? implode(', ', $formatted) : $raw;
    }
    
    public function _get_company_details()
    {
        $store = $this->store;

		$pageWidth = $this->getPageWidth();
		$isA5 = ($pageWidth < 160);
		// DejaVu supports both English and Arabic in one header
		$this->setFont('dejavusans', '', $isA5 ? 7 : 8, '', true);
		$this->setCellPadding(0);
		$this->setCellMargins(0, 0, 0, 0);

		// Bigger contact fonts + line spacing; TRN removed from header (shown beside invoice title)
		$name_fs = $isA5 ? '10' : '12';
		$detail_fs = $isA5 ? '9' : '10.5';
		$logo_w = $isA5 ? 28 : 40;
		$logo_h = $isA5 ? 18 : 24;
		$company_y = $isA5 ? 5 : 6; // top margin above the company header
		$col_w = ($pageWidth - 10) * 0.40;
		$mid_w = ($pageWidth - 10) * 0.20;

		// Company name: 1st line = Branch First Name, 2nd line = Branch Last Name (falls back to the full store name)
		$name_clean = function ($value, $upper = false) {
			$value = trim(html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8'));
			if ($upper) { $value = strtoupper($value); }
			$value = str_replace(array('&AMP;', '&amp;'), '&', $value);
			return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		};
		$en_first = $name_clean(isset($store->branch_first_name) ? $store->branch_first_name : '', true);
		$en_last = $name_clean(isset($store->branch_last_name) ? $store->branch_last_name : '', true);
		$en_name_lines = array();
		if ($en_first !== '' || $en_last !== '') {
			if ($en_first !== '') { $en_name_lines[] = $en_first; }
			if ($en_last !== '') { $en_name_lines[] = $en_last; }
		} else {
			$en_name_lines[] = $name_clean($store->store_name, true);
		}
		// Arabic side: 1st line = Store Name (Arabic), 2nd line = Last Name (Arabic); English name when no Arabic is saved
		$ar_first = $name_clean(isset($store->store_name_arabic) ? $store->store_name_arabic : '');
		$ar_last = $name_clean(isset($store->store_last_name_arabic) ? $store->store_last_name_arabic : '');
		$ar_name_lines = array();
		if ($ar_first !== '') { $ar_name_lines[] = $ar_first; }
		if ($ar_last !== '') { $ar_name_lines[] = $ar_last; }
		if (empty($ar_name_lines)) { $ar_name_lines = $en_name_lines; }
		// Same number of name lines on both sides so Mobile / Email / Website stay level
		while (count($en_name_lines) < count($ar_name_lines)) { $en_name_lines[] = '&nbsp;'; }
		while (count($ar_name_lines) < count($en_name_lines)) { $ar_name_lines[] = '&nbsp;'; }
		$store_name_en = implode('<br/>', $en_name_lines);
		$store_name_ar = implode('<br/>', $ar_name_lines);

		$mobile = !empty($store->mobile) ? $store->mobile : (!empty($store->phone) ? $store->phone : '');
		$mobile_fmt = $this->_format_header_mobile((string)$mobile);
		$mobile_esc = htmlspecialchars($mobile_fmt, ENT_QUOTES, 'UTF-8');
		// Keep + and digits LTR on both EN/AR sides
		$mobile_html = '<span dir="ltr">'.$mobile_esc.'</span>';
		$email_esc = !empty($store->email) ? htmlspecialchars((string)$store->email, ENT_QUOTES, 'UTF-8') : '';
		$website_raw = !empty($store->store_website) ? $store->store_website : (!empty($store->website) ? $store->website : '');
		$website_esc = htmlspecialchars((string)$website_raw, ENT_QUOTES, 'UTF-8');

		// Header: Company, Mobile, Email, Website (TRN moved next to TAX INVOICE title)
		$left_lines = array('<b style="font-size:'.$name_fs.'pt;">'.$store_name_en.'</b>');
		$left_lines[] = 'Mobile : '.$mobile_html;
		$left_lines[] = 'Email : '.$email_esc;
		$left_lines[] = 'Website: '.$website_esc;
		$left = '<span style="font-size:'.$detail_fs.'pt; line-height:1.55;">'.implode('<br/>', $left_lines).'</span>';

		// Arabic side: same pattern as Email/Website — value, then colon, then Arabic label
		$colon = '<span dir="ltr"> :</span> ';
		$right_lines = array('<b style="font-size:'.$name_fs.'pt;">'.$store_name_ar.'</b>');
		$right_lines[] = $mobile_html.$colon.'الجوال';
		$right_lines[] = $email_esc.$colon.'البريد';
		$right_lines[] = $website_esc.$colon.'الموقع';
		$right = '<span style="font-size:'.$detail_fs.'pt; line-height:1.55;">'.implode('<br/>', $right_lines).'</span>';

		$left_x = 5;
		$logo_x = 5 + $col_w;
		$right_x = 5 + $col_w + $mid_w;

		// ln=1 so GetY() lands BELOW the cell (ln=0 keeps Y at top → body overlapped header)
		$this->writeHTMLCell($col_w, 0, $left_x, $company_y, $left, 0, 1, false, true, 'L', true);
		$text_bottom = $this->GetY();

		$this->writeHTMLCell($col_w, 0, $right_x, $company_y, $right, 0, 0, false, true, 'R', true);
		$text_bottom = max($text_bottom, $company_y + $this->getLastH());

		if (!empty($store->store_logo) && file_exists($store->store_logo)) {
			$logo_y = $company_y + max(0, (($text_bottom - $company_y) - $logo_h) / 2);
			$this->Image($store->store_logo, $logo_x + (($mid_w - $logo_w) / 2), $logo_y, $logo_w, $logo_h, '', '', '', false, 300, '', false, false, 0, true);
			$text_bottom = max($text_bottom, $logo_y + $logo_h);
		}

		// Small gap under header text — never let body cover the company block
		$this->header_bottom_y = $text_bottom + 2;

        return $this;
    }

    public function _get_company_details2()
    {
        $store = $this->store;
        $txt='';
        
        $txt .= '<span style="font-size:16px;font-weight:500">'.$store->store_name.'</span>';
        
        $this->writeHTMLCell($w =135, 0, $x='38', $y='16', $txt, $border = 0, 0, 0, true, '', true);
        
        $txt = "";
        $txt .= '<br><span style="font-size:12px;">'.$store->address.'</span>';

        // $address_line_2 = '';
        // if(!empty($store->city)){
        //     $address_line_2 .= $store->city;
        // }
        // if(!empty($store->state)){
        //     $address_line_2 .= ", ".$store->state;
        // }
        // if(!empty($store->postcode)){
        //     $address_line_2 .= ", -".$store->postcode;
        // }
        //     $txt .= '<br><span style="font-size:12px;">'.$address_line_2.'</span>';

        $txt .= '<br><span style="font-size:12px;">'.$this->CI->lang->line('mobile').' :'.$store->mobile.'<br/>'.$this->CI->lang->line('email').'        :'.$store->email.'</span>';
        $txt .= '<br><span style="font-size:12px;">';
            // if($this->_invoice_format=='Default'){
            //   $txt .= $this->CI->lang->line('tax_number').' :</b> '.$store->vat_no; 
            // }
            // else{
            //     $txt .= $this->CI->lang->line('tax_number').' :</b> '.$store->gst_no;
            // }
        //  $txt .= $this->CI->lang->line('tax_number').' :</b> 100457407300003';
        $txt .= $this->CI->lang->line('website').'  :'.$store->store_website.'</span>';
        

        $this->setFont($this->get_font_name(), '', 14, '', true);

        $this->writeHTMLCell($w =135, 0, $x='43', $y='22', $txt, $border = 0, 0, 0, true, '', true);
        return $this;
    }
    
    public function _get_qr()
    {
        return null;
        $qr_data = $this->_get_qr_data();
    }

    public function _get_hr()
    {
        // set color for background
        $this->setFillColor(65, 59, 212);

        $this->setFont($this->get_font_name(), '', 0.80);

        $this->MultiCell('', '', $txt ='', $border =0, 'L', 1, 1, $x = '', $y = '50', true,1, true);
        return $this;
    }

    public function _is_rtl_lang()
    {   
        //Based on Session variable check RTL
        $lang = trim(strtoupper($this->CI->session->userdata('language')));

        $this->_app_language = $lang;

        $rtl_languages = array(strtoupper('arabic'),strtoupper('urdu'));

        return (in_array($lang, $rtl_languages)) ? true : false;

    }

    //Page header
    public function Header() {

        //Auto find RTL language
        $this->_rtl($this->_is_rtl_lang());

        $this->_document_details();

        // Title
        $this->_get_invoice_title();

        // Logo
        $this->_get_logo();

        // Company Details
        $this->_get_company_details();

        // Body starts just under header (with safe floor so it never covers company text)
        if (!empty($this->header_bottom_y)) {
            $min_top = ($this->getPageWidth() < 160) ? 20 : 22;
            $top = max($this->header_bottom_y, $min_top);
            $this->header_bottom_y = $top;
            $this->setTopMargin($top);
        }

        // QRCODE
        $this->_get_qr();

    }

    public function _get_qr_data(){
        $store = $this->store;
        $sales = $this->sales;
        $customer = $this->customer;

        $str = '';
        $str .= 'Seller Name:'.$store->store_name;
        $str .= PHP_EOL;
        $str .= 'Buyer Name:'.$customer->customer_name;
        $str .= PHP_EOL;
        $str .= 'Buyer Tax Number:'.$store->vat_no;
        $str .= PHP_EOL;
        $str .= 'Invoice Number:'.$sales->sales_code;
        $str .= PHP_EOL;
        $str .= 'Date & Time:'.$sales->created_date." ".$sales->created_time;
        $str .= PHP_EOL;
        $str .= 'TAX Total:'.store_number_format(get_sales_tax_total($sales->id));
        $str .= PHP_EOL;
        $str .= 'Invoice Total:'.store_number_format($sales->grand_total);
        return $str;
    }

    //set footer document details
    public function _set_document_name($value='')
    {
        $this->_document_name = $value;
    }

    //set footer document number
    public function _set_document_number($value='')
    {
        $this->_document_number = $value;
    }
    
    /**
     * Build small blue-circle PNG icons for PDF footer (arco style).
     */
    protected function _pdf_footer_icon_file($type)
    {
        $dir = APPPATH.'cache/pdf_icons/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $file = $dir.$type.'.png';
        if (is_file($file) && filesize($file) > 0) {
            return $file;
        }
        if (!function_exists('imagecreatetruecolor')) {
            return '';
        }

        $size = 64;
        $im = imagecreatetruecolor($size, $size);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $transparent);
        $blue = imagecolorallocate($im, 26, 54, 131); // #1A3683
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledellipse($im, 32, 32, 62, 62, $blue);

        if ($type === 'phone') {
            imagefilledrectangle($im, 26, 18, 38, 46, $white);
            imagefilledrectangle($im, 28, 20, 36, 24, $blue);
            imagefilledellipse($im, 32, 42, 6, 6, $blue);
        } elseif ($type === 'email') {
            imagefilledrectangle($im, 16, 22, 48, 42, $white);
            imageline($im, 16, 22, 32, 34, $blue);
            imageline($im, 48, 22, 32, 34, $blue);
            imageline($im, 16, 42, 28, 32, $blue);
            imageline($im, 48, 42, 36, 32, $blue);
        } elseif ($type === 'web') {
            imageellipse($im, 32, 32, 34, 34, $white);
            imageellipse($im, 32, 32, 18, 34, $white);
            imageline($im, 15, 32, 49, 32, $white);
            imageline($im, 32, 15, 32, 49, $white);
        } else { // pin
            imagefilledellipse($im, 32, 26, 22, 22, $white);
            $pts = array(32, 48, 22, 30, 42, 30);
            imagefilledpolygon($im, $pts, $white);
            imagefilledellipse($im, 32, 26, 8, 8, $blue);
        }

        imagepng($im, $file);
        imagedestroy($im);
        return is_file($file) ? $file : '';
    }

    protected function _pdf_footer_icon_html($type)
    {
        $file = $this->_pdf_footer_icon_file($type);
        if ($file === '') {
            return '';
        }
        // TCPDF prefers forward slashes in HTML img paths
        $src = str_replace('\\', '/', $file);
        return '<img src="'.$src.'" width="16" height="16" style="vertical-align:middle;" />';
    }

    // Page footer — Generate/Page (optional) above line, then Address + City + State + Country
    public function Footer() {
        $store = $this->store;
        $textBlack = '#000000';
        $pageWidth = $this->getPageWidth();
        $side_fs = ($pageWidth < 160) ? 8 : 9;
        $has_gen = (trim((string)$this->footer_generate_text) !== '');

        // Generate (left) + Page (right) — sit in the gap above the address line
        if ($has_gen) {
            $usable = $pageWidth - $this->lMargin - $this->rMargin;
            $this->setY(-19.5); // clear of the signature box above and the address line below
            $this->SetFont($this->get_font_name(), 'B', $side_fs); // bold Generate / Page line
            // Generate the alias after selecting its rendering font (Unicode-aware).
            $page_txt = 'Page ' . $this->getPage() . ' of ' . $this->getAliasNbPages();
            $this->SetTextColor(0, 0, 0); // black Generate / Page text
            $this->SetX($this->lMargin);
            $this->Cell($usable / 2, 4, $this->footer_generate_text, 0, 0, 'L', false, '', 0, false, 'T', 'M');
            $this->Cell($usable / 2, 4, $page_txt, 0, 1, 'R', false, '', 0, false, 'T', 'M');
            $this->SetTextColor(0, 0, 0);
        }

        $parts = array();
        $address = !empty($store->address) ? trim(html_entity_decode((string)$store->address, ENT_QUOTES, 'UTF-8')) : '';
        if ($address === '' && !empty($store->address_arabic)) {
            $address = trim(html_entity_decode((string)$store->address_arabic, ENT_QUOTES, 'UTF-8'));
        }
        if ($address !== '') {
            $parts[] = $address;
        }
        foreach (array('city', 'state', 'country') as $field) {
            if (!empty($store->$field)) {
                $val = trim(html_entity_decode((string)$store->$field, ENT_QUOTES, 'UTF-8'));
                if ($val !== '') {
                    $parts[] = $val;
                }
            }
        }
        if (empty($parts)) {
            return;
        }

        $line = implode(', ', $parts);
        $pinIcon = $this->_pdf_footer_icon_html('pin');
        $line_esc = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
        $lineGray = '#AAAAAA';
        $html = '<div style="border-top:1.5px solid '.$lineGray.';padding-top:5px;font-family:dejavusans;">'
              . '<div style="font-size:12pt;color:'.$textBlack.';line-height:1.4;text-align:center;">'
              . $pinIcon.'&nbsp;'.$line_esc
              . '</div></div>';

        $this->setY(-14);
        $this->setFont('dejavusans', '', 8, '', true);
        $this->setCellPadding(0);
        $this->writeHTMLCell(0, 0, $this->lMargin, $this->GetY(), $html, 0, 0, false, true, 'C', true);
    }

    // set document information
    public function _document_details()
    {
        $this->setCreator(PDF_CREATOR);
        $this->setAuthor('Billing Boook');
        $this->setTitle($this->page_title);
        $this->setSubject('Invoice Details');
        $this->setKeywords('TCPDF, PDF, example, test, guide');
    }

    //Document Settings
    public function _document_settings($value='')
    {
        // set default header data
        $this->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

         // Safe initial top; Header() recalculates exact bottom after drawing company block
         $pageWidth = $this->getPageWidth();
         $top_margin = ($pageWidth < 160) ? 28 : 32;
         $this->setMargins($PDF_MARGIN_LEFT=5, $PDF_MARGIN_TOP=$top_margin, $PDF_MARGIN_RIGHT=5);
         $this->setHeaderMargin(0);
         $this->header_bottom_y = 0;
        // Room for Generate/Page row + address footer
        $this->setFooterMargin(28);

        // set auto page breaks
        $this->setAutoPageBreak(TRUE, 32);

        // dark for all default-color lines/borders (boxes, hr rules)
        $this->setDrawColor(0, 0, 0);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    /**
     * Write HTML once, pinned just above the page footer text.
     * Never measure with writeHTML/startTransaction — TCPDF leaves ghost table/QR drawings.
     */
    public function write_bottom_html($html, $estimated_height_mm = 55)
    {
        if ($html === '' || $html === null) {
            return;
        }

        $currY = $this->GetY();
        $pageH = $this->getPageHeight();
        // Keep Thank You just above blue address line (small gap only)
        $footerPad = max(16, (float)$this->getFooterMargin());
        $auto = $this->getAutoPageBreak();
        $bMargin = $this->getBreakMargin();

        $height = (float)$estimated_height_mm;
        if ($height < 30) {
            $height = 60;
        }

        $avail = $pageH - $footerPad - $currY;
        if ($avail < 15 || $height >= $avail) {
            $this->SetAutoPageBreak(false, 0);
            $this->writeHTML($html, true, false, true, true, 'J');
            $this->SetAutoPageBreak($auto, $bMargin);
            return;
        }

        // Mild underestimate pulls block down closer to footer without going under it
        $height = min($height * 0.90, $avail - 2);
        $targetY = $pageH - $footerPad - $height;
        if ($targetY <= $currY) {
            $this->SetAutoPageBreak(false, 0);
            $this->writeHTML($html, true, false, true, true, 'J');
            $this->SetAutoPageBreak($auto, $bMargin);
            return;
        }

        $this->SetAutoPageBreak(false, 0);
        $this->SetY($targetY);
        $this->writeHTML($html, true, false, true, true, 'J');
        $this->SetAutoPageBreak($auto, $bMargin);
    }

    /**
     * Draw PAID seal overlay when Branch setting is Yes and payment is Paid.
     * @param string|null $payment_status e.g. Paid / Partial / Unpaid
     */
    public function draw_paid_seal($payment_status = null)
    {
        if (!function_exists('is_paid_seal_enabled') || !is_paid_seal_enabled()) {
            return;
        }
        if ($payment_status !== null && strcasecmp(trim((string)$payment_status), 'Paid') !== 0) {
            return;
        }
        $img = function_exists('get_paid_seal_image_path') ? get_paid_seal_image_path() : '';
        if ($img === '' || !is_file($img)) {
            return;
        }

        $seal_w = ($this->getPageWidth() < 160) ? 28 : 42;
        $seal_h = $seal_w;
        $x = ($this->getPageWidth() - $seal_w) / 2;
        // Place near middle of page so it reads as a stamp overlay
        $y = max(40, ($this->getPageHeight() - $seal_h) / 2 - 10);

        // Keep current page; draw on top of existing content
        $this->setAlpha(0.85);
        $this->Image($img, $x, $y, $seal_w, $seal_h, '', '', '', false, 300, '', false, false, 0, true);
        $this->setAlpha(1);
    }
}
