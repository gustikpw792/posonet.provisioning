<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>

<script>
  $(function() {
    getSelect();
  });

  function getSelect() {
    $('[name="id_kolektor"]').load("<?php echo site_url('getselect/pilih_mul_dua/v_kolektor/id_kolektor/kode_karyawan/nama_lengkap') ?>");
  }

  var id_kolektor = $('[name="id_kolektor"]').select2({
    placeholder: "Pilih Kolektor",
    width: "100%",
    // dropdownParent : $('#myModal')
  });

  var table;
  $(document).ready(function() {

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
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        "url": "<?php echo site_url('master_setoran/ajax_list') ?>",
        "type": "POST"
      },
      "columnDefs": [{
          "targets": [-1],
          "orderable": false,
        },
        {
          visible: false,
          targets: [1]
        }
      ],
      drawCallback: function(settings) {
        var api = this.api();
        var rows = api.rows({
          page: 'current'
        }).nodes();
        var last = null;

        api.column(1, {
          page: 'current'
        }).data().each(function(group, i) {
          if (last !== group) {
            $(rows).eq(i).before(
              '<tr class="group active"><td colspan="5" class="font-bold">' + group + '</td></tr>'
            );

            last = group;
          }
        });
      },

      // pageLength: 25,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [{
          text: '<span class="text-success"><i class="fa fa-plus"></i> Tambah</span>',
          action: function(e, dt, node, config) {
            adds();
          }
        },
        //    { text: 'Reload',
        //       action: function ( e, dt, node, config ) {
        //           table.ajax.reload();
        //    }},
        //    { extend: 'copy'},
        //    {extend: 'csv'},
        //    {extend: 'excel', title: '<//?=//$active ?>',
        //    exportOptions: {
        //           columns: [ 0, 1, 2 ]
        //      }
        //   },
        //    {  extend: 'pdfHtml5',
        //        orientation: 'portrait',
        //        pageSize: 'A4',
        //        title: '<//?=//$active ?>',
        //        exportOptions: {
        //               columns: [ 0, 1, 2 ]
        //       }
        //    },
        //    {extend: 'print',
        //       customize: function (win){
        //             $(win.document.body).addClass('white-bg');
        //             $(win.document.body).css('font-size', '10px');
        //
        //             $(win.document.body).find('table')
        //                     .addClass('compact')
        //                     .css('font-size', 'inherit');
        //       }
        //    }
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
    // $("select").change(function() {
    //   $(this).parent().parent().removeClass('has-error');
    //   $(this).next().empty();
    // });


  });
</script>

<script type="text/javascript">
  var save_method;

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  }

  function adds() {
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('[name="id_master_setoran"]').val('');
    $('#myModal').modal('show'); // show bootstrap modal
    id_kolektor.val(null).trigger('change');
    $('.help-block').empty();
    $('.fokus').focus();
    $('.modal-title').text('Add <?php echo ucwords(str_replace('_', ' ', $active)); ?>'); // Set Title to Bootstrap modal title
  }

  function save() {
    $('#ibox1').children('.ibox-content').toggleClass('sk-loading');
    $('#btnSave').text('Saving...'); //change button text
    $('#btnSave').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?php echo site_url('master_setoran/save') ?>";
    } else {
      url = "<?php echo site_url('master_setoran/update') ?>";
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
        } else if (!data.status && data.msg != null) {
          notif(data.msg, 'Duplikat', 'error');
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
      url: "<?php echo site_url('master_setoran/get_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id_master_setoran"]').val(data.id_master_setoran);
        $('[name="tgl_setoran"]').val(data.tgl_setoran);
        id_kolektor.val(data.id_kolektor).trigger('change');
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
    if (confirm('Perhatian!\nSemua setoran yang telah di-scan akan dihapus dalam database.\nYakin menghapus data setoran kolektor ini?')) {
      $.ajax({
        url: "<?php echo site_url('master_setoran/delete') ?>/" + id,
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

  // function views(id) {
  //   $.ajax({
  //     url: "</?php echo site_url('master_setoran/get_edit/') ?>" + id,
  //     type: "GET",
  //     dataType: "JSON",
  //     success: function(data) {
  //       $('.v1').text(data.id_master_setoran);
  //       $('.v2').text(data.tgl_setoran);
  //       $('.v2').text(data.wilayah);
  //       $('.v3').text(data.keterangan);
  //       $('#DetailModal').modal('show');
  //       $('.modal-title').text('Detail </?php echo ucwords(str_replace('_', ' ', $active)); ?>');
  //     },
  //     error: function(jqXHR, textStatus, errorThrown) {
  //       notif('Gagal mengambil data!', 'Error', 'error');
  //     }
  //   });
  // }
</script>
</body>

</html>