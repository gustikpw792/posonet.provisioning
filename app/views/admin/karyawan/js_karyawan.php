<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>

<script>
  var table;
  $(document).ready(function() {
    $('.btnFokus').focus(); // fokus ke field ketika tombol tambah di klik
    $('.date').datepicker({
      todayBtn: "linked",
      keyboardNavigation: false,
      forceParse: false,
      calendarWeeks: true,
      autoclose: true,
      format: "yyyy-mm-dd"
    });

    table = $('#table').DataTable({
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?php echo site_url('karyawan/ajax_list') ?>",
        "type": "POST"
      },
      //Set column definition initialisation properties.
      "columnDefs": [{
        "targets": [-1], //last column
        "orderable": false, //set not orderable
      }, ],

      // pageLength: 25,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [{
          text: '<span class="text-success"><i class="fa fa-plus"></i> Tambah</span>',
          action: function(e, dt, node, config) {
            adds();
          }
        },
        {
          extend: 'copy'
        },
        // {extend: 'csv'},
        // {extend: 'excel', title: '</?php echo $active; ?>'},
        // {extend: 'pdf', title: '</?php echo $active; ?>'},

        {
          extend: 'print',
          customize: function(win) {
            $(win.document.body).addClass('white-bg');
            $(win.document.body).css('font-size', '10px');

            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', 'inherit');
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
    $("select").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });

  });
</script>

<script type="text/javascript">
  $(function() {
    getSelect();
  });

  function getSelect() {
    $('[name="status"]').load("<?php echo site_url('getselect/get_enum_values/karyawan/status') ?>");
  }
  var save_method;

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  }


  function adds() {
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('[name="id_karyawan"]').val('');
    $('#myModal').modal('show'); // show bootstrap modal
    $('.help-block').empty();
    $('.fokus').focus();
    $('.modal-title').text('Add <?php echo ucwords(str_replace('_', ' ', $active)); ?>'); // Set Title to Bootstrap modal title
  }

  function save() {
    // $('#ibox1').children('.ibox-content').toggleClass('sk-loading');
    $('#btnSave').text('Saving...'); //change button text
    $('#btnSave').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?php echo site_url('karyawan/save_karyawan') ?>";
    } else {
      url = "<?php echo site_url('karyawan/update_karyawan') ?>";
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: $('#form').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) //if success close modal and reload ajax table
        {
          $('#myModal').modal('hide');
          reload_table();
          $('.btnFokus').focus();
          notif('Berhasil menambah/edit data!', 'Sukses', 'success');
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSave').text('Save changes'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengUpdate data!', 'Error', 'error');
        $('#btnSave').text('Save changes'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      }
    });
  }

  function edits(id) {
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $.ajax({
      url: "<?php echo site_url('karyawan/get_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id_karyawan"]').val(data.id_karyawan);
        $('[name="kode_karyawan"]').val(data.kode_karyawan);
        $('[name="nama_karyawan"]').val(data.nama_lengkap);
        $('[name="status"]').val(data.status);
        $('[name="tgl_masuk"]').val(data.tgl_masuk);
        $('[name="tgl_berakhir"]').val(data.tgl_berakhir);
        $('[name="alamat"]').val(data.alamat);
        $('[name="telp"]').val(data.telp);
        $('#myModal').modal('show');
        $('.modal-title').text('Edit <?php echo ucwords(str_replace('_', ' ', $active)); ?>');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengambil data!', 'Error', 'error');
      }
    });
  }

  function deletes(id) {
    if (confirm('Are you sure delete this data?')) {
      $.ajax({
        url: "<?php echo site_url('karyawan/delete_karyawan') ?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          notif('Berhasil menghapus data!', 'Sukses', 'success');
          reload_table();
          $('.btnFokus').focus();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          notif('Gagal menghapus data!', 'Error', 'error');
        }
      });

    }
  }

  function views(id) {
    $.ajax({
      url: "<?php echo site_url('karyawan/get_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        // $('.v1').text(data.id_karyawan);
        $('.v1').text(data.kode_karyawan);
        $('.v2').text(data.nama_lengkap);
        $('.v5').text(data.status);
        $('.v7').text(data.tgl_masuk);
        $('.v4').text(data.alamat);
        $('.v8').text(data.telp);
        $('#DetailModal').modal('show');
        $('.modal-title').text('Detail <?php echo ucwords(str_replace('_', ' ', $active)); ?>');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengambil data!', 'Error', 'error');
      }
    });
  }
</script>
</body>

</html>