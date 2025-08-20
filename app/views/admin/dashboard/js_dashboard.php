<!-- Flot -->
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.js') ?>"></script>
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.tooltip.min.js') ?>"></script>
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.spline.js') ?>"></script>
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.pie.js') ?>"></script>
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.symbol.js') ?>"></script>
<script src="<?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.time.js') ?>"></script>
<!-- Sparkline -->
<script src="<?= base_url('assets/inspinia271/js/plugins/sparkline/jquery.sparkline.min.js') ?>"></script>
<!-- Chart.js -->
<script src="<?= base_url('assets/inspinia271/js/plugins/chartJs/Chart.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/datapicker/bootstrap-datepicker.js') ?>"></script>

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

    $(document).ready(function() {
        getDonat();
        getLineChart();
        getSummary();

        $(".cdet15, .cdet16, .cdet1, .cdet7").click(function() {
            $(".det15,.det16,.det8,.det1,.det7").toggle('slow');
        });
    });

    function donat(isi) {
        var myoption = {
            responsive: true,
            animation: {
                animateScale: true
            }
        };

        var ctx2 = document.getElementById("doughnutChart").getContext("2d");
        new Chart(ctx2, {
            type: 'doughnut',
            data: isi,
            options: myoption
        });
    }

    function mylineChart(lineData) {
        lineOptions = {
            responsive: true,
            animation: {
                animateScale: true
            },
            scales: {
                yAxes: [{
                    ticks: {
                        // Include a dollar sign in the ticks
                        callback: function(value, index, values) {
                            return 'Rp ' + value;
                        }
                    }
                }]
            }
        };

        var ctx = document.getElementById("lineChart").getContext("2d");
        new Chart(ctx, {
            type: 'line',
            data: lineData,
            options: lineOptions
        });
    }

    function getSummary() {
        bulan = $("#bulan_penagihan").val();
        $('#ibox2').children('.ibox-content').toggleClass('sk-loading');
        $.ajax({
            url: "<?= site_url('Api_search/dashboard_summary') ?>",
            type: "POST",
            data: {
                bulan_penagihan: bulan
            },
            dataType: "JSON",
            success: function(data) {
                // Persentase Pencapaian
                $(".det15").html('<span title="Rp ' + data.pencapaian.target_nonaktif + ' Jumlah iuran nonaktif">' + data.pencapaian.target + '</span>');
                $(".det16").html('<span title="Rp ' + data.pencapaian.tercapai + ' jika setoran normal tanpa remark">' + data.pencapaian.tercapai_remark + '</span>');
                //   $(".det18").html(data.pencapaian.tercapai_remark + ' <sup>*remark</sup>');
                $(".det17").html(data.pencapaian.tercapai_percent);
                // $(".det18").html(data.pencapaian.margin);
                $('#ibox2').children('.ibox-content').toggleClass('sk-loading');
            }
        });
    }

    function getLineChart() {
        bulan = $("#bulan_penagihan").val();
        $.ajax({
            url: "<?= site_url('Api_search/dashboard_line') ?>",
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                // Summary
                $(".det8").text(data.chart_des.total_setoran.total_remark);
                $(".det9").text(data.chart_des.total_setoran.bulan);
                $(".det10").text(data.chart_des.max_setoran.kolektor);
                $(".det11xx").text(data.chart_des.max_setoran.total);
                $(".det12").text(data.chart_des.update_on);
                $(".det13").text(data.chart_des.last_month_summary.bulan);
                $(".det14xx").text(data.chart_des.last_month_summary.total_remark);

                // Chart
                mylineChart(data.line_chart_data);
            }
        });
    }

    function getDonat() {
        $.ajax({
            url: "<?= site_url('Api_search/dashboard_donat') ?>",
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                // Pelanggan
                $(".det1").text(data.pelanggan.total_pelanggan);
                $(".det2").text(data.pelanggan.pelanggan_aktif);
                $(".det3").text(data.pelanggan.pelanggan_putus_sementara);
                $(".det4").text(data.pelanggan.pelanggan_non_aktif);
                det7 = (data.pelanggan.pelanggan_non_aktif * 1);
                $(".det7").text(det7);
                // Wilayah
                $(".det5").text(data.total_wilayah);
                $(".det6").html(data.wilayah);
                // Chart
                donat(data.doughnutchart_data);
            }
        });
    }
</script>
</body>

</html>