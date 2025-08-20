<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/typehead/bootstrap3-typeahead.min.js') ?>"></script>

<script>
  var table, table2;
  var typeaheadSource;


  $(document).ready(function() {
    getSummary(null);

    $.get('<?= site_url('pengeluaran/autocompletes') ?>', function(data) {
      $(".typeahead").typeahead({
        source: data
      });
    }, 'json');

    $('.date').datepicker({
      todayBtn: "linked",
      keyboardNavigation: false,
      forceParse: false,
      calendarWeeks: true,
      autoclose: true,
      format: "yyyy-mm-dd"
    });

    $('.btnFokus').focus();

    table = $('#table').DataTable({
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?php echo site_url('pengeluaran/ajax_list') ?>",
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
        {
          extend: 'print',
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

    tabel2 = $('#tablepengeluaran').DataTable({
      ajax: {
        url: "<?php echo site_url('pengeluaran/monthly') ?>"
      },
      order: [0, "desc"],
      columnDefs: [{
        targets: [-1],
        orderable: false
      }, ],
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": false,
      "bInfo": true,
      "bAutoWidth": true
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
  var save_method;

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
    table2.ajax.reload(null, false); //reload datatable ajax
  }

  function adds() {
    $('#btnSave').text('Save changes'); //change button text
    $('#btnSave').attr('disabled', false); //set button enable
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('[name="id_pengeluaran"]').val('');
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
      url = "<?php echo site_url('pengeluaran/save_pengeluaran') ?>";
    } else {
      url = "<?php echo site_url('pengeluaran/update_pengeluaran') ?>";
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
          getSummary();
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
    $('#btnSave').text('Save changes'); //change button text
    $('#btnSave').attr('disabled', false); //set button enable
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $.ajax({
      url: "<?php echo site_url('pengeluaran/get_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id_pengeluaran"]').val(data.id_pengeluaran);
        $('[name="nama_pengeluaran"]').val(data.nama_pengeluaran);
        $('[name="tgl_pengeluaran"]').val(data.tgl_pengeluaran);
        $('[name="jumlah"]').val(data.jumlah);
        $('[name="keterangan"]').val(data.keterangan);
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
        url: "<?php echo site_url('pengeluaran/delete_pengeluaran') ?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          notif('Berhasil menghapus data!', 'Sukses', 'success');
          reload_table();
          $('.btnFokus').focus();
          getSummary();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          notif('Gagal menghapus data!', 'Error', 'error');
        }
      });

    }
  }

  function views(id) {
    $.ajax({
      url: "<?php echo site_url('pengeluaran/get_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('.v1').text(data.nama_pengeluaran);
        $('.v2').text(data.tgl_pengeluaran);
        $('.v3').text(data.jumlah);
        $('.v4').text(data.keterangan);
        $('#DetailModal').modal('show');
        $('.modal-title').text('Detail <?php echo ucwords(str_replace('_', ' ', $active)); ?>');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengambil data!', 'Error', 'error');
      }
    });
  }

  function getSummary(bulan = null) {
    $.ajax({
      url: "<?php echo site_url('pengeluaran/getSummary/') ?>",
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('.vs1').html('Rp ' + data.thisMonth);
        $('.vs2').html('Rp ' + data.lastMonth);
        $('.vs3').html('Rp ' + data.thisYear);
        $('.vs4').html('Rp ' + data.lastYear);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengambil data!', 'Error', 'error');
      }
    });
  }
</script>
</body>

</html>