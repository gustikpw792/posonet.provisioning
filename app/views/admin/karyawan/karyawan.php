<div class="wrapper wrapper-content">
            <div class="container">
            
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><?php echo ucwords(str_replace('_',' ',$active)); ?></h5>
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
                                    <th>#</th>
                                    <th>Kode Karyawan</th>
                                    <th>Nama Lengkap</th>
                                    <th>Telp/HP</th>
                                    <th>Status</th>
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
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Tambah <?php echo ucwords(str_replace('_',' ',$active)); ?></h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body" id="step2">
              <div class="row">
                <form id="form" action="#" class="form-horizontal">
                    <input type="text" name="id_karyawan" hidden>
                    <div class="col-md-6 b-r">
                      <div class="form-group"><label class="col-md-3 control-label ">Kode Karyawan</label>
                          <div class="col-md-9"><input type="text" name="kode_karyawan" placeholder="Kode Karyawan" class="form-control fokus" value="PTV"> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-3 control-label">Nama Karyawan</label>
                          <div class="col-md-9"><input type="text" name="nama_karyawan" placeholder="Nama Karyawan" class="form-control"> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-3 control-label">Status</label>
                          <div class="col-md-9"><select name="status" class="form-control"></select> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-3 control-label ">Tanggal Masuk</label>
                          <div class="col-md-9"><input type="text" name="tgl_masuk" placeholder="Tanggal Masuk" class="form-control date"> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-6 b-r">
                      <div class="form-group"><label class="col-md-3 control-label">Tanggal Berakhir</label>
                          <div class="col-md-9"><input type="text" name="tgl_berakhir" placeholder="Tanggal Berakhir" class="form-control date"> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-3 control-label">Alamat</label>
                          <div class="col-md-9"><textarea name="alamat" placeholder="Alamat" class="form-control"></textarea> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-3 control-label">Telepon</label>
                          <div class="col-md-9"><input type="text" name="telp" placeholder="Telepon" class="form-control"> <span class="help-block m-b-none"></span>
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
    <div class="modal-dialog" style="width:60%"><!---->
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Detail <?php echo ucwords(str_replace('_',' ',$active)); ?></h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body">
              <div class="row m-b-lg m-t-lg">
                  <div class="col-md-8">
                      <table class="table small m-b-xs">
                          <tbody>
                          <tr>
                              <td>
                                  Kode Karyawan <h3><strong><span class="v1">No data</span></strong></h3>
                              </td>
                              <td>
                                  Nama Karyawan <h3><strong><span class="v2">No data</span></strong></h3>
                              </td>

                          </tr>
                          <tr>
                              <td class="font-bold">
                                  <!-- Status <h3><strong><span class="v3">No data</span></strong></h3> -->
                              </td>
                              <td class="font-bold">
                                  Alamat <h3><strong><span class="v4">No data</span></strong></h3>
                              </td>
                          </tr>
                          <!-- <tr>
                              <td class="font-bold">
                                  Tgl Masuk <h3><strong><span class="v10">No data</span></strong></h3>
                              </td>
                          </tr> -->
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
                                  <!-- Tarif <strong><span class="v6">No data</span></strong> -->
                              </td>

                          </tr>
                          <tr>
                              <td class="font-bold">
                                  Tgl masuk <h3><strong><span class="v7">No data</span></strong></h3>
                              </td>
                              <td class="font-bold">
                                  Telepon <h3><strong><span class="v8">No data</span></strong></h3>
                              </td>
                          </tr>
                          </tbody>
                      </table>
                  </div>
                  <div class="col-md-3">
                    <!-- <small>Lokasi Map</small>
                    <h3 class="no-margins text-danger"><span class="v9">No data</span></h3>
                    <small>Aktif Iuran</small>
                    <dt class="text-bold"><span class="v11">No data</span></dt> -->
                  </div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>