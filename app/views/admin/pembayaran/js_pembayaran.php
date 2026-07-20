
<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/iCheck/icheck.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/typehead/bootstrap3-typeahead.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>

<!-- Ladda -->
<script src="<?php echo base_url('assets/inspinia271/js/plugins/ladda/spin.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/ladda/ladda.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/ladda/ladda.jquery.min.js') ?>"></script>


<script>
  var l = $( '.ladda-button-demo' ).ladda();
  
let noIntenet     = '';
let kodeInvoice   = '';
let totalAmount   = 0;
let paymentMethod = '';

function getDetailInvoice(nopel) {
  // $.post("</?=site_url('pembayaran/getDetailInvoice') ?>",
  $.post("<?=site_url('pembayaran/get_detail_invoice') ?>",
  {
    no_internet: nopel,
  },
  function(response, status){
    $('#resDetailInvoice').html(response.html);
    $('#resultcari').hide();
    $('#panelDetail').show();

    noIntenet     = response.data.data.account.no_internet;
    kodeInvoice   = response.data.data.billing.kode_invoice;
    totalAmount   = response.data.data.billing.total_amount;
    paymentMethod = '';
    // console.log(data);
  }, "json");
}

function payNow() {
  alert(noIntenet + " === " + kodeInvoice + " === " + totalAmount);
  // $.post("</?=site_url('pembayaran/proses_pembayaran') ?>",
  // {
  //   no_internet: noIntenet,
  //   kode_invoice: kodeInvoice,
  //   total_amount: totalAmount
  // },
  // function(response, status){
  //   // console.log(response);
  // }, "json");
}




  $(function() {
    getSelect();
  });

  function getSelect() {
    $('[name="id_karyawan"]').load("<?php echo site_url('getselect/pilih_mul_dua/karyawan/id_karyawan/kode_karyawan/nama_lengkap/') ?>"); // gunakan ini jika id jabatan belum diketahui
  }

  var id_karyawan = $('[name="id_karyawan"]').select2({
    placeholder: "Pilih Nama Penerima",
    width: "100%",
    // dropdownParent : $('#myModal')
  });


  $(document).ready(function() {

// Bind normal buttons
  Ladda.bind( '.ladda-button',{ timeout: 2000 });

  // Bind progress buttons and simulate loading progress
  Ladda.bind( '.progress-demo .ladda-button',{
      callback: function( instance ){
          var progress = 0;
          var interval = setInterval( function(){
              progress = Math.min( progress + Math.random() * 0.1, 1 );
              instance.setProgress( progress );

              if( progress === 1 ){
                  instance.stop();
                  clearInterval( interval );
              }
          }, 200 );
      }
  });


  // var l = $( '.ladda-button-demo' ).ladda();

  l.click(function(){
      // Start loading
      l.ladda( 'start' );

      // Timeout example
      // Do something in backend and then stop ladda
      // setTimeout(function(){
      //     l.ladda('stop');
      // },12000)


  });


    $('.typeahead_1').typeahead({
      source: ["RUSAK TV ", "RUSAK SEJAK ", "NON AKTIF ", "KENA PETIR ", "LUNAS s/d ", "JAN ", "FEB ", "MARET ", "APRIL ", "MEI ", "JUNI ", "JULI ", "AGUSTUS ", "SEPT ", "OKT ", "NOV ", "DES ", "<?php echo date('Y') ?>", "<?php echo date('Y') + 1; ?>"]
    });

    $('#coba').click(function() {
      var pageTitle = 'HASIL SETORAN',
        stylesheet = '<?php echo base_url('assets/inspinia271/css/bootstrap.min.css') ?>',
        win = window.open('', 'Print', 'width=500,height=300');
      win.document.write('<html><head><title>' + pageTitle + '</title>' +
        '<link rel="stylesheet" href="' + stylesheet + '">' +
        '</head><body>' + $('.cetak')[0].outerHTML + '</body></html>');
      win.print();
    });

  });

</script>

<script>
  $(document).ready(function() {

    var delayTimer;
    $('#search_input').on('input', function() {
        var keyword = $(this).val();
        clearTimeout(delayTimer);
        
        delayTimer = setTimeout(function() {
            if (keyword.length >= 3) {
                // Jalankan fungsi AJAX di sini
                getClient(keyword);
            }
        }, 300); // Menunggu 300 milidetik setelah ketukan terakhir
    });

    function getClient(keyword) {
      $.ajax({
          url: '<?=site_url('pembayaran/cari')?>', // Ubah dengan route/prosesor backend Anda
          type: 'GET',
          data: { cari: keyword },
          dataType: 'json',
          beforeSend: function() {
              // Opsional: Tampilkan loading spinner atau teks "Mencari..."
              $('#resultcari').show();
              $('#tbhasil').html('<tr><td colspan="3">Mencari data...</td></tr>');
          },
          success: function(response) {
              var html = '';
              
              if(response.length > 0) {
                html += response;
              } else {
                  html = '<tr><td colspan="3">Data tidak ditemukan</td></tr>';
              }
              
              // Masukkan hasil ke dalam tabel/container hasil pencarian
              $('#tbhasil').html(html);
              $('#resultcari').show();
          },
          error: function() {
              $('#tbhasil').html('<tr><td colspan="3">Terjadi kesalahan sistem.</td></tr>');
          }
      });
    }




    $('#cari').on('keypress', function(e) {
        // Cek apakah tombol yang ditekan adalah Enter (kode 13)
        if (e.which === 13) {
            e.preventDefault(); // Mencegah form reload atau aksi default
            var noInet = document.getElementById("search_input");
            // alert('enter');
            getClient(noInet);   // Panggil fungsi Anda
        }
    });

    


      // function cariTagihan() {
      //   $.post("</?php echo site_url('pembayaran/cari') ?>",
      //   {
      //     cari: $('#cari').val(),
      //   },
      //   function(data, status){
      //     $('#tbhasil').html(data);
      //     $('#resultcari').show();
      //     $('#panelDetail').hide();
      //     l.ladda('stop');
      //     // $('#resModal').modal('show');
      //     // console.log(data);
      //   });
      // }

      // integrasi billing API
      // function getInvoice(params) {
      //   $.get("</?= site_url('billing_api/getBill') ?>",
      //   {
      //     cari: $('#cari').val(),
      //   },
      //   function(data, status){
      //     $('#tbhasil').html(data);
      //     $('#resultcari').show();
      //     $('#panelDetail').hide();
      //     l.ladda('stop');
      //     // $('#resModal').modal('show');
      //     // console.log(data);
      //   });
      // }
      

      
  });

</script>

</body>

</html>