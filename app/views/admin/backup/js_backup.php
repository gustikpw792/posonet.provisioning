<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>

<script>
  var tabel, start_time;
  $(document).ready(function() {
    tabel = $('#table').DataTable({
      ajax: {
        url: "<?php echo site_url('backup/listfiles') ?>"
      },
      order: [2, "desc"],
      columnDefs: [{
          targets: [-1],
          orderable: false
        },
        {
          visible: false,
          targets: [1]
        }
      ],
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [{
          text: '<span class="text-success"><i class="fa fa-plus"></i> Backup now</span>',
          action: function(e, dt, node, config) {
            adds();
          }
        },
        {
          text: '<span class="text-default"><i class="fa fa-refresh"></i> Reload</span>',
          action: function(e, dt, node, config) {
            reload();
          }
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
              '<tr class="group active"><td colspan="3" class="font-bold">' + group + '</td></tr>'
            );

            last = group;
          }
        });
      },
    });
  });

  function reload() {
    tabel.ajax.reload(null, false);
  }

  function adds() {
    $.ajax({
      url: "<?php echo site_url('backup/backup/') ?>",
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          notif(data.message, 'Sukses', 'success');
          reload();
        } else {
          notif(data.message, 'Gagal', 'error');
          reload();
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengambil data!', 'Error', 'error');
      }
    });
  }
</script>
</body>

</html>