<div class="wrapper wrapper-content">
    <div class="">

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
                        <div class="tabs-container">

                            <div class="tabs-left">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-1"><span class="fa fa-address-book-o"></span> Pelanggan</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-2"><span class="fa fa-line-chart"></span> Setoran</a></li>
                                </ul>
                                <div class="tab-content ">
                                    <div id="tab-1" class="tab-pane active">
                                        <div class="panel-body">
                                            <p>Silahkan tentukan WILAYAH yang akan di export dalam bentuk EXCEL!</p>
                                            <div class="col-md-6">
                                                <form id="formLap" action="#" class="form-horizontal m-t-lg">
                                                    <div class="form-group"><label class="col-md-4 control-label ">Wilayah</label>
                                                        <div class="col-md-8"><select name="id_wilayah" class="form-control id_wilayah"></select> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <!-- <span class="fa fa-file-excel-o fa-2x"></span> | Export to Office Excel -->
                                                    <div class="btn-group pull-right">
                                                        <a href="<?= site_url('laporan/exportplgnAll') ?>" class="btn btn-sm btn-success">Semua Wilayah</a>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="export_lap_pelanggan('xls')">By Wilayah .Xls (2007)</a>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="export_lap_pelanggan('xlsx')">By Wilayah .Xlsx</a>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-md-6"></div>
                                        </div>
                                    </div>
                                    <div id="tab-2" class="tab-pane">
                                        <div class="panel-body">
                                            <p>Silahkan tentukan WILAYAH yang akan di export dalam bentuk EXCEL!</p>
                                            <div class="col-md-6">
                                                <form id="formLap" action="#" class="form-horizontal m-t-lg">
                                                    <div class="form-group"><label class="col-md-4 control-label ">Tahun Laporan</label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="tahun_laporan">
                                                                <?php
                                                                $selected = (date('Y') == date('Y')) ? 'selected' : '';
                                                                for ($i = 2020; $i <= date('Y'); $i++) {
                                                                    echo "<option value=\"$i\" $selected>$i</option>";
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-4 control-label ">Wilayah</label>
                                                        <div class="col-md-8"><select name="id_wilayah" id="id_wilayah" class="form-control id_wilayah"></select> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <span class="fa fa-file-excel-o fa-2x"></span> | Export to Office Excel
                                                    <div class="btn-group pull-right">
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="export_lap_penagihan('xls')">By Wilayah .Xls (2007)</a>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="export_lap_penagihan('xlsx')">By Wilayah .Xlsx</a>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-md-6"></div>
                                        </div>
                                    </div>
                                    <div id="tab-3" class="tab-pane">
                                        <div class="panel-body">
                                            <strong>Backup Database</strong>

                                            <p>Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects
                                                and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>

                                            <p>I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite
                                                sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>