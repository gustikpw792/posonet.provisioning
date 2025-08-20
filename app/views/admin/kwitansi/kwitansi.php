<div class="wrapper wrapper-content">
    <div class="container">

        <div class="row">
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
                                    <div class="form-group" id="step2"><label class="col-md-4 control-label">Pilih Wilayah</label>
                                        <div class="col-md-8"><select name="wilayah" class="form-control"></select> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-4 control-label">Urutkan</label>
                                        <div class="col-md-8">
                                            <select name="urutkan" class="form-control">
                                                <option value="KODE_PELANGGAN" selected>Kode Pelanggan</option>
                                                <option value="ASC">A-Z (sort)</option>
                                                <option value="DESC">Z-A (sort)</option>
                                            </select>
                                            <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" id="step3"><label class="col-md-4 control-label ">Sandi</label>
                                        <div class="col-md-8"><input type="password" name="sandi" placeholder="Sandi Keamanan Kwitansi" class="form-control"> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <span class="pull-right">
                            <a href="#" class="btn btn-primary" onclick="regInvoice()" id="step4"><i class="fa fa-hdd-o"></i> Register Invoice</a>
                        </span>
                        Lama registrasi tergantung banyaknya jumlah pelanggan! Bisa >30 detik <br><br>
                    </div>
                </div>
            </div>


            <!-- panel pdf -->
            <div class="col-md-7">
                <div class="ibox float-e-margins step5" id="ibox2">
                    <div class="ibox-title panel-success">
                        <h5>Generated Kwitansi</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
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
                            <table id="table" class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Registered Kwitansi</th>
                                        <th>Bulan Tagihan</th>
                                        <th>Bulan Tagihan</th>
                                        <th style="width: 180px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="dataFiles">
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="ibox-footer">
                        <span class="pull-right">
                            <!-- <a href="#" class="btn btn-primary" onclick="regInvoice()"  id="step4"><i class="fa fa-hdd-o"></i> Register Invoice</a> -->
                        </span>
                        Daftar Invoice yang telah ter-Registrasi ke sistem! <br><br>
                    </div>
                </div>

            </div>

        </div>


        <div class="row">
            <div class="col-md-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title panel-primary">
                        <h5>Warna Kertas <?php echo ucwords(str_replace('_', ' ', $active)); ?> (Opsional)</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table id="tablex" class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bulan</th>
                                    <th>Warna Kertas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Januari</td>
                                    <td class="info">Biru</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Februari</td>
                                    <td class="warning">Kuning</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Maret</td>
                                    <td class="success">Hijau</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>April</td>
                                    <td class="danger">Merah</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Mei</td>
                                    <td class="info">Biru</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Juni</td>
                                    <td class="warning">Kuning</td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Juli</td>
                                    <td class="success">Hijau</td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Agustus</td>
                                    <td class="danger">Merah</td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>September</td>
                                    <td class="info">Biru</td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>Oktober</td>
                                    <td class="warning">Kuning</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>November</td>
                                    <td class="success">Hijau</td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>Desember</td>
                                    <td class="danger">Merah</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="ibox-footer">
                        <span class="pull-right">
                        </span>
                        <!-- Daftar Invoice yang telah ter-Registrasi ke sistem! <br><br> -->
                    </div>
                </div>
            </div>
        </div>