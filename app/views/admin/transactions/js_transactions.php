<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<!-- <script src="</?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script> -->
<!-- <script src="</?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script> -->
<!-- <script src="</?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script> -->

<script>
  // $(function() {
  //   getSelect();
  // });

  // function getSelect() {
  //   // $('[name="id_karyawan"]').load("</?php echo site_url('getselect/pilih_mul_dua_sorted/karyawan/id_karyawan/kode_karyawan/nama_lengkap/jabatan/2')?>"); // gunakan ini jika id jabatan sudah diketahui
  //   $('[name="id_karyawan"]').load("</?php echo site_url('getselect/pilih_mul_dua/karyawan/id_karyawan/kode_karyawan/nama_lengkap/jabatan') ?>"); // gunakan ini jika id jabatan belum diketahui
  //   $('.multiSelect').load("</?php echo site_url('getselect/pilih_mul_dua/wilayah/id_wilayah/kode_wilayah/wilayah') ?>");
  // }

  // var id_karyawan = $('[name="id_karyawan"]').select2({
  //   placeholder: "Pilih Karyawan",
  //   width: "100%",
  //   // dropdownParent : $('#myModal')
  // });
  // var wilayah = $('.multiSelect').select2({
  //   placeholder: "Pilih Wilayah Penagihan",
  //   width: "100%",
  //   // dropdownParent : $('#myModal')
  // });

  var table;
  $(document).ready(function() {
    $('.btnFokus').focus(); // fokus ke field ketika tombol tambah di klik

    table = $('#table').DataTable({
      processing: true, //Feature control the processing indicator.
      serverSide: true, //Feature control DataTables' server-side processing mode.
      order: [], //Initial no order.
      // Load data for the table's content from an Ajax source
      ajax: {
        "url": "<?php echo site_url('transactions/ajax_list') ?>",
        "type": "POST",
      },
      //Set column definition initialisation properties.
      columnDefs: [{
        "targets": [-1], //last column
        "orderable": false, //set not orderable
      }, ],
      // fnDrawCallback: function (oSettings) {
      //   $('#table tbody tr').each(function () {
      //       var sTitle;
      //       var nTds = $('td', this);
      //       var s0 = $(nTds[0]).text();
      //       var s1 = $(nTds[1]).text();
      //       var s2 = $(nTds[2]).text();
      //       var s3 = $(nTds[3]).text();
      //       var s4 = $(nTds[4]).text();
      //       var s5 = $(nTds[5]).text();

      //       sTitle = "<h1>"+s0+"</h1>";

      //       this.setAttribute('rel', 'tooltip');
      //       this.setAttribute('title', sTitle);
      //       console.log(this);
      //       console.log($(this));
      //       $(this).tooltip({
      //           html: true
      //       });
      //   });
      // },

      // pageLength: 25,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        {
          extend: 'pdf',
          title: '<?php echo $active; ?>',
          exportOptions: {
            columns: [0, 1, 2, 3, 4]
          }
        },
        {
          extend: 'print',
          title: 'Daftar Transaksi',
          customize: function(win) {
            $(win.document.body).addClass('white-bg');
            $(win.document.body).css('font-size', '10px');

            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', 'inherit');
          },
          exportOptions: {
            columns: [0, 1, 2, 3, 4]
          }
        }
      ]

    });

    $("input").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("textarea").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    // $("select").change(function(){
    //     $(this).parent().parent().removeClass('has-error');
    //     $(this).next().empty();
    // });

  });
</script>

</body>

</html>