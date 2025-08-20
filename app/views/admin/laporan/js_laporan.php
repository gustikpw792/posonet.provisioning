<!-- <script src="<?php //echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') 
                  ?>"></script> -->
<script src="<?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script>
<script>
  $(function() {
    getSelect();
  });

  function getSelect() {
    $('.id_wilayah').load("<?php echo site_url('getselect/pilih_mul_dua/wilayah/id_wilayah/kode_wilayah/wilayah') ?>");
  }

  /*
    Script Laporan Penagihan
  */

  function export_lap_pelanggan(format) {
    if ($('[name="id_wilayah"]').val() == '') {
      alert('Pilih wilayah!');
    } else {
      link = "<?= site_url('laporan/exportplgn/') ?>" + $('[name="id_wilayah"]').val() + '/' + format;
      if (window.open(link, '_self')) {
        notif('Tunggu beberapa saat! Laporan akan terunduh otomatis pada browser!', 'Export success!', 'success');
      }
    }
  }

  function export_lap_penagihan(format) {
    if ($('[name="id_wilayah"]').val() == '') {
      alert('Pilih wilayah!');
    } else {
      link = "<?= site_url('laporan/export_tagihan_by/') ?>" + $('#id_wilayah').val() + '/' + $('[name="tahun_laporan"]').val() + '/' + format;
      if (window.open(link, '_self')) {
        notif('Tunggu beberapa saat! Laporan akan terunduh otomatis pada browser!', 'Export success!', 'success');
      }
    }
  }
</script>

</body>

</html>