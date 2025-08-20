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
                                  <th>KODE</th>
                                  <th>Wilayah</th>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Tambah <?php echo ucwords(str_replace('_',' ',$active)); ?></h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body" id="step2">
              <div class="row">
                <form id="form" action="#" class="form-horizontal">
                    <div class="col-md-12 b-r">
                      <input type="text" name="id_wilayah" hidden>
                      <div class="form-group"><label class="col-md-2 control-label ">Kode Wilayah</label>
                          <div class="col-md-10"><input type="number" min="1" max="9" name="kode_wilayah" placeholder="Kode Wilayah" class="form-control fokus"> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-2 control-label ">Wilayah</label>
                          <div class="col-md-10"><input type="text" name="wilayah" placeholder="Wilayah" class="form-control"> <span class="help-block m-b-none"></span>
                          </div>
                      </div>
                      <div class="form-group"><label class="col-md-2 control-label">Keterangan</label>
                          <div class="col-md-10"><input type="text" name="keterangan" placeholder="Keterangan" class="form-control"> <span class="help-block m-b-none"></span>
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
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Detail <?php echo ucwords(str_replace('_',' ',$active)); ?></h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body">
              <div class="row">
                <table class="table table-striped table-hover" >
                <tr>
                    <td class="font-bold">KODE</td>
                    <td class="font-bold v1"></td>
                </tr>
                <tr>
                    <td class="font-bold">Wilayah</td>
                    <td class="font-bold v2"></td>
                </tr>
                <tr>
                    <td class="font-bold">Keterangan</td>
                    <td class="font-bold v3"></td>
                </tr>
                <tbody>

                </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
