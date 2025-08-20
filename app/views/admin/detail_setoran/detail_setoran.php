<div class="wrapper wrapper-content">
    <div class="container">

        <div class="row">

            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title panel-primary">
                        <h5>Pindai Kwitansi <small><?php echo ucwords(str_replace('_', ' ', $active)); ?></small></h5>
                        <div class="ibox-tools list-cam">

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="number" name="id_master_setoran" id="id_master_setoran" value="<?= $detail_setoran->id_master_setoran ?>" hidden>
                                <input type="number" name="id_kolektor" id="id_kolektor" value="<?= $detail_setoran->id_kolektor ?>" hidden>
                                <!-- <input type="number" name="id_transfer" id="id_transfer" value="</?= $transfer->id_karyawan ?>" hidden> -->
                                <div class="col-md-4">
                                    <video class="vidscan img-rounded" id="preview"></video>
                                </div>
                                <div class="col-md-8">
                                    <span class="help-block m-b-none text-warning font-italic">Masukan No. Invoice jika scanner tidak bekerja!</span>
                                    <div class="input-group"><input type="search" name="kode_invoice" placeholder="No. Invoice" class="form-control" onkeyup="setUpper()" onsearch="getDetail('kode','inputmanual')">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary" onclick="getDetail('kode','inputmanual')"><span class="fa fa-search"></span> Go!</button>
                                        </span>
                                    </div>
                                    <br>
                                    <p>Enable Whatsapp notification?</p>
                                    <div class="switch">
                                        <div class="onoffswitch">
                                            <input type="checkbox" class="onoffswitch-checkbox" id="idmodewa" onclick="modewa(this)">
                                            <label class="onoffswitch-label" for="idmodewa">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title panel-primary">
                        <h5>Keterangan</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-down"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="p-w-md">
                                    <ul class="list-group clear-list">
                                        <li class="list-group-item fist-item">
                                            <span class="pull-right">
                                                <h3><strong class='text-success'><?= $detail_setoran->kolektor ?></strong></h3>
                                            </span>
                                            <strong>Kolektor</strong>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="pull-right">
                                                <strong class='text-success'><?= date('d M Y', strtotime($detail_setoran->tgl_setoran)) ?></strong>
                                            </span>
                                            <strong>Tanggal Setoran</strong>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="pull-right" id="update_remark" style="font-size: 12pt; font-weight: bold;">
                                                <?= number_format($detail_setoran->total_setoran_remark, 0, ",", ".") ?>
                                            </span>
                                            <strong>Jumlah Disetor Rp</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
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