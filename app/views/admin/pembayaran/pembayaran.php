<div class="wrapper wrapper-content">
    <div class="container">

        <div class="row">

            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title panel-primary">
                        <h5>Pencarian Data <small><?php echo ucwords(str_replace('_', ' ', $active)); ?></small></h5>
                        <div class="ibox-tools">

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <form role="form">
                                    <label>Nama atau No Internet</label>
                                    <div class="input-group">
                                        <input type="text" id="cari" class="form-control" placeholder="Nama atau No Internet">
                                        <span class="input-group-btn">
                                            <button type="button" class="ladda-button btn btn-primary" data-style="zoom-in" onclick="cariz()">
                                            Cari
                                            </button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- <div class="row"> -->
                            <br><br>
                            <table id="resultcari" class="table table-hover " style="display:none">
                                <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Wilayah</th>
                                    <!-- <th>Paket</th> -->
                                    <!-- <th>Tarif</th> -->
                                    <!-- <th>Expired</th> -->
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="tbhasil">
                                
                                </tbody>
                            </table>
                        <!-- </div> -->

                        <!-- <button class="ladda-button btn btn-warning" data-style="zoom-in">Submit</button> -->
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title panel-primary">
                        <h5>Detail Tagihan</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-down"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="display: none;" id="panelDetail">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="p-w-md" id="resDetailInvoice">
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal search result -->
        <div class="modal inmodal" id="resModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-laptop modal-icon"></i>
                        <h4 class="modal-title">Modal title</h4>
                        <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="btn-group">
                                        <button class="btn btn-white  btn-large-dim" type="button">1</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">2</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">3</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="btn-group">
                                        <button class="btn btn-white  btn-large-dim" type="button">4</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">5</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">6</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="btn-group">
                                        <button class="btn btn-white  btn-large-dim" type="button">7</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">8</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">9</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="btn-group">
                                        <button class="btn btn-white  btn-large-dim" type="button">000</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">00</button>
                                        <button class="btn btn-white  btn-large-dim" type="button">0</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- panel pdf -->
        <div class="row">

            <div class="col-md-12">
                <div class="ibox float-e-margins step5" id="ibox2">
                    <div class="ibox-title panel-success">
                        <h5>Scanned Kwitansi</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="table-responsive">
                            <form id="formScanned" action="#" method="post">
                                <table class="table table-hover table-condensed" id="table2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice</th>
                                            <th>Kode</th>
                                            <th>Nama Lengkap</th>
                                            <th>Bulan</th>
                                            <th style="max-width:140px">Penerima</th>
                                            <th>Tarif</th>
                                            <th>Remark</th>
                                            <th>Keterangan</th>
                                            <th class="text-center" style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataScanned">

                                    </tbody>
                                </table>
                            </form>
                        </div>

                    </div>
                    <div class="ibox-footer">
                        Daftar Invoice yang telah ter-Registrasi ke sistem! <br><br>
                    </div>
                </div>

            </div>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Informasi Tambahan</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="formKet" action="#" class="form-horizontal">
                                <input type="text" name="md_no_pelanggan" hidden>
                                <input type="text" name="md_kode_invoice" hidden>
                                <input type="number" name="id_karyawan_kolektor" id="id_karyawan_kolektor" value="<?= $detail_setoran->id_karyawan ?>" hidden>
                                <!-- <input type="number" name="id_karyawan_transfer" id="id_karyawan_transfer" value="</?= $transfer->id_karyawan ?>" hidden> -->
                                <div class="col-md-12">
                                    <div class="form-group"><label class="col-md-2 control-label ">Kode Pelanggan</label>
                                        <div class="col-md-10"> <span class="text-primary" id="md_no_pelanggan" style="font-size: 25pt; font-weight: bold;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-2 control-label ">Metode Pembayaran</label>
                                        <div class="col-md-10">
                                            <label class="checkbox-inline"> <input type="radio" value="transfer" id="transfer" name="metode_pembayaran" onchange="item('hide')"> Transfer </label>
                                            <label class="checkbox-inline"> <input type="radio" checked="" value="kolektor" id="kolektor" name="metode_pembayaran" onchange="item('hide')"> Kolektor </label>
                                            <label class="checkbox-inline"> <input type="radio" value="antar" id="antar" name="metode_pembayaran" onchange="item('show')"> Antar Langsung</label>
                                        </div>
                                    </div>
                                    <div class="form-group hidemode" style="display:none"><label class="col-md-2 control-label ">Pilih Penerima</label>
                                        <div class="col-md-10">
                                            <select name="id_karyawan" class="form-control"></select> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-2 control-label ">Remark Tarif</label>
                                        <div class="col-md-10">
                                            <input type="number" name="remark" step="5000" class="form-control input-lg m-b-sm" placeholder="Remark tarif setoran">
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="setNol(0)">0 (nol)</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="setNol(null,'transfer')">Transfer + No Pel</a><br>
                                            <p></p>
                                            <?php foreach ($paket as $p) {
                                                if (!$p->tarif == 0) {
                                                    echo "<a href=\"javascript:void(0)\" class=\"label text-danger text-bold\" onclick=\"setNol($p->tarif)\">" . number_format($p->tarif, 0, ',', '.') . "</a> ";
                                                }
                                            } ?>
                                            <span class="text-primary" id="md_kd_pelanggan"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-2 control-label">Keterangan</label>
                                        <div class="col-md-10">
                                            <textarea name="md_keterangan" placeholder="Keterangan" class="typeahead_1 form-control"></textarea>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="setKet('')">(Kosongkan keterangan)</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('NON AKTIF ')">NON AKTIF</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('LUNAS s/d ')">LUNAS s/d</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('<?= date('Y') - 1 ?>')"><?= date('Y') - 1 ?></a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('<?= date('Y') ?>')"><?= date('Y') ?></a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('<?= date('Y') + 1 ?>')"><?= date('Y') + 1 ?></a>
                                            <p></p>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('JANUARI ')">JANUARI</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('FEBRUARI ')">FEBRUARI</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('MARET ')">MARET</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('APRIL ')">APRIL</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('MEI ')">MEI</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('JUNI ')">JUNI</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('JULI ')">JULI</a>
                                            <p></p>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('AGUSTUS ')">AGUSTUS</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('SEPTEMBER ')">SEPTEMBER</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('OKTOBER ')">OKTOBER</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('NOVEMBER ')">NOVEMBER</a>
                                            <a href="javascript:void(0)" class="label text-danger text-bold" onclick="appendKet('DESEMBER ')">DESEMBER</a>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnSave" onclick="saveKeterangan()">Save changes</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>