<div class="wrapper wrapper-content">
    <div class="container">


        <div class="row"><!-- START row 1 -->
            <!-- UNCFG LOS EXPIRED -->
            <div id="lompatAtas" class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <h5></?php echo ucwords(str_replace('_', ' ', $active)); ?></h5> -->
                        <h5>TOOLS</h5>

                        <div id="iboxToggle" class="ibox-tools">

                            <button class="btn btn-xs" onclick="showUncfg()"><i class="fa fa-plus"></i> Unconfig</button>
                            <?php if ($this->session->level == 'administrator' || $this->session->level == 'kolektor'): ?>
                                <button class="btn btn-xs" id="#btnExpiredContent" onclick="toggleExpired()"><i class="fa fa-warning"></i> Expired</button>
                                <button class="btn btn-xs" onclick="show_raw_content('log','')"><i class="fa fa-history"></i> Log</button>
                            <?php endif; ?>

                            <!-- <button class="btn btn-xs" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-history"></i> Recent Request</button> -->
                            <button class="btn btn-xs btn-primary v_online" style="display:none">online -</button>
                            <a href="#divoffline" class="btn btn-xs v_offline" style="display:none">offline -</a>
                            <button id="btnLos" class="btn btn-xs btn-danger" style="display:none"><i class="fa fa-unlink"></i> <span class="v_los"> LOS -</span></button>
                            <button id="btnNoInet" class="btn btn-xs btn-warning btn_no_internet" onclick="show_raw_content('no-internet','')" style="display:none"><i class="fa fa-unlink"></i> <span class="v_no_internet"> NO INET</span></button>
                            <button class="btn btn-xs btn-info v_ont" style="display:none">total -</button>

                            <div class="btn-group">
                                <button type="button" class="btn btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-server"></i> OLT Info<span class="fa fa-info"> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('card','')">Show Card</a></li>
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('vlan-summary','')">Show VLAN Summary</a></li>
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('gpon-profile-vlan','')">Show Profile VLAN</a></li>
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('gpon-profile-tcont','')">Show Profile Tcont</a></li>
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('gpon-profile-traffic','')">Show Profile Traffic</a></li>
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('onu-type','')">Show ONU Type</a></li>
                                    <li><a href="javascript:void(0)" onclick="show_raw_content('ip-route','')">Show IP Route</a></li>

                                    <?php if ($this->session->level == 'administrator' || $this->session->level == 'teknisi'): ?>
                                        <li><a href="javascript:void(0)" onclick="show_raw_content('interfaces','')">Show Interfaces</a></li>
                                    <?php endif; ?>

                                    <!-- <li><a href="javascript:void(0)" onclick="show_raw_content('detail-info','')">Backup Config</a></li> -->
                                </ul>
                            </div>

                            <?php if ($this->session->level == 'administrator' || $this->session->level == 'teknisi'): ?>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Manual<span class="fa fa-info"> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" onclick="close_all_remote_web()"><i class="fa fa-close"></i> Close all remote web</a></li>
                                        <li><a href="javascript:void(0)" onclick="update_onurx()"><i class="fa fa-magic"></i> Update All OnuRx dBm</a></li>
                                        <li><a href="javascript:void(0)" onclick="if(confirm('Konversi shortlink google maps ke titik koordinat?\nMembutuhkan waktu lebih dari 2 menit.\nTergantung jumlah pelanggan')) { updateCoordinate(); }"><i class="fa fa-map"></i> Convert ShortMap to Coordinate</a></li>
                                        <!-- <li><a href="javascript:void(0)" onclick="unsaved_onu()">Detect Unsaved ONU</a></li>
                                    <li><a href="javascript:void(0)" onclick="reconfig()">Proses Pindah Port / Reconfig</a></li>
                                    <li><a href="javascript:void(0)" onclick="onustate()">ONU State</a></li> -->

                                    </ul>
                                </div>

                            <?php endif; ?>

                            <button id="toggleIntervalBtn" class="btn btn-xs btn-default" title="Refresh data dari OLT setiap 15 Menit">Auto Refresh: OFF</button>

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                        </div>
                    </div>

                    <div class="ibox-content">
                        <div class="iface-los">
                            <!-- LOS -->
                        </div>

                        <div class="xtable-responsive" id="unconfig" style="display:none">
                            <table class="table table-condensed table-hover" id="tb-unconfig">
                                <thead class="bg-info">
                                    <tr>
                                        <th class="uncfg">Unconfig found(s)</th>
                                        <th>Interface</th>
                                        <th>Model</th>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>OnuType</th>
                                        <th>Paket</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div id="lompatRegis">
                            <!-- Start registerForm -->
                            <div id="divRegisterForm" style="display:none">
                                <div class="">
                                    <h2>REGISTRATION FORM</h2><br>
                                </div>
                                <form id="form" action="#" class="form-horizontal" autocomplete="off">
                                    <input type="text" name="id_pelanggan" hidden>
                                    <div class="row">
                                        <div class="col-md-6 b-r">
                                            <div class="form-group">
                                                <label class="col-md-3 control-label text-danger">PON & OnuType</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="text" name="interface" placeholder="Interface OLT" class="form-control" readonly>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select name="onutype" class="form-control">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label text-danger">Mode & VLAN</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-6" style="display:none">
                                                            <select name="service_mode" class="form-control" readonly>
                                                                <option value="pppoe">PPPoE</option>
                                                            </select> <span class="help-block m-b-none"></span>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <select name="cvlan" id="cvlan" class="form-control" onchange="setVlanProfile()">

                                                            </select> <span class="help-block m-b-none"></span>
                                                        </div>
                                                        <input type="text" id="vlan_profile" name="vlan_profile" hidden>
                                                    </div>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label text-danger">Wilayah & ID Pelanggan</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <select name="id_wilayah" id="id_wilayah" class="form-control fokus"></select> <span class="help-block m-b-none"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="no_pelanggan" class="form-control" xreadonly placeholder="ID Pelanggan"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group"><label class="col-md-3 control-label ">Wilayah</label>
                                                <div class="col-md-9"><select name="id_wilayah" id="id_wilayah" class="form-control fokus"></select> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-md-3 control-label ">No Pelanggan</label>
                                                <div class="col-md-9"><input type="text" name="no_pelanggan" class="form-control" xreadonly> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div> -->
                                            <div class="form-group"><label class="col-md-3 control-label">Nama Pelanggan</label>
                                                <div class="col-md-9"><input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" class="form-control"> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>

                                            <div class="form-group"><label class="col-md-3 control-label">Telepon/HP</label>
                                                <div class="col-md-9"><input type="text" name="telp" placeholder="Telepon/HP" class="form-control"> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none"><label class="col-md-3 control-label">Tanggal Instalasi</label>
                                                <div class="col-md-9"><input type="text" name="tgl_instalasi" placeholder="Tanggal Instalasi" class="form-control date"> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-md-3 control-label  text-danger">Expired at</label>
                                                <div class="col-md-9"><input type="text" name="expired" placeholder="Paket kadaluarsa" class="form-control date"> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <!-- <div class="form-group"><label class="col-md-3 control-label ">ODP Name</label>
                                                <div class="col-md-9"><select name="id_odp" id="id_odp" class="form-control" style="display: none;"></select> <span class="help-block m-b-none"></span>
                                                </div>
                                            </div> -->

                                            <div class="form-group">
                                                <label class="col-md-3 control-label text-danger">ODP Name</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <select name="id_odp" id="id_odp" class="form-control" style="display: none;"></select>
                                                            <span class="help-block m-b-none"></span>
                                                        </div>
                                                        <div class="col-md-4" id="odpName">

                                                        </div>
                                                    </div>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label text-danger">Paket & Status</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <select name="id_paket" class="form-control"></select>
                                                            <span class="help-block m-b-none"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select name="status" class="form-control"></select> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label text-danger">S/N (ONT/STB)</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="text" name="serial_number" placeholder="Serial Number ONT" class="form-control">
                                                        </div>
                                                        <div class="col-md-6" style="display:none">
                                                            <input type="text" name="sn_stb" placeholder="S/N STB" class="form-control">
                                                        </div>
                                                    </div>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none">
                                                <label class="col-md-3 control-label text-danger">STB Username & Password</label>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="text" name="stb_username" placeholder="STB Username" class="form-control">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="stb_password" placeholder="STB Password" class="form-control">
                                                        </div>
                                                    </div>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>

                                            <div class="form-group"><label class="col-md-3 control-label">Lokasi Map</label>
                                                <div class="col-md-9">
                                                    <textarea name="lokasi_map" rows="2" placeholder="Lokasi Map" class="form-control"></textarea>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-md-3 control-label">Keterangan Tagihan</label>
                                                <div class="col-md-9">
                                                    <textarea name="keterangan" placeholder="Keterangan Tagihan" class="form-control"></textarea>
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-md-3 control-label">No Urut Penagihan</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="sort" placeholder="No Urut Kwitansi Penagihan" class="form-control">
                                                    <span class="help-block m-b-none"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="pull-right">
                                                <a href="#lompatAtas" class="btn btn-default" onclick="hideRegisForm()">Close</a>
                                                <!-- <button type="button" class="btn btn-primary ladda-button ladda-button-demo" data-style="expand-right" id="btnSave" onclick="save()"><span class="ladda-label">Register ONU</span></button> -->
                                                <button type="button" class="btn btn-primary ladda-button ladda-button-demo" data-style="expand-right" onclick="save()"><span id="btnSave" class="ladda-label">Register ONU</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <hr><br>
                            </div> <!-- end registerForm -->
                        </div>

                        <div class="xtable-responsive" id="los" style="display:none">
                            <table class="table table-condensed table-hover" id="tb-los">
                                <thead>
                                    <tr>
                                        <th class="text-danger">LOS</th>
                                        <th>Interface</th>
                                        <th>Name</th>
                                        <th>Expired</th>
                                        <th>Cause</th>
                                        <th>ODP</th>
                                        <th>ONT</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="xtable-responsive " id="expired" style="display:none">
                            <table class="table table-condensed table-hover" id="tb-expired">
                                <thead>
                                    <tr>
                                        <th class="text-warning ">EXPIRED</th>
                                        <th>Interface</th>
                                        <th>Nama</th>
                                        <th>Expire</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div id="info-detail" style="display:none">
                            <div class="panel panel-default shadow-sm">
                                <!-- Panel Header -->
                                <div class="panel-heading clearfix bg-primary text-white" style="padding: 12px 15px;">
                                    <div class="pull-left">
                                        <h4 class="panel-title" style="font-size: 18px; margin-top: 3px;">
                                            <i class="fa fa-user"></i> Detail Pelanggan: <strong id="val-nama-header">562. SUGIARTO</strong>
                                        </h4>
                                    </div>
                                    <div class="pull-right">
                                        <span id="val-ont-phase" style="font-size: 11px; padding: 5px 8px;">
                                            <i class="fa fa-check-circle"></i> WORKING
                                        </span>
                                        <span id="val-vendor-header" class="label label-info" style="font-size: 11px; padding: 5px 8px;">
                                            FIBERHOME
                                        </span>
                                    </div>
                                </div>

                                <!-- Panel Body -->
                                <div class="panel-body">
                                    <!-- Row 1: Key Info Cards -->
                                    <div class="row">
                                        <!-- Paket & Tarif -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="well well-sm text-center">
                                                <small class="text-muted text-uppercase">Paket Berlangganan</small>
                                                <h3 class="text-primary" style="margin: 5px 0 0 0; font-weight: bold;" id="val-paket">-- Mbps</h3>
                                                <p class="text-muted" style="margin-top: 3px;">
                                                    <i class="fa fa-tag"></i> Rp <span id="val-tarif">-</span> / bln
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Redaman (dBm) -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="well well-sm text-center">
                                                <small class="text-muted text-uppercase">Redaman (dBm)</small>
                                                <h3 class="text-success" style="margin: 5px 0 0 0; font-weight: bold;" id="val-dbm">-25.090 dBm</h3>
                                                <p class="text-success" style="margin-top: 3px;" id="val-signal-status">
                                                    <i class="fa fa-signal"></i> Signal Normal
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Tanggal Expire -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="well well-sm text-center">
                                                <small class="text-muted text-uppercase">Masa Aktif s/d</small>
                                                <h3 class="text-warning" style="margin: 5px 0 0 0; font-weight: bold;" id="val-expire">2026-08-20</h3>
                                                <p class="text-muted" style="margin-top: 3px;" id="val-status-langganan">
                                                    <i class="fa fa-calendar"></i> Status: Aktif
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Wilayah / Area -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="well well-sm text-center">
                                                <small class="text-muted text-uppercase">Wilayah / Area</small>
                                                <h3 class="text-info" style="margin: 5px 0 0 0; font-weight: bold;" id="val-wilayah">MAYOA</h3>
                                                <p class="text-muted" style="margin-top: 3px;">
                                                    <i class="fa fa-server"></i> IF: <strong id="val-interface-card">1/1/14:69</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <hr style="margin-top: 5px; margin-bottom: 20px;">

                                    <!-- Row 2: Detailed Attributes Table -->
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h5 class="text-bold"><i class="fa fa-list"></i> Informasi Tambahan</h5>
                                            <table class="table table-striped table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th width="35%">ID & Nama</th>
                                                        <td id="val-id-nama">Nama</td>
                                                    </tr>
                                                    <!-- Baris Tanggal Instalasi -->
                                                    <tr>
                                                        <th>Tgl. Instalasi</th>
                                                        <td id="val-tgl-instalasi"><i class="fa fa-calendar-check-o text-success"></i> 20 Juni 2026</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tipe/Vendor ONT</th>
                                                        <td><span class="label label-default" id="val-vendor-table">FIBERHOME</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Port Interface</th>
                                                        <td><code id="val-interface">1/1/14:69</code></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status ODP</th>
                                                        <td id="val-odp-container">
                                                            <span id="val-status-odp">ODP Kosong</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lokasi Pelanggan</th>
                                                        <td>
                                                            <span id="link-maps">
                                                                <i class="fa fa-map-marker text-danger"></i> Buka Google Maps
                                                            </span>
                                                            <span id="btn-show-map">
                                                                <i class="fa fa-map text-info"></i> Tampilkan di Peta Dashboard
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Quick Action Toolbar -->
                                        <div class="col-md-5">
                                            <h5 class="text-bold"><i class="fa fa-cogs"></i> Panel Kontrol & Aksi</h5>

                                            <!-- Info Tools Group -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading"><small><strong><i class="fa fa-info-circle"></i> Diagnostic Tools</strong></small></div>
                                                <div class="panel-body" style="padding: 10px;">
                                                    <button class="btn btn-sm btn-default btn-diag" style="margin: 5px;" data-type="wanip"><i class="fa fa-network-wired"></i> WAN IP</button>
                                                    <button class="btn btn-sm btn-default btn-diag" style="margin: 5px;" data-type="attenuation"><i class="fa fa-line-chart"></i> Attenuation</button>
                                                    <button class="btn btn-sm btn-default btn-diag" style="margin: 5px;" data-type="detail-info"><i class="fa fa-info"></i> Detail Info</button>
                                                    <button class="btn btn-sm btn-default btn-diag" style="margin: 5px;" data-type="iphost"><i class="fa fa-desktop"></i> IP Host</button>
                                                    <button class="btn btn-sm btn-default btn-diag" style="margin: 5px;" data-type="onu-run"><i class="fa fa-code"></i> Running Config</button>
                                                </div>
                                            </div>

                                            <!-- Management Actions -->
                                            <div class="btn-group btn-group-justified">
                                                <div class="btn-group">
                                                    <button id="btn-remote" class="btn btn-primary"><i class="fa fa-globe"></i> Remote Web</button>
                                                </div>
                                                <div class="btn-group">
                                                    <button id="btn-reboot" class="btn btn-warning"><i class="fa fa-refresh"></i> Reboot</button>
                                                </div>
                                                <!-- <a href="javascript:void(0)" id="btn-extend" class="btn btn-success"><i class="fa fa-calendar"></i> Perpanjang</a> -->
                                                <!-- Dropdown Operations -->
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fa fa-wrench"></i> Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" id="more_action">
                                                        <li><a href="#lompatAtas" id="btn-edit"><i class="glyphicon glyphicon-pencil"></i> Edit Pelanggan</a></li>
                                                        <li><a href="javascript:void(0)" id="btn-ticket"><i class="fa fa-ticket"></i> Make Ticket</a></li>
                                                        <li><a href="javascript:void(0)" id="btn-replace-ont"><i class="fa fa-exchange"></i> Replace ONT</a></li>
                                                        <li role="separator" class="divider"></li>
                                                        <li><a href="javascript:void(0)" id="btn-restore"><i class="fa fa-undo text-warning"></i> Restore Factory</a></li>
                                                        <li><a href="javascript:void(0)" id="btn-del-manual"><i class="fa fa-trash text-danger"></i> Delete Manual</a></li>
                                                        <li><a href="javascript:void(0)" id="btn-del-perm"><i class="fa fa-trash text-danger"></i> Delete Permanent</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <span class="pull-right">
                                    <button type="button" class="btn btn-default btn-sm btn-close-panel" id="btn-close-footer">
                                        <i class="fa fa-times text-danger"></i> Tutup
                                    </button>
                                </span>

                            </div>


                        </div>

                    </div> <!--ibox-content-->

                    <!-- <div class="ibox-footer">
                        
                    </div> -->
                </div>
            </div>

            <!-- TABEL PELANGGAN -->
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>LIST ONU</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="table-responsive" id="step4">
                            <table class="table table-condensed table-hover" id="table">
                                <thead>
                                    <tr>
                                        <th data-priority="1">Action</th>
                                        <th data-priority="2">Interface</th>
                                        <th data-priority="3">Nama</th>
                                        <th>Tgl Instalasi</th>
                                        <th data-priority="4">ontPhase</th>
                                        <th data-priority="5">dBm</th>
                                        <!-- <th>meter</th> -->
                                        <th>Paket</th>
                                        <th>Expire</th>
                                        <th>Tarif</th>
                                        <th>Status</th>
                                        <!-- <th>Aksi</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <!-- TABEL OFFLINE -->
            <div class="col-lg-12">
                <div class="ibox collapsed" id="divoffline">
                    <div class="ibox-title">
                        <h5>
                            <div class="text-uppercase v_offline"></div>
                        </h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#">Config option 1</a>
                                </li>
                                <li><a href="#">Config option 2</a>
                                </li>
                            </ul>
                            <!-- <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a> -->
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="xtable-responsive" id="offline" style="display:none">
                            <table class="table table-condensed table-hover" id="tb-offline">
                                <thead>
                                    <tr>
                                        <th class="text-info">OFFLINE</th>
                                        <th>Interface</th>
                                        <th>Name</th>
                                        <th>Cause</th>
                                        <th>Expired</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <!-- TABEL UNSPEC -->
            <div class="col-lg-12">
                <div class="ibox collapsed" id="divunspec">
                    <div class="ibox-title">
                        <h5>
                            <div class="text-uppercase v_unspec"><span class="fa fa-warning"></span> UNSPEC <small class="m-l-sm"> Laser pada ONT tidak normal!</small></div>
                        </h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="xtable-responsive" id="unspec">
                            <table class="table table-condensed table-hover" id="tb-unspec">
                                <thead>
                                    <tr>
                                        <th data-priority="1">Action</th>
                                        <th data-priority="2">Interface</th>
                                        <th data-priority="3">Nama</th>
                                        <th data-priority="4">ontPhase</th>
                                        <th data-priority="5">dBm</th>
                                        <th>meter</th>
                                        <th>Expire</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- END row 1 -->

        <!-- MODAL -->

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info" id="headerModalExample">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="exampleModalLabel">New message</h4>
                    </div>
                    <div class="modal-body">
                        <div id="rawdata"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info" id="headerMapModal">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="mapModalLabel">New message</h4>
                    </div>
                    <div class="modal-body">
                        <iframe id="iframeOnMap" src="" width="100%" height="450px" frameborder="0"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="extendPaket" tabindex="-1" role="dialog" aria-labelledby="extendPaketLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="extendPaketLabel">New message</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="formExtendPaket" action="#" class="form-horizontal">
                                <input type="text" name="md_gpon_onu" hidden>
                                <div class="col-md-12">
                                    <div class="form-group"><label class="col-md-3 control-label ">Kode Pelanggan</label>
                                        <div class="col-md-9"> <span class="text-primary" id="md_no_pelanggan" style="font-size: 20pt; font-weight: bold;"></span>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group"><label class="col-md-3 control-label ">Metode Pembayaran</label>
                                    <div class="col-md-9">
                                        <label class="checkbox-inline"> <input type="radio" value="transfer" id="transfer" name="metode_pembayaran" onchange="item('hide')"> Transfer </label>
                                        <label class="checkbox-inline"> <input type="radio" checked="" value="kolektor" id="kolektor" name="metode_pembayaran" onchange="item('hide')"> Kolektor </label>
                                        <label class="checkbox-inline"> <input type="radio" value="antar" id="antar" name="metode_pembayaran" onchange="item('show')"> Antar Langsung</label>
                                    </div>
                                </div>

                                <div class="form-group hidemode" style="display:none"><label class="col-md-3 control-label ">Pilih Penerima</label>
                                    <div class="col-md-9">
                                        <select name="id_karyawan" class="form-control"></select> <span class="help-block m-b-none"></span>
                                    </div>
                                </div>

                                <div class="form-group"><label class="col-md-3 control-label ">Remark Tarif</label>
                                    <div class="col-md-9">
                                        <input type="number" name="remark" step="5000" class="form-control input-lg m-b-sm" placeholder="Remark tarif setoran">
                                        
                                        <span class="text-primary" id="md_kd_pelanggan"></span>
                                    </div>
                                </div>

                                <div class="form-group"><label class="col-md-3 control-label">Keterangan</label>
                                    <div class="col-md-9">
                                        <textarea name="md_keterangan" placeholder="Keterangan" class="typeahead_1 form-control"></textarea>
                                    </div>
                                </div> -->

                                    <div class="form-group"><label class="col-md-3 control-label ">Paket</label>
                                        <div class="col-md-9">
                                            <input type="text" name="md_nama_paket" class="form-control m-b-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">Aktif s/d</label>
                                        <div class="col-md-9">
                                            <input type="text" name="md_tgl_expired" class="form-control m-b-sm date">
                                            <?php
                                            $tanggalSaatIni = date('Y-m-20');
                                            $tanggalSatuBulanKedepan = date('Y-m-d', strtotime($tanggalSaatIni . ' +1 month'));
                                            ?>
                                            <br>
                                            <!-- <button class="btn btn-xs btn-default" onclick="setTgl('</?= $tanggalSatuBulanKedepan ?>')"></?= $tanggalSatuBulanKedepan ?></button> -->
                                            <a href="javascript:void(0)" class="btn btn-sm btn-default text-danger text-bold" onclick="setTgl('<?= $tanggalSatuBulanKedepan ?>')"><?= $tanggalSatuBulanKedepan ?></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-warning" id="btnSaveExtendPaketSementara" onclick="setExtendPaket()"><span class="fa fa-send-o"></span> Sementara</button> -->
                        <button type="button" class="ladda-button ladda-button-demo btn btn-primary" data-style="expand-right" id="btnSaveExtendPaket" onclick="setExtendPaket()"><span class="fa fa-clock"></span> Extend</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="changessidModal" tabindex="-1" role="dialog" aria-labelledby="changessidLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="changessidLabel">Change SSID</h4>
                    </div>
                    <div class="modal-body">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-1"> WPA Passphrase</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-2">SSID</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-3">ALL</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="panel-body">
                                        <div class="row">
                                            <form id="formChangeSsid" action="#" class="form-horizontal">
                                                <input type="text" name="cs_gpon_onu" hidden>
                                                <div class="col-md-12">
                                                    <div class="form-group"><label class="col-md-3 control-label ">WPA Passphrase</label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="wpa_keyx" placeholder="Enter new WAP Key" class="form-control m-b-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- </form> -->
                                        </div>
                                        <button type="button" class="ladda-button ladda-button-demo btn btn-primary pull-right" data-style="expand-right" id="btnSaveSsidx" onclick="setSsid('wpa_key')">Change Key</button>



                                    </div>
                                </div>
                                <div id="tab-2" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="row">
                                            <!-- <form id="formChangeSsidy" action="#" class="form-horizontal">
                                            <input type="text" name="md_gpon_onu" hidden> -->
                                            <div class="col-md-12">
                                                <div class="form-group"><label class="col-md-3 control-label ">SSID</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="ssidy" placeholder="Enter new SSID" class="form-control m-b-sm">
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- </form> -->
                                        </div>
                                        <button type="button" class="ladda-button ladda-button-demo btn btn-primary pull-right" data-style="expand-right" id="btnSaveSsidy" onclick="setSsid('ssid')">Change SSID</button>
                                    </div>
                                </div>
                                <div id="tab-3" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="row">
                                            <!-- <form id="formChangeSsidz" action="#" class="form-horizontal">
                                            <input type="text" name="md_gpon_onu" hidden> -->
                                            <div class="col-md-12">
                                                <div class="form-group"><label class="col-md-3 control-label ">SSID</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="ssidz" placeholder="Enter new SSID" class="form-control m-b-sm">
                                                    </div>
                                                </div>
                                                <div class="form-group"><label class="col-md-3 control-label ">WPA Passphrase</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="wpa_keyz" placeholder="Enter new WAP Key" class="form-control m-b-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                        <button type="button" class="ladda-button ladda-button-demo btn btn-primary pull-right" data-style="expand-right" id="btnSaveSsidz" onclick="setSsid('both')">Change All</button>

                                    </div>
                                </div>
                            </div>


                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="replaceOntModal" tabindex="-1" role="dialog" aria-labelledby="replaceOntLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="replaceOntLabel">Replace ONT</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="formReplaceOnt" action="#" class="form-horizontal">
                                <!-- <input type="text" name="rep_gpon_onu" hidden> -->
                                <div class="col-md-12">
                                    <div class="form-group"><label class="col-md-3 control-label ">Interface ONU</label>
                                        <div class="col-md-9">
                                            <input type="text" name="rep_gpon_onu" class="form-control m-b-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">Name</label>
                                        <div class="col-md-9">
                                            <input type="text" name="rep_name" class="form-control m-b-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">Old S/N</label>
                                        <div class="col-md-9">
                                            <input type="text" name="rep_old_sn" class="form-control m-b-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">New S/N</label>
                                        <div class="col-md-9">
                                            <input type="text" name="rep_new_sn" placeholder="S/N ONT Baru" class="form-control m-b-sm">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">Onu Type</label>
                                        <div class="col-md-9">
                                            <select name="rep_onutype" class="form-control">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnSaveReplaceOnt" onclick="setReplaceOnt()"><span class="fa fa-clock"></span> Replace ONT</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ticketsModal" tabindex="-1" role="dialog" aria-labelledby="ticketsLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="ticketsLabel">Tickets</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="formReplaceOnt" action="#" class="form-horizontal">
                                <div class="col-md-12">
                                    <div class="form-group"><label class="col-md-3 control-label ">Interface ONU</label>
                                        <div class="col-md-9">
                                            <input type="text" name="tic_gpon_onu" class="form-control m-b-sm" disabled>
                                        </div>
                                    </div>
                                    <!-- <div class="form-group"><label class="col-md-3 control-label ">Name</label>
                                    <div class="col-md-9">
                                        <input type="text" name="tic_name" class="form-control m-b-sm" disabled>
                                    </div>
                                </div> -->
                                    <div class="form-group"><label class="col-md-3 control-label ">Keluhan</label>
                                        <div class="col-md-9">
                                            <input type="text" name="tic_keluhan" class="form-control m-b-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-md-3 control-label ">Scripts</label>
                                        <div class="col-md-9">
                                            <textarea id="skrip" name="tic_scripts" class="form-control" rows="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" onclick="copyText()">Copy Scripts</button>
                        <!-- <button type="button" class="btn btn-primary" id="btnSaveTickets" onclick="getTickets()"><span class="fa fa-clock"></span> Send to Teknisi</button> -->
                        <button type="button" class="btn btn-danger" id="btnSendTicketGroupTg" onclick="sendTicket('grouptg')"><span class="fa fa-telegram"></span> Group Telegram</button>
                        <button type="button" class="btn btn-primary" id="btnSendTicketGroupWa" onclick="sendTicket('groupwa')"><span class="fa fa-whatsapp"></span> Group WhatsApp</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>