<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<!-- <script src="</?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script> -->
<!-- <script src="</?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script> -->
<script src="<?= base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>

<script>

  var table,id_odp_parent;
  $(document).ready(function() {
    $('.btnFokus').focus(); // fokus ke field ketika tombol tambah di klik

    table = $('#table').DataTable({
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?php echo site_url('odp/ajax_list') ?>",
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
          extend: 'csv'
        },
        {
          extend: 'excel',
          title: '<?php echo $active; ?>'
        },
        {
          extend: 'pdf',
          title: '<?php echo $active; ?>',
          exportOptions: {
            columns: [0, 1, 2, 3, 4]
          }
        },

        {
          extend: 'print',
          title: 'Daftar odp',
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


  id_odp_parent = $('#id_odp_parent').select2({
      minimumInputLength: 3, // Optional: Minimum characters to type before searching
      ajax: {
          url: '<?= site_url('odp/s2_get_data_for_select2') ?>', // Your CodeIgniter AJAX endpoint
          dataType: 'json',
          delay: 250, // Delay in milliseconds before sending the request
          data: function (params) {
              return {
                  search: params.term, // Search term from the user input
                  page: params.page // For pagination
              };
          },
          processResults: function (data, params) {
              params.page = params.page || 1;
              return {
                  results: data.items, // Array of objects with 'id' and 'text'
                  pagination: {
                      more: (params.page * 10) < data.total_count // Example for pagination
                  }
              };
          },
          cache: true
      },
      placeholder: 'Cari ODP...',
      width: "100%",
      dropdownParent : $('#myModal')

  });

</script>

<script type="text/javascript">
  var save_method;

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  }


  function adds() {
    save_method = 'add';
    description = 'SPLITTER RATIO: \nIN LASER: ';
    $('#form')[0].reset(); // reset form on modals
    $('[name="id_odp"]').val('');
    $('[name="description"]').val(description);
    $('[name="type"]').val('odp').trigger('change');
    id_odp_parent.val(null).trigger('change');
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
      url = "<?php echo site_url('odp/save_odp') ?>";
    } else {
      url = "<?php echo site_url('odp/update_odp') ?>";
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
      url: "<?php echo site_url('odp/vget_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id_odp"]').val(data.id_odp);
        $('[name="odp_name"]').val(data.odp_name);
        $('[name="latlong"]').val(data.latlong);
        $('[name="description"]').val(data.description);
        $('[name="type"]').val(data.type);
        $('[name="capacity"]').val(data.capacity);

        if(data.id_odp_parent == null) {
          id_odp_parent.val(null).trigger('change');
        } else{
          id_odp_parent.val(data.id_odp_parent).trigger('change');
        }

        $('#odpName').html("<a href='https://www.google.com/maps/?q="+data.latlong+"' class='btn btn-outline btn-sm btn-primary' target='_blank'>"+data.odp_parent_name+"</a>");

        id_odp_parent.val(data.id_odp_parent).trigger('change');

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
        url: "<?php echo site_url('odp/delete_odp') ?>/" + id,
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

//   function views(id) {
//     $.ajax({
//       url: "</?php echo site_url('odp/vget_edit/') ?>" + id,
//       type: "GET",
//       dataType: "JSON",
//       success: function(data) {
//         $('.v1').text(data.kode_karyawan);
//         $('.v2').text(data.nama_lengkap);
//         $('.v3').text(data.wilayah);
//         $('.v4').text(data.keterangan);
//         $('#DetailModal').modal('show');
//         $('.modal-title').text('Detail </?php echo ucwords(str_replace('_', ' ', $active)); ?>');
//       },
//       error: function(jqXHR, textStatus, errorThrown) {
//         notif('Gagal mengambil data!', 'Error', 'error');
//       }
//     });
//   }
</script>
</body>

</html>