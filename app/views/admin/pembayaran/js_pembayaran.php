
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

function cariz() {
  $.post("<?php echo site_url('pembayaran/cari') ?>",
  {
    cari: $('#cari').val(),
  },
  function(data, status){
    $('#tbhasil').html(data);
    $('#resultcari').show();
    $('#panelDetail').hide();
    l.ladda('stop');
    $('#resModal').modal('show');
    // console.log(data);
  });
}

function getDetailInvoice(nopel) {
  $.post("<?php echo site_url('pembayaran/getDetailInvoice') ?>",
  {
    no_pelanggan: nopel,
  },
  function(data, status){
    $('#resDetailInvoice').html(data);
    $('#resultcari').hide();
    $('#panelDetail').show();
    // console.log(data);
  });
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

  var dataScan, tabelku, kode_invoice, row_index, id_master_setoran, listKamera;
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    listScanned($("#id_master_setoran").val());
    // Mengambil index row tabel untuk update keterangan & remark
    $('#table2 tbody').on('click', 'td', function() {
      row_index = tabelku.row(this).index();
    });
    id_master_setoran = $("#id_master_setoran").val();
  });

  // InstaScan QRCode
  let scanner = new Instascan.Scanner({
    video: document.getElementById('preview'),
    scanPeriod: 5
  });
  scanner.addListener('scan', function(content) {
    getDetail(content, 'kamera');
    kode_invoice = content;
  });
  Instascan.Camera.getCameras().then(function(cameras) {
    listKamera = cameras;
    if (cameras.length > 0) {
      let kamera = "";
      for (let i = 0; i < cameras.length; i++) {
        kamera += "<button class='btn btn-xs' title='" + cameras[i].name + "' onclick='scanner.start(listKamera[" + i + "])'> Cam " + i + "</button> ";
      }
      $('.list-cam').html(kamera);
      // var selectedCam = cameras[0];
      scanner.start(cameras[0]);
      // $(".welcome-message").html("<span class='font-bold text-success' style='font-size:12pt; font-weight:bold'>" + selectedCam.name + "</span>");
    } else {
      // $(".welcome-message").html("<span class='font-bold text-danger' style='font-size:12pt; font-weight:bold'>No cameras found</span>");
      console.error('No cameras found.');
    }
  }).catch(function(e) {
    console.error(e);
    // alert(e);
  });

  var cekMethod = '';

  function getDetail(kode, cekMethod) {
    var url, kode;
    if (cekMethod == 'inputmanual') {
      kode = $("[name='kode_invoice']").val();
      if (kode == '') {
        alert('KODE INVOICE kosong!');
        return;
      }
    } else {
      kode = kode;
    }

    url = "<?= site_url('detail_setoran/checkInvoice/') ?>" + $('#id_master_setoran').val() + '/' + kode + '/' + $("[name='id_kolektor']").val() + '/' + $('#id_karyawan_kolektor').val();

    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        if (data.code == 0 || data.code == 2 || data.code == 3) {
          // play sound
          let gvoice2 = document.querySelector("audio#myAudioError");
          gvoice2.play();
          notif(data.message, data.title, 'error');
        } else {
          load_inserted($("#id_master_setoran").val(), kode);
          console.log(data.data);
        }
      },
      error: function(jqXHR, errorThrown, textStatus) {
        alert('error getting data from server!' + textStatus);
      }
    });

  }

  // function listScanned(id_master_setoran) {
  //   $.ajax({
  //     url: "<?= site_url('detail_setoran/tesdatax/') ?>" + id_master_setoran,
  //     type: "GET",
  //     dataType: "JSON",
  //     success: function(data) {
  //       setTable(data.data);
  //       countScanned(id_master_setoran);
  //     },
  //     error: function(jqXHR, errorThrown, textStatus) {
  //       alert('error getting data from server!' + textStatus);
  //     }
  //   });
  // }

  // function countScanned(id_master_setoran) {
  //   $.ajax({
  //     url: "</?= site_url('detail_setoran/invoiceCountBy2/') ?>" + id_master_setoran,
  //     type: "GET",
  //     dataType: "JSON",
  //     success: function(data) {
  //       $("#update_remark").html(data.data);
  //     },
  //     error: function(jqXHR, errorThrown, textStatus) {
  //       alert('error getting data from server!' + textStatus);
  //     }
  //   });
  // }

  function getKeterangan(kodePelanggan, kodeInvoice) {
    $.ajax({
      url: "<?= site_url('detail_setoran/get_keterangan/') ?>" + kodePelanggan + "/" + kodeInvoice,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="md_no_pelanggan"]').val(data.no_pelanggan);
        $('[name="md_kode_invoice"]').val(kodeInvoice);
        $('#md_no_pelanggan').text(data.no_pelanggan);
        $('#' + data.metode_pembayaran).prop('checked', true);
        if (data.metode_pembayaran == 'antar') {
          item('show');
        } else {
          item('hide');
          id_karyawan.val(null).trigger('change');
        }
        id_karyawan.val(data.penerima).trigger('change');
        $('[name="remark"]').val(data.remark);
        $('[name="md_keterangan"]').val(data.keterangan);
        $('#myModal').modal('show'); // show bootstrap modal
      },
      error: function(jqXHR, errorThrown, textStatus) {
        alert('error getting data from server!' + textStatus);
      }
    });
  }

  function saveKeterangan() {
    $.ajax({
      url: "<?= site_url('detail_setoran/save_keterangan') ?>",
      type: "POST",
      data: $('#formKet').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#myModal').modal('hide');
          // listScanned($("#id_master_setoran").val());
          // setCheckBox();
          notif('Berhasil mengedit data!', 'Sukses', 'success');
          tabelku.cell({
            row: row_index,
            column: 5
          }).data(data.metode_pembayaran);
          tabelku.cell({
            row: row_index,
            column: 7
          }).data(data.remark);
          tabelku.cell({
            row: row_index,
            column: 8
          }).data(data.keterangan);
          countScanned(id_master_setoran);
        } else {
          alert('Keterangan gagal di update!');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        notif('Gagal mengUpdate data!', 'Error', 'error');
      }
    });
  }

  function delete_by(kodeInvoice) {
    if (confirm('Hapus setoran "' + kodeInvoice + '" ini?')) {
      $.ajax({
        url: "<?= site_url('detail_setoran/delete_by/') ?>" + kodeInvoice,
        type: "GET",
        dataType: "JSON",
        success: function(data) {
          // var filteredData = tabelku.rows().indexes().filter( function ( value, index ) {
          //        return tabelku.row(value).data()[1] == kodeInvoice;
          //     });
          // tabelku.rows( filteredData )
          // .remove()
          // .draw();
          tabelku.row(row_index).remove().draw();
          notif('Berhasil menghapus data!', 'Sukses', 'success');
          countScanned(id_master_setoran);
          //listScanned($("#id_master_setoran").val());
        },
        error: function(jqXHR, errorThrown, textStatus) {
          alert('error getting data from server!' + textStatus);
        }
      });
    }
  }

  function deleteAllBy(id_master_setoran) {
    if (confirm('Hapus semua setoran kolektor ini?\nHati-hati Data tidak dapat dikembalikan!')) {
      $.ajax({
        url: "<?= site_url('detail_setoran/delAllBy/') ?>" + id_master_setoran,
        type: "GET",
        dataType: "JSON",
        success: function(data) {
          notif('Berhasil menghapus data!', 'Sukses', 'success');
          listScanned($("#id_master_setoran").val());
        },
        error: function(jqXHR, errorThrown, textStatus) {
          alert('error getting data from server!' + textStatus);
        }
      });
    }
  }

  function setUpper() {
    var kode = $("[name='kode_invoice']").val().toUpperCase();
    $("[name='kode_invoice']").val(kode);
  }

  function setTable(dataScan) {
    tabelku = $('#table2').DataTable({
      'data': dataScan,
      'columnDefs': [{
        'targets': 0,
        'checkboxes': true
      }],
      'order': [
        [0, 'desc']
      ]
    });
    setCheckBox();
  }

  function setCheckBox() {
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
  }

  function load_inserted(id_master_setoran, kode) {
    $.ajax({
      url: "<?= site_url('detail_setoran/get_last_inserted/') ?>" + id_master_setoran + "/" + kode,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          // play sound
          let gvoice = document.querySelector("audio#myAudio");
          gvoice.play();
          // draw datatable
          tabelku.row.add(data.data).draw(false).nodes().to$().addClass('success');
          tabelku.page('first').draw(false);
          setTimeout(function() {
            $('#dataScanned tr.success').removeClass('success');
          }, 3000);
          countScanned(id_master_setoran);
        }
      },
      error: function(jqXHR, errorThrown, textStatus) {
        alert('error getting data from server!' + textStatus);
      }
    });
  }

  function setNol(param = null, mode = null) {
    if (param == null && mode == 'transfer') {
      param = parseInt($("[name='remark']").val()) + parseInt($('#md_no_pelanggan').text());
    }
    $("[name='remark']").val(param);
  }

  function appendKet(param) {
    $("[name='md_keterangan']").val($("[name='md_keterangan']").val() + param);
  }

  function setKet(param) {
    $("[name='md_keterangan']").val(param);
  }

  function item(params = 'hide') {
    if (params == 'hide') {
      $('.hidemode').hide('slow');
    } else {
      $('.hidemode').show('slow');
    }
  }

  let wamode = false;

  function modewa(x) {
    wamode = !wamode;
    console.log(wamode);
  }
</script>

</body>

</html>