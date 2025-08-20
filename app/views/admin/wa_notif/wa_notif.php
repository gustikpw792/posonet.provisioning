<div class="wrapper wrapper-content">
    <div class="container">

        <div class="row">
            <!-- panel pdf -->
            <div class="col-md-7">
                <div class="ibox float-e-margins step5" id="ibox2">
                    <div class="ibox-title panel-success">
                        <h5>List Notification</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-danger btn-xs" onclick="openModal()"><i class="fa fa-link"></i> Hubungkan Perangkat</button>
                            <!-- <button type="button" class="btn btn-success btn-xs" onclick="javascript:void(0)"><i class="fa fa-link"></i> Perangkat Terhubung</button> -->
                            <button type="button" class="btn btn-default btn-xs" onclick="javascript:void(0)"><i class="fa fa-send"></i> Send Blast </button>
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="sk-spinner sk-spinner-wave">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>
                        <div class="row">
                            <blockquote>
                                <p>Pelanggan Yth,<br> Tagihan POSONET Anda sebagai berikut : <br>
                                    Nomor Pelanggan: <b>209</b> <br>
                                    Nama Lengkap: <b>LOREM IPSUM DOLOR</b><br>
                                    Bulan Tagihan: Maret 2022<br>
                                    Masa Aktif: 20 Maret 2022 s/d 20 April 2022 <br>
                                    Total Tagihan: <b>Rp 200.209,-</b> <br>
                                    <br>
                                    Batas akhir pembayaran setiap tanggal 20 bulan berjalan.<br><br>
                                    Pembayaran dapat dilakukan melalui ATM, Mobile Banking, Internet Banking dan SMS Banking.<br><br>
                                    Untuk pembayaran melewati tgl 20, silahkan konfirmasi dengan menyertakan bukti pembayaran.<br>
                                    <b>*Abaikan informasi ini jika sdh melakukan pembayaran.</b> <br>
                                    Terima kasih
                                </p>
                                <small><strong>Admin</strong> <cite title="" data-original-title="">POSO NET</cite></small>
                            </blockquote>
                            <!-- <table id="table" class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Mode</th>
                                        <th>Pelanggan</th>
                                        <th>Total</th>
                                        <th style="width: 180px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="dataFiles">
                                    <tr>
                                        <td>Single</td>
                                        <td>102 | Rahtut Aza</td>
                                        <td>Rp 200.102</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Batch</td>
                                        <td>302 | Rahtut Aza</td>
                                        <td>Rp 200.302</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table> -->

                        </div>
                    </div>
                    <div class="ibox-footer">
                        <span class="pull-right">
                            <!-- <a href="#" class="btn btn-primary" onclick="regInvoice()"  id="step4"><i class="fa fa-hdd-o"></i> Register Invoice</a> -->
                        </span>
                        <br><br>
                    </div>
                </div>

            </div>

            <div class="col-md-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title panel-primary">
                        <h5><?php echo ucwords(str_replace('_', ' ', $active)); ?></h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-default btn-xs" onclick="deleteChache()"><i class="fa fa-recycle"></i> Hapus Tempfile </button>
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form id="formKwitansi" action="#" class="form-horizontal">
                                <div class="col-md-12">
                                    <div class="form-group" id="step1"><label class="col-md-4 control-label ">Bulan Penagihan</label>
                                        <div class="col-md-8"><input type="text" name="bulan_penagihan" placeholder="Pilih Bulan Penagihan" class="form-control date" readonly> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <span class="pull-right">
                            <a href="#" class="btn btn-primary" id="step4"><i class="fa fa-hdd-o"></i> Send Blast</a>
                        </span>
                        <br><br>
                    </div>
                </div>
            </div>




        </div>




        <!-- Detail Modal -->
        <div class="modal inmodal" id="linkDevice" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="">
                <!---->
                <div class="modal-content animated fadeInDown">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h5 class="modal-title">Hubungkan Perangkat</h5>
                        <small class="font-bold">Scan melalui aplikasi WhatsApp di handphone</small>
                    </div>
                    <div class="modal-body text-center">
                        <div class="img-fluid" id="qrcodex"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>