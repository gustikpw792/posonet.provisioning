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
                                    <!-- <li class="active"><a data-toggle="tab" href="#tab-6"><span class="fa fa-wrench"></span> Profil</a></li> -->
                                    <!-- <li class=""><a data-toggle="tab" href="#tab-7"><span class="fa fa-warning"></span> Keamanan</a></li> -->
                                    <li class="active"><a data-toggle="tab" href="#tab-7"><span class="fa fa-ticket"></span> Rekening</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-8"><span class="fa fa-database"></span> Users Access</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-9" onclick="getTgBot()"><span class="fa fa-bell"></span> Notifications</a></li>
                                </ul>
                                <div class="tab-content ">
                                    <div id="tab-6" class="tab-pane">
                                        <div class="panel-body">
                                            <form id="form" action="#" class="form-horizontal">
                                                <input type="text" name="id_profil" hidden>
                                                <div class="col-md-6 b-r">
                                                    <div class="form-group"><label class="col-md-2 control-label">Nama Perusahaan</label>
                                                        <div class="col-md-10"><input type="text" name="nama_perusahaan" placeholder="Nama Perusahaan" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Alias</label>
                                                        <div class="col-md-10"><input type="text" name="alias" placeholder="Alias" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Slogan</label>
                                                        <div class="col-md-10"><input type="text" name="slogan" placeholder="Slogan" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Alamat</label>
                                                        <div class="col-md-10"><input type="text" name="alamat" placeholder="Alamat" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Email</label>
                                                        <div class="col-md-10"><input type="text" name="email" placeholder="Email" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group"><label class="col-md-2 control-label">Telepon</label>
                                                        <div class="col-md-10"><input type="text" name="telp" placeholder="Telepon" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Telp CS/Teknisi</label>
                                                        <div class="col-md-10"><input type="text" name="telp_cs" placeholder="Telp CS/Teknisi" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Kode Pos</label>
                                                        <div class="col-md-10"><input type="text" name="kodepos" placeholder="Kode Pos" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Pimpinan</label>
                                                        <div class="col-md-10"><input type="text" name="nama_pimpinan" placeholder="Nama Pimpinan" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Jabatan</label>
                                                        <div class="col-md-10"><input type="text" name="jabatan_pimpinan" placeholder="Jabatan Pimpinan" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="tab-7" class="tab-pane active">
                                        <div class="panel-body">
                                            <strong>Nomor Rekening akan tercetak di Kwitansi</strong><br><br>

                                            <form id="form" action="#" class="form-horizontal">
                                                <div class="col-md-6 b-r">
                                                    <div class="form-group"><label class="col-md-2 control-label">Bank</label>
                                                        <div class="col-md-10"><input onchange="upperCase()" id="bank" type="text" name="nama_bank" placeholder="Nama BANK" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Rekening</label>
                                                        <div class="col-md-10"><input type="text" name="no_rekening" placeholder="Nomor Rekening" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Pemilik Rekening</label>
                                                        <div class="col-md-10"><input type="text" name="nama_pemilik_pekening" placeholder="Nama Pemilik Rekening" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-primary btn-block" id="btnSaveRek" onclick="save('rekening')">Save Rekening</button>
                                                </div>

                                                <div class="col-md-6">
                                                    
                                                </div>
                                            </form>


                                        </div>
                                    </div>
                                    <div id="tab-8" class="tab-pane">
                                        <div class="panel-body">
                                            <h3>User Login</h3>
                                            
                                            <div class="table-responsiveX">
                                                <table class="table table-hover" id="tableUsers">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Username</th>
                                                            <th>Level</th>
                                                            <th>Owner</th>
                                                            <th>Aktif</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Modal -->

                                            <div class="modal inmodal" id="myModalUser" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" style="width:80%">
                                                    <div class="modal-content animated fadeInDown">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title">Tambah <?php echo ucwords(str_replace('_', ' ', $active)); ?> Pelanggan</h4>
                                                            <small class="font-bold"></small>
                                                        </div>
                                                        <div class="modal-body" id="step2">
                                                            <div class="row">
                                                                <form id="form_users" action="#" class="form-horizontal">
                                                                    <input type="text" name="id_users" hidden>
                                                                    <div class="col-md-6 b-r">
                                                                        <div class="form-group"><label class="col-md-2 control-label">Username</label>
                                                                            <div class="col-md-10"><input type="text" name="username" placeholder="Username" class="form-control"> <span class="help-block m-b-none"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group"><label class="col-md-2 control-label">Password</label>
                                                                            <div class="col-md-10"><input type="password" name="password" placeholder="Password" class="form-control"> <span class="help-block m-b-none"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group"><label class="col-md-2 control-label">Karyawan</label>
                                                                            <div class="col-md-10">
                                                                                <select name="id_karyawan" class="form-control">
                                                                                    <option value="">--SELECT--</option>
                                                                                </select> <span class="help-block m-b-none"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group"><label class="col-md-2 control-label">Privillege</label>
                                                                            <div class="col-md-10">
                                                                                <select name="level" class="form-control">
                                                                                    <option value="">--SELECT--</option>
                                                                                </select> <span class="help-block m-b-none"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group"><label class="col-md-2 control-label">Status</label>
                                                                            <div class="col-md-10">
                                                                                <select name="aktif" class="form-control">
                                                                                    <option value="aktif">AKTIF</option>
                                                                                    <option value="nonaktif">NONAKTIF</option>
                                                                                </select> <span class="help-block m-b-none"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group"><label class="col-md-2 control-label">Block Wilayah</label>
                                                                            <div class="col-md-10">
                                                                                <select name="akses_wilayah[]" class="multiSelect" multiple="multiple"></select>
                                                                            <span class="has-error">Pilih wilayah yang disembunyikan!</span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        Privillege: <br>
                                                                        <strong>Admin</strong> Bisa mengakses semua menu <br>
                                                                        <strong>Kolektor</strong> Hanya melakukan perpanjangan <br>
                                                                        <strong>Teknisi</strong> Hanya melakukan PSB <br>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-primary" id="btnSaveUsers" onclick="save('users')"><span class="fa fa-clock"></span> Save User</button>
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-9" class="tab-pane" >
                                        <div class="panel-body">
                                            <h3>Notif to TelegramBot</h3>

                                            <form id="formTelegram" action="#" class="form-horizontal">
                                                <div class="col-md-6 b-r">
                                                    <div class="form-group"><label class="col-md-2 control-label">Base URL Bot</label>
                                                        <div class="col-md-10"><input id="tg_base_url" type="text" value="https://api.telegram.org/" name="tg_base_url" placeholder="Base URL Bot" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Token</label>
                                                        <div class="col-md-10"><input id="tg_token" type="text" name="tg_token" placeholder="Token Telegram botXXXXXX:XXXXXXXXXX" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">Username Bot</label>
                                                        <div class="col-md-10"><input type="text" name="tg_username" placeholder="Username Bot" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">CHAT_ID ADMIN</label>
                                                        <div class="col-md-10"><input type="text" name="tg_chat_id_admin" placeholder="CHAT_ID ADMIN" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">CHAT_ID TEKNISI</label>
                                                        <div class="col-md-10"><input type="text" name="tg_chat_id_teknisi" placeholder="CHAT_ID TEKNISI" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><label class="col-md-2 control-label">CHAT_ID GROUP</label>
                                                        <div class="col-md-10"><input type="text" name="tg_chat_id_group" placeholder="CHAT_ID GROUP" class="form-control"> <span class="help-block m-b-none"></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-primary btn-block" id="btnSaveTgBot" onclick="save('tg_bot')">Save Notif</button>
                                                </div>

                                                <div class="col-md-6">
                                                    
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>