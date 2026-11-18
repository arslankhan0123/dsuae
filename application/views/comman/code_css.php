  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$page_title;?></title>
  <link rel='shortcut icon' href='<?php echo $theme_link; ?>images/favicon.ico' />
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>bootstrap/css/bootstrap.min.css">
   <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>css/ionicons-2.0.1/css/ionicons.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/select2/select2.min.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/extensions/FixedHeader-3.1.4/css/fixedHeader.dataTables.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/extensions/FixedHeader-3.1.4/css/fixedHeader.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/extensions/Responsive-2.2.2/css/responsive.dataTables.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/extensions/Responsive-2.2.2/css/responsive.bootstrap.min.css">  
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/DataTables-1.10.18/extensions/Buttons-1.5.4/css/buttons.bootstrap.min.css">  
  <!-- end -->
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/newcustom.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/skins/_all-skins.min.css">
  <!-- bootstrap date-range-picker -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/datepicker/datepicker3.css">
 <!--Toastr notification -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>toastr/toastr.css">
  <!--Toastr notification end-->
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/iCheck/square/orange.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/iCheck/square/blue.css">
  <style>
    /* PDF Options: center on screen */
    #pdf-part-number-modal {
      text-align: center;
      padding: 0 !important;
    }
    #pdf-part-number-modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
    }
    #pdf-part-number-modal .modal-dialog {
      display: inline-block;
      text-align: left;
      vertical-align: middle;
      margin: 0 auto;
    }
    /* PDF Options: header/footer light blue, body white */
    #pdf-part-number-modal .modal-content {
      background-color: #fff;
      border: none;
      overflow: hidden;
    }
    #pdf-part-number-modal .modal-header {
      background-color: #3c8dbc;
      color: #fff;
      border-bottom: none;
    }
    #pdf-part-number-modal .modal-title {
      color: #fff;
    }
    #pdf-part-number-modal .modal-header .close {
      color: #fff;
      opacity: 0.9;
      text-shadow: none;
    }
    #pdf-part-number-modal .modal-header .close:hover {
      color: #fff;
      opacity: 1;
    }
    #pdf-part-number-modal .modal-body {
      background-color: #fff;
      color: #333;
    }
    #pdf-part-number-modal .modal-body label {
      color: #333;
      font-weight: normal;
    }
    #pdf-part-number-modal .modal-footer {
      background-color: #3c8dbc;
      border-top: none;
    }
    #pdf-part-number-modal .modal-footer .btn-default {
      background-color: #e6e6e6;
      border-color: #adadad;
      color: #333;
    }
    #pdf-part-number-modal .modal-footer .btn-default:hover {
      background-color: #d4d4d4;
      border-color: #8c8c8c;
      color: #222;
    }
    #pdf-part-number-modal .modal-footer .btn-primary {
      background-color: #245269;
      border-color: #1a3c4d;
      color: #fff;
    }
    #pdf-part-number-modal .modal-footer .btn-primary:hover {
      background-color: #1a3c4d;
      border-color: #122b37;
      color: #fff;
    }
  </style>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--Custom Css File-->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/custom.css?v=<?php echo time(); ?>">
  <!-- <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/sidebar.css"> -->

  <!-- Autocomplete -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/autocomplete/autocomplete.css">
  <!-- Pace Loader -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/pace/pace.min.css">
  <?php 
      $lang = trim(strtoupper($this->session->userdata('language')));
      if($lang==strtoupper('arabic') || $lang==strtoupper('urdu')) {?>
  <!-- RTL For arabic styles -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>bootstrap/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/AdminLTE.rtl.min.css">
  <?php } ?>
  <!-- Theme color finder -->
  <script type="text/javascript">
   var theme_skin = 'skin-blue' ;
  var sidebar_collapse = 'skin-blue';
  </script>
  <!-- jQuery 2.2.3 -->
  <script src="<?php echo $theme_link; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
  