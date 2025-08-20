<div class="wrapper wrapper-content">
    <div class="x-container">

        <div class="row">
            <div class="col-md-8">
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
                        <div class="row m-b-md">
                            <div class="col-md-3">
                                <div class="widget xnavy-bg no-padding">
                                    <div class="p-m">
                                        <h2 class="m-xs vs1 font-bold">0</h2>

                                        <small class="m-xs">
                                            Pengeluaran bulan ini
                                        </small>
                                        <!-- <small>Pengeluaran bulan ini</small> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="widget xlazur-bg no-padding">
                                    <div class="p-m">
                                        <h2 class="m-xs vs2 font-bold">0</h2>

                                        <small class="m-xs">
                                            Pengeluaran bulan sebelumnya
                                        </small>
                                        <!-- <small>Pengeluaran bulan sebelumnya</small> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="widget xyellw-bg no-padding">
                                    <div class="p-m">
                                        <h2 class="m-xs vs3 font-bold">0</h2>

                                        <small class="m-xs">
                                            Tahun ini
                                        </small>
                                        <!-- <small>Sales marketing.</small> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="widget xyellw-bg no-padding">
                                    <div class="p-m">
                                        <h2 class="m-xs vs4 font-bold">Upcoming</h2>

                                        <small class="m-xs">
                                            Tahun lalu
                                        </small>
                                        <!-- <small>Sales marketing.</small> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" id="step4">
                            <table class="table table-hover" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tgl Pengeluaran</th>
                                        <th>Nama Pengeluaran</th>
                                        <th>Jumlah</th>
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
            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Pengeluaran perbulan</h5>
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
                        <table id="tablepengeluaran" class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Pengeluaran</th>
                                </tr>
                            </thead>
                            <tbody class="dataPengeluaran">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Tambah <?php echo ucwords(str_replace('_', ' ', $active)); ?></h4>
                        <small class="font-bold"></small>
                    </div>
                    <div class="modal-body" id="step2">
                        <div class="row">
                            <form id="form" action="#" class="form-horizontal">
                                <input type="text" name="id_pengeluaran" hidden>
                                <div class="col-md-12 b-r">
                                    <div class="form-group"><label class="col-md-2 control-label ">Tanggal Pengeluaran</label>
                                        <div class="col-md-10"><input type="text" name="tgl_pengeluaran" placeholder="Tanggal Pengeluaran" class="form-control date"> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-2 control-label ">Nama Pengeluaran</label>
                                        <div class="col-md-10"><input type="text" name="nama_pengeluaran" placeholder="Nama Pengeluaran" class="form-control typeahead" data-provide="typeahead" autocomplete="off"> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-2 control-label ">Jumlah</label>
                                        <div class="col-md-10"><input type="number" min="0" step="5000" name="jumlah" placeholder="Jumlah" class="form-control"> <span class="help-block m-b-none"></span>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-2 control-label">Keterangan</label>
                                        <div class="col-md-10"><textarea name="keterangan" placeholder="Keterangan" class="form-control"></textarea>
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
                                                Nama Pengeluaran <h3><strong><span class="v1">No data</span></strong></h3>
                                            </td>
                                            <td>
                                                Tgl Pengeluaran<h3><strong><span class="v2">No data</span></strong></h3>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="font-bold">
                                                Jumlah <h3><strong><span class="v3">No data</span></strong></h3>
                                            </td>
                                            <td class="font-bold">
                                                Keterangan <h3><strong><span class="v4">No data</span></strong></h3>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-3">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>