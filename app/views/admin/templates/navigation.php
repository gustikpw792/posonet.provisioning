<body class="top-navigation">

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom white-bg">
                <nav class="navbar navbar-static-top" role="navigation">
                    <div class="navbar-header">
                        <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                            <i class="fa fa-reorder"></i>
                        </button>
                        <a href="<?= site_url('quicklink') ?>" class="navbar-brand">POSO Net
                            <!-- <img src="<//?php echo base_url('assets/posonet/img/logo_report.png') ?>" style="padding-top:5px;" class="img-responsive"> -->
                        </a>
                    </div>
                    <div class="navbar-collapse collapse" id="navbar">
                        <ul class="nav navbar-nav">

                            <?php if ($this->session->level == 'administrator'): ?>

                            <li class="<?= ($active == "dashboard") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="<?= site_url('dashboard') ?>"> Dashboard</a>
                            </li>
                            <li class="dropdown <?= ($active == "paket" || $active == "karyawan" || $active == "wilayah") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-users"></span> Master <span class="caret"></span>
                                </a>
                                <ul role="menu" class="dropdown-menu">
                                    <!-- <li><a href="</?= site_url('dashboard/pelanggan') ?>">Data Pelanggan</a></li> -->
                                    <li><a href="<?= site_url('dashboard/karyawan') ?>">Karyawan</a></li>
                                    <li><a href="<?= site_url('dashboard/kolektor') ?>">Kolektor</a></li>
                                    <li><a href="<?= site_url('dashboard/paket') ?>">Paket</a></li>
                                    <li><a href="<?= site_url('dashboard/wilayah') ?>">Wilayah</a></li>
                                </ul>
                            </li>

                            <?php endif; ?>

                            <?php if ($this->session->level == 'administrator' || $this->session->level == 'kolektor'): ?>

                            <li class="dropdown <?= ($active == "pelanggan") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-desktop"></span> Provisioning <span class="caret"></span>
                                </a>
                                <ul role="menu" class="dropdown-menu">
                                    <li></span><a href="<?= site_url('dashboard/pelanggan') ?>">ONU</a></li>
                                    <li><a href="#">ONU Type</a></li>
                                </ul>
                            </li>

                            <!-- <li class="</?= ($active == "pembayaran") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="</?= site_url('dashboard/pembayaran') ?>"><span class="fa fa-area-chart"></span> Pembayaran </a>
                            </li> -->

                            <?php endif; ?>

                            <?php if ($this->session->level == 'administrator'): ?>

                            <li class="dropdown <?= ($active == "kwitansi" || $active == "setoran") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-calculator"></span> Transaksi <span class="caret"></span>
                                </a>
                                <ul role="menu" class="dropdown-menu">
                                    <li><a href="<?= site_url('dashboard/kwitansi') ?>">Kwitansi</a></li>
                                    <li><a href="<?= site_url('dashboard/master_setoran') ?>">Setoran</a></li>
                                </ul>
                            </li>

                            <!-- <li class="</?= ($active == "pembayaran") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="</?= site_url('dashboard/pembayaran') ?>"><span class="fa fa-area-chart"></span> Pembayaran </!--span class="badge badge-info">New (1)--/></a>
                            </li> -->

                            <li class="">
                                <a aria-expanded="false" role="button" href="<?= site_url('dashboard/pengeluaran') ?>"><span class="fa fa-area-chart"></span> Pengeluaran <!--span class="badge badge-info">New (1)--></a>
                            </li>
                            <li <?= ($active == "laporan") ? 'active' : '' ?>">
                                <a aria-expanded="false" role="button" href="<?= site_url('dashboard/laporan') ?>">
                                    <span class="fa fa-file-excel-o"></span> Laporan</span></a>
                            </li>
                            <li class="dropdown">
                                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-cogs"></span> Pengaturan
                                    <!--span class="badge badge-info">New (1)</span--> <span class="caret"></span>
                                </a>
                                <ul role="menu" class="dropdown-menu">
                                    <!-- <li><a href="">Profil Perusahaan</a></li> -->
                                    <li><a href="<?= site_url('dashboard/backup') ?>">Backup Database <span class="badge badge-info">1</span></a></li>
                                    <li><a href="<?= site_url('dashboard/settings') ?>">Advance Setting</a></li>
                                    <!-- <li><a href="">Menu item</a></li>
                            <li><a href="">Menu item</a></li> -->
                                </ul>
                            </li>
                            <!-- <li class="dropdown">
                                <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-cogs"></span> CRM
                                    <span class="badge badge-info">New (1)
                                    </span>
                                    <span class="caret"></span>
                                </a>
                                <ul role="menu" class="dropdown-menu">
                                    <li><a href="">Profil Perusahaan</a></li>
                                    <li><a href="<\?= site_url('dashboard/wa_notif') ?>">Set Notif <span class="badge badge-info">1</span></a></li>
                                    <li><a href="<\?= site_url('wa_notif/get_qr') ?>">Link Device <span class="badge badge-danger">Unlinked</span><span class="badge badge-info">Linked</span></a></li>
                                    <li><a href="<\?= site_url('dashboard/set_wa_template') ?>">WhatsApp Template</a></li> 
                                    <li><a href="">Menu item</a></li>
                            <li><a href="">Menu item</a></li> 
                                </ul>
                            </li> -->

                            <?php endif; ?>


                        </ul>
                        <ul class="nav navbar-top-links navbar-right">
                            <li><strong><?php echo $this->session->username; ?></strong></li>
                            <li>
                                <a href="<?= site_url('logout') ?>">
                                    <i class="fa fa-sign-out"></i> Log out
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>