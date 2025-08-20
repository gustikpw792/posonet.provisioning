<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/bootstrapTour/bootstrap-tour.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/jquery-qrcode-master/jquery.qrcode.min.js') ?>"></script>
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
        placeholder: "Pilih Wilayah",
        width: "100%"
    });

    var table;
    $(document).ready(function() {
        $('.btnFokus').focus(); // fokus ke field ketika tombol tambah di klik
    });
</script>

<script>
    function openModal() {
        $('#linkDevice').modal('show'); // show bootstrap modal
        // jquery('#qrcodex').qrcode("this plugin is great");
        jQuery('#qrcodex').qrcode({
            width: 150,
            height: 150,
            text: "1@i7RPhu8JWXD1J3YzGjS3NgurJO/J3oihx4rCS5eILz4m8dtUxs+Gvw715v6FMaPvYLBufMK/nsuLOg==,jPPT/bFki6JVZCHhP6AXNZCBperXr3gSJseMBN2+C34=,vGC+iU80cDQZvpYalBn+ew=="
        });
    }
    var tabel, start_time;
    // $(document).ready(function() {
    //     tabel = $('#table').DataTable({
    //         ajax: {
    //             url: "</?php echo site_url('kwitansi/files2') ?>"
    //         },
    //         order: [2, "desc"],
    //         columnDefs: [{
    //                 targets: [-1],
    //                 orderable: false
    //             },
    //             {
    //                 visible: false,
    //                 targets: [1]
    //             }
    //         ],
    //         drawCallback: function(settings) {
    //             var api = this.api();
    //             var rows = api.rows({
    //                 page: 'current'
    //             }).nodes();
    //             var last = null;

    //             api.column(1, {
    //                 page: 'current'
    //             }).data().each(function(group, i) {
    //                 if (last !== group) {
    //                     $(rows).eq(i).before(
    //                         '<tr class="group active"><td colspan="3" class="font-bold">' + group + '</td></tr>'
    //                     );

    //                     last = group;
    //                 }
    //             });
    //         },
    //     });
    // });

    $(function() {
        getSelect();
    });

    function getSelect() {
        $('[name="wilayah"]').load("<?php echo site_url('getselect/pilih_mul/wilayah/kode_wilayah/wilayah') ?>");
    }

    function waktu(mili) {
        var menit = Math.floor(mili / 60000);
        var detik = ((mili % 60000) / 1000).toFixed(0);
        res = menit + ":" + (detik < 10 ? '0' : '') + detik;
        return res;
    }

    function reload_table() {
        tabel.ajax.reload(null, false);
    }
</script>
</body>

</html>