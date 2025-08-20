<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>

<script>
$('.date').datepicker({
    minViewMode: 1,
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: true,
    autoclose: true,
    format: "yyyy-mm"
});
var wilayah = $('[name="wilayah"]').select2({
  placeholder : "Pilih Wilayah",
  width : "100%"
});

var table;
$(document).ready(function(){
  $('.btnFokus').focus(); // fokus ke field ketika tombol tambah di klik
});

</script>

<script>
var tabel, start_time;
$(document).ready(function(){
   tabel = $('#table').DataTable({
      ajax : {
         url : "<?php echo site_url('kwitansi/files2')?>"
      },
      order: [ 2, "desc" ],
      columnDefs:[
         {
            targets: [ -1 ],
            orderable: false
         },
         { visible: false, targets: [ 1 ] }
      ],
      drawCallback: function ( settings ) {
         var api = this.api();
         var rows = api.rows( {page:'current'} ).nodes();
         var last=null;

         api.column(1, {page:'current'} ).data().each( function ( group, i ) {
             if ( last !== group ) {
                 $(rows).eq( i ).before(
                     '<tr class="group active"><td colspan="3" class="font-bold">'+group+'</td></tr>'
                 );

                 last = group;
             }
         } );
      },
   });
});

$(function(){
  getSelect();
});

function getSelect() {
  $('[name="wilayah"]').load("<?php echo site_url('getselect/pilih_mul/wilayah/kode_wilayah/wilayah')?>");
}

function regInvoice()
{
  if (wilayah.val() === '' || $('[name="bulan_penagihan"]').val() === '' || $('[name="sandi"]').val() === '') {
    alert('Isi kolom yang kosong!');
  }
  else {
    if(confirm('Data Invoice "'+wilayah.val()+'", Bulan Penagihan : "'+$('[name="bulan_penagihan"]').val()+'" akan didaftarkan ke sistem. Yakin?'))
    {
      $('#ibox2').children('.ibox-content').toggleClass('sk-loading');
      $.ajax({
          url : "<?php echo site_url('kwitansi/createInvCode')?>",
          data : $('#formKwitansi').serialize(),
          type: "POST",
          dataType: "JSON",
          beforeSend : function(){
            // Menampilkan waktu loading
            start_time = new Date().getTime();
          },
          success: function(data) {
            // Time load ajax
            request_time = new Date().getTime() - start_time;
            time_msg = data.pesan + '<br> Waktu : '+ waktu(request_time) +' detik';
            notif(time_msg,data.title,data.msgtype);
            // $('[name="bulan_penagihan"]').val('');
            wilayah.val('').trigger('change');
            reload_table();
            $('#ibox2').children('.ibox-content').toggleClass('sk-loading');
        },
        error: function (jqXHR, textStatus, errorThrown) {
           $('#ibox2').children('.ibox-content').toggleClass('sk-loading');
          notif('Gagal mengambil data! <br>'+textStatus,'Error','error');
        }
      });
    }
  }
}

function hapusFile(id)
{
  if (confirm('Yakin menghapus kwitansi '+id+'? \nHati-hati dalam menghapus Tagihan.\nTagihan ini telah terdaftar di database dan akan terhapus!\nApabila tagihan ini sudah dicetak dan akan disetor, sistem akan menolak karena data sudah terhapus!')) {
    var sandiInvoice = $('[name="sandi"]').val();
    if (sandiInvoice == '') {
      alert('Sandi Invoice belum diisi!'+sandiInvoice);
      $('[name="sandi"]').focus();
    } else {
      $.ajax({
        url : "<?php echo site_url('kwitansi/hapusFile/')?>"+id,
        type: "POST",
        dataType: "JSON",
        data : $('[name="sandi"]').serialize(),
        success: function(data) {
          if (data.status) {
            notif(data.msg,'Sukses','success');
            reload_table()
          } else {
            notif(data.msg,'Error','error');
            reload_table()
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          notif('Gagal menghapus data!','Error','error');
        }
      });
    }
  }
}

function deleteChache() {
     $.ajax({
       url : "<?php echo site_url('kwitansi/hapusTempAll')?>",
       type: "POST",
       dataType: "JSON",
       success: function(data) {
         if (data.status) {
           notif('Berhasil menghapus cache kwitansi!','Sukses','warning');
           reload_table();
         }
       },
       error: function (jqXHR, textStatus, errorThrown) {
         notif('Gagal menghapus cache!','Error','error');
       }
     });
}

function waktu(mili) {
  var menit = Math.floor(mili / 60000);
  var detik = ((mili % 60000) / 1000).toFixed(0);
  res = menit + ":" + (detik < 10 ? '0' : '') + detik;
  return res;
}

function reload_table() {
   tabel.ajax.reload(null,false);
}
</script>
</body>

</html>
