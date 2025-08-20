<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->

  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/inspinia271/img/favicon/apple-touch-icon.png') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/inspinia271/img/favicon/favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/inspinia271/img/favicon/favicon-16x16.png') ?>">
  <link rel="manifest" href="<?php echo base_url('assets/inspinia271/img/favicon/site.webmanifest') ?>">

  <title><?= ucwords(str_replace('_', ' ', ($active == 'pelanggan') ? 'ONU' : $active)) . " | " . 'Provisioning'//$profilP->nama_perusahaan; ?></title>

  <link href="<?= base_url('assets/inspinia271/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/inspinia271/font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/inspinia271/css/animate.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/inspinia271/css/style.css') ?>" rel="stylesheet">
  

  <?php if ($active == 'pembayaran') : ?>
    <link href="<?php echo base_url('assets/inspinia271/css/plugins/ladda/ladda-themeless.min.css') ?>" rel="stylesheet">
  <?php endif; ?>

  <?php if ($active != 'profil_perusahaan') : ?>
    <link href="<?php echo base_url('assets/inspinia271/css/plugins/dataTables/datatables.min.css') ?>" rel="stylesheet">
  <?php endif; ?>

  <?php if ($active == 'karyawan' || $active == 'pelanggan' || $active == 'perbaikan_gangguan' || $active == 'kwitansi' || $active == 'master_setoran' || $active == 'dashboard' || $active == 'pengeluaran' || $active == 'wa_notif') : ?>
    <link href="<?php echo base_url('assets/inspinia271/css/plugins/datapicker/datepicker3.css') ?>" rel="stylesheet">
  <?php endif; ?>

  <?php if ($active == 'detail_setoran') : ?>
    <style media="screen">
      .vidscan {
        width: 160px !important;
        height: auto !important;
      }
    </style>
    <script type="text/javascript" src="<?php echo base_url('assets/instascan/download/instascan.min.js') ?>"></script>
  <?php endif; ?>

  <?php if ($active == 'pelanggan' || $active == 'kwitansi' || $active == 'master_setoran' || $active == 'kolektor' || $active == 'detail_setoran' || $active == 'wa_notif' || $active == 'settings') : ?>
    <link href="<?= base_url('assets/inspinia271/css/plugins/select2/select2.min.css') ?>" rel="stylesheet">
    <style media="screen">
      .select2-close-mask {
        z-index: 2199;
      }

      .select2-dropdown {
        z-index: 2200;
      }
    </style>
  <?php endif; ?>
  <?php if ($active == 'pengeluaran') : ?>
    <style media="screen">
      .typeahead {
        z-index: 2200;
      }
    </style>
  <?php endif; ?>

  <link href="<?php echo base_url('assets/inspinia271/css/plugins/toastr/toastr.min.css') ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/inspinia271/css/plugins/ladda/ladda-themeless.min.css') ?>" rel="stylesheet">


</head>