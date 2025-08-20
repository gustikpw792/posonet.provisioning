                </div> <!-- wrapper wrapper-content-->
                </div> <!-- container -->
                <div class="footer">
                    <div class="pull-right">
                        <strong><?= ucwords(str_replace('_', ' ', $active)) ?></strong>.
                    </div>
                    <div>
                        <strong>Copyright</strong> <span class="cdet15"><?= $profilP->nama_perusahaan; ?> &copy; 2020-<?= date('Y') ?> </span>
                    </div>
                </div>

                </div> <!-- page-wrapper -->
                </div> <!-- wrapper -->

                <!-- Mainly scripts -->
                <script src="<?= base_url('assets/inspinia271/js/jquery-3.1.1.min.js') ?>"></script>
                <script src="<?= base_url('assets/inspinia271/js/bootstrap.min.js') ?>"></script>
                <script src="<?= base_url('assets/inspinia271/js/plugins/metisMenu/jquery.metisMenu.js') ?>"></script>
                <script src="<?= base_url('assets/inspinia271/js/plugins/slimscroll/jquery.slimscroll.min.js') ?>"></script>

                <!-- Custom and plugin javascript -->
                <script src="<?= base_url('assets/inspinia271/js/inspinia.js') ?>"></script>
                <script src="<?= base_url('assets/inspinia271/js/plugins/pace/pace.min.js') ?>"></script>

                <!-- Flot -->
                <!-- <script src="</?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.js') ?>"></script>
                <script src="</?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.tooltip.min.js') ?>"></script>
                <script src="</?= base_url('assets/inspinia271/js/plugins/flot/jquery.flot.resize.js') ?>"></script> -->

                <!-- ChartJS-->
                <script src="<?= base_url('assets/inspinia271/js/plugins/chartJs/Chart.min.js') ?>"></script>

                <!-- Peity -->
                <!-- <script src="</?= base_url('assets/inspinia271/js/plugins/peity/jquery.peity.min.js') ?>"></script> -->
                <!-- Peity demo -->
                <!-- <script src="</?= base_url('assets/inspinia271/js/demo/peity-demo.js') ?>"></script> -->

                <!-- Toastr -->
                <script src="<?php echo base_url('assets/inspinia271/js/plugins/toastr/toastr.min.js') ?>"></script>
                <!-- Jquery Validate -->
                <script src="<?php echo base_url('assets/inspinia271/js/plugins/validate/jquery.validate.min.js') ?>"></script>
                <!-- Ladda -->
                <script src="<?php echo base_url('assets/inspinia271/js/plugins/ladda/spin.min.js') ?>"></script>
                <script src="<?php echo base_url('assets/inspinia271/js/plugins/ladda/ladda.min.js') ?>"></script>
                <script src="<?php echo base_url('assets/inspinia271/js/plugins/ladda/ladda.jquery.min.js') ?>"></script>
                
                

                <script>
                    var jdlPesan, isiPesan, msgType;

                    function notif(isiPesan, jdlPesan, msgType) {
                        toastr.options = {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                            // positionClass: 'toast-bottom-right',
                            showEasing: 'swing',
                            hideEasing: 'linear',
                            showMethod: 'show',
                            hideMethod: 'hide',
                            timeOut: 4000,
                            extendedTimeOut: 1000
                        };
                        if (msgType == 'success') {
                            toastr.success(isiPesan, jdlPesan)
                        } else if (msgType == 'info') {
                            toastr.info(isiPesan, jdlPesan)
                        } else if (msgType == 'warning') {
                            toastr.warning(isiPesan, jdlPesan)
                        } else if (msgType == 'error') {
                            toastr.error(isiPesan, jdlPesan)
                        }
                    }
                </script>