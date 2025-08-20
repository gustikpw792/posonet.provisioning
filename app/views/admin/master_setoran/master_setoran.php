<div class="wrapper wrapper-content">
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?php echo ucwords(str_replace('_', ' ', $active)); ?></h5>
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
                        <div class="xtable-responsive" id="step4">
                            <table class="table table-hover" id="table">
                                <thead>
                                    <tr>
                                        <!-- <th>#</th> -->
                                        <th>Tgl Setoran</th>
                                        <th>Bulan Group</th>
                                        <th style="max-width: 400px;">Kolektor</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width:80%">
                <div class="modal-content animated fadeInDown">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Tambah <?php echo ucwords(str_replace('_', ' ', $active)); ?> Pelanggan</h4>
                        <small class="font-bold"></small>
                    </div>
                    <div class="modal-body" id="step2">
                        <div class="row">
                            <form id="form" action="#" class="form-horizontal">
                                <input type="text" name="id_master_setoran" hidden>
                                <div class="col-md-12">
                                    <div class="form-group"><label class="col-md-3 control-label">Tanggal Setoran</label>
                                        <div class="col-md-9"><input type="text" name="tgl_setoran" placeholder="Tanggal Setoran" class="form-control date"> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">Kolektor</label>
                                        <div class="col-md-9"><select name="id_kolektor" class="form-control fokus"></select> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label">Keterangan</label>
                                        <div class="col-md-9"><textarea name="keterangan" placeholder="Keterangan" class="form-control"></textarea>
                                            <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary step3" id="btnSave" onclick="save()">Save changes</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal inmodal" id="DetailModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width:60%">
                <!---->
                <div class="modal-content animated fadeInDown">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Detail <?php echo ucwords(str_replace('_', ' ', $active)); ?></h4>
                        <small class="font-bold"></small>
                    </div>
                    <div class="modal-body">
                        <div class="row m-b-lg m-t-lg">
                            <div class="col-md-8">
                                <table class="table small m-b-xs">
                                    <tbody>
                                        <tr>
                                            <td>
                                                Nomor Pelanggan <h3><strong><span class="v1">No data</span></strong></h3>
                                            </td>
                                            <td>
                                                Nama Pelanggan <h3><strong><span class="v2">No data</span></strong></h3>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="font-bold">
                                                Wilayah <h3><strong><span class="v3">No data</span></strong></h3>
                                            </td>
                                            <td class="font-bold">
                                                Alamat <h3><strong><span class="v4">No data</span></strong></h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold">
                                                Keterangan <h3><strong><span class="v10">No data</span></strong></h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table small m-b-xs">
                                    <tbody>
                                        <tr>
                                            <td>
                                                Status <strong><span class="v5">No data</span></strong>
                                            </td>
                                            <td>
                                                Tarif <strong><span class="v6">No data</span></strong>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="font-bold">
                                                Tgl Instalasi <h3><strong><span class="v7">No data</span></strong></h3>
                                            </td>
                                            <td class="font-bold">
                                                Telepon <h3><strong><span class="v8">No data</span></strong></h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-3">
                                <small>Lokasi Map</small>
                                <h3 class="no-margins text-danger"><span class="v9">No data</span></h3>
                                <small>Aktif Iuran</small>
                                <dt class="text-bold"><span class="v11">No data</span></dt>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Laporan -->
        <div class="modal inmodal" id="myModal_laporan" tabindex="-1" role="dialog" aria-hidden="true">
            <!-- <div class="modal-dialog" style="width:80%"> -->
            <div class="modal-dialog">
                <div class="modal-content animated flipInY">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Laporan <?php echo ucwords(str_replace('_', ' ', $active)); ?> Pelanggan</h4>
                        <small class="font-bold"></small>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="form_laporan" action="#" class="form-horizontal">
                                <!-- <div class="col-md-6 b-r"> -->
                                <div class="form-group"><label class="col-md-3 control-label ">Wilayah</label>
                                    <div class="col-md-9"><select name="lap_wilayah" class="form-control fokus"></select> <span class="help-block m-b-none"></span>
                                    </div>
                                </div>
                                <!-- </div> -->

                                <!-- <div class="col-md-6 b-r"> -->
                                <div class="form-group"><label class="col-md-3 control-label">Tanggal Pasang</label>
                                    <div class="col-md-9"><input type="text" name="lap_tgl_pasang" placeholder="Tanggal Pasang/Instalasi" class="form-control date"> <span class="help-block m-b-none"></span>
                                    </div>
                                </div>
                                <!-- <div class="form-group"><label class="col-md-3 control-label">Tarif Berlangganan</label>
                          <div class="col-md-9"><select name="lap_tarif" class="form-control"></select> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-3 control-label">Status</label>
                          <div class="col-md-9"><select name="lap_status" class="form-control"></select> <span class="help-block m-b-none"></span>
                          </div>
                      </div> -->
                                <div class="form-group"><label class="col-md-3 control-label">Group All</label>
                                    <div class="col-md-9">
                                        <select name="lap_group_all" class="form-control">
                                            <option value=""></option>
                                            <option value="group_all"></option>
                                        </select>
                                        <span class="help-block m-b-none"></span>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnGetLaporan" onclick="get_laporan()">Lihat Laporan</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>