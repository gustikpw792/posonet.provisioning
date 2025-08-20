<div class="wrapper wrapper-content">
    <div class="container">

        <div class="row">

            <div class="col-sm-8">
                <div class="m-l-n-xs">
                    <img src="<?= base_url('assets/posonet/img/poso_net_fit.png') ?>" class="img-rounded" alt="" width="150"><br>
                </div>

                <small class="m-xs">
                    PT. POSO MEDIA VISION
                </small>
                <br><br>
                <div class="border-bottom"></div>
                <!-- <div id="sparkline1" class="m-b-sm"></div> -->
                <div id="ibox2">
                    <div class="ibox-content">
                        <div class="sk-spinner sk-spinner-wave">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 border-right">
                                <small class="stats-label cdet15x">Target Rp</small>
                                <h2 class="no-margins"><span class="font-bold det15" style="display: none;">0</span></h2>
                            </div>
                            <div class="col-xs-3 border-right">
                                <small class="stats-label cdet16x">Tercapai Rp</small>
                                <h2 class="no-margins"><span class="font-bold det16" style="display: none;">0</span></h2>
                                <span class="no-margins text-info" style="font-size: 12pt;"><span class="det18"></span></span>
                            </div>
                            <div class="col-xs-3 border-right">
                                <small class="stats-label">% Tercapai</small>
                                <h2 class="no-margins"><span class="det17"></span></h2>
                            </div>
                            <div class="col-md-3">
                                <small class="stats-label">Bulan Penagihan</small>
                                <div class="input-group"><input type="text" id="bulan_penagihan" value="<?php echo date('Y-m') ?>" placeholder="Bulan Penagihan" class="form-control date" readonly>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary" onclick="getSummary()">Set!</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-bottom"></div>

            </div>
            <div class="col-sm-4">
                <!-- <h1 class="m-b-xs">
                        98,100
                    </h1>
                    <small>
                        Sales in last 24h
                    </small>
                    <div id="sparkline2" class="m-b-sm"></div>
                    <div class="row">
                        <div class="col-xs-4">
                            <small class="stats-label">Pages / Visit</small>
                            <h4>166 781.80</h4>
                        </div>

                        <div class="col-xs-4">
                            <small class="stats-label">% New Visits</small>
                            <h4>22.45%</h4>
                        </div>
                        <div class="col-xs-4">
                            <small class="stats-label">Last week</small>
                            <h4>862.044</h4>
                        </div>
                    </div> -->


            </div>
            <div class="col-sm-4">

                <div class="row m-t-xs">
                    <div class="col-xs-6">
                        <h5 class="m-b-xs cdet1x">Total Customers</h5>
                        <h1 class="no-margins"><span class="det1" style="display: none;">0</span></h1>
                        <!-- <div class="font-bold text-navy">98% <i class="fa fa-bolt"></i></div> -->
                    </div>
                    <div class="col-xs-6">
                        <h5 class="m-b-xs cdet7x">Inactive</h5>
                        <h1 class="no-margins"><span class="det7" style="display: none;">0</span></h1>
                        <!-- <div class="font-bold text-navy">98% <i class="fa fa-bolt"></i></div> -->
                    </div>
                </div>


                <table class="table small m-t-sm">
                    <tbody>
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="text-info dropdown-toggle" id="menu1" data-toggle="dropdown">
                                        <strong><span>More</span></strong> Details
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                        <li><a role="menuitem" href="<?php echo site_url('dashboard/pelanggan?search=AKTIF') ?>" target="_blank"><i class="fa fa-circle-o text-info"></i> Aktif <span class="text-success font-bold det2 pull-right"></span></a></li>
                                        <li><a role="menuitem" href="<?php echo site_url('dashboard/pelanggan?search=NONAKTIF') ?>" target="_blank"><i class="fa fa-times-circle-o text-warning"></i> Non-Aktif<span class="text-danger font-bold det4 pull-right"></span></a></li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="text-info dropdown-toggle" id="menu2" data-toggle="dropdown">
                                        <strong><span class="det5">0</span></strong> Total Wilayah
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu det6" role="menu" aria-labelledby="menu2">
                                    </ul>
                                </div>
                            </td>

                        </tr>
                        <!-- <tr>
                            <td>
                                <strong>61</strong> Comments
                            </td>
                            <td>
                                <strong>54</strong> Articles
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>154</strong> Companies
                            </td>
                            <td>
                                <strong>32</strong> Clients
                            </td>
                        </tr> -->
                    </tbody>
                </table>



            </div>

        </div>

        <div class="row">
            <div class="col-md-8">
                <div>
                    <span class="pull-right text-right">
                        <small>Setoran tertinggi kolektor: <strong><span class="det10">Nama Kolektor</span></strong></small>
                        <br />
                        <span class="det11">0</span>
                    </span>
                    <h1 class="m-b-xs"><span class="det8" style="display: none;">0</span></h1>
                    <h3 class="font-bold no-margins">
                        Grafik Januari - <span class="det9">Bulan</span>
                    </h3>
                    <small>By all collectors.</small>
                </div>

                <div>
                    <canvas id="lineChart" height="150"></canvas>
                </div>

                <div class="m-t-md">
                    <small class="pull-right">
                        <i class="fa fa-clock-o"> </i>
                        Update on <span class="det12">00.00.0000</span><br>
                        <small>Grafik akan tersedia jika semua kolektor telah melakukan setoran min 1x</small>
                    </small>
                    <small>
                        <strong>Analisis setoran:</strong> Nilai telah berubah dari waktu ke waktu,<br> dan bulan lalu <span class="det13">0</span> mencapai level max <span class="det14">0</span>.
                    </small>
                </div>

            </div>
            <div class="col-md-4">
                <div class="border-left">
                    <canvas id="doughnutChart" width="100%"></canvas>
                </div>
            </div>
        </div>












        <!-- <div class="row">
            <div class="col-md-2">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-success pull-right">Monthly</span>
                        <h5>Views</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">386,200</h1>
                        <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                        <small>Total views</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-info pull-right">Annual</span>
                        <h5>Orders</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">80,800</h1>
                        <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                        <small>New orders</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-primary pull-right">Today</span>
                        <h5>visits</h5>
                    </div>
                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-md-6">
                                <h1 class="no-margins">$ 406,420</h1>
                                <div class="font-bold text-navy">44% <i class="fa fa-level-up"></i> <small>Rapid pace</small></div>
                            </div>
                            <div class="col-md-6">
                                <h1 class="no-margins">206,120</h1>
                                <div class="font-bold text-navy">22% <i class="fa fa-level-up"></i> <small>Slow pace</small></div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Monthly income</h5>
                        <div class="ibox-tools">
                            <span class="label label-primary">Updated 12.2015</span>
                        </div>
                    </div>
                    <div class="ibox-content no-padding">
                        <div class="flot-chart m-t-lg" style="height: 55px;">
                            <div class="flot-chart-content" id="flot-chart1"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div>
                            <span class="pull-right text-right">
                                <small>Average value of sales in the past month in: <strong>United states</strong></small>
                                <br />
                                All sales: 162,862
                            </span>
                            <h3 class="font-bold no-margins">
                                Half-year revenue margin
                            </h3>
                            <small>Sales marketing.</small>
                        </div>

                        <div class="m-t-sm">

                            <div class="row">
                                <div class="col-md-8">
                                    <div>
                                        <canvas id="lineChart" height="114"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <ul class="stat-list m-t-lg">
                                        <li>
                                            <h2 class="no-margins">2,346</h2>
                                            <small>Total orders in period</small>
                                            <div class="progress progress-mini">
                                                <div class="progress-bar" style="width: 48%;"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <h2 class="no-margins ">4,422</h2>
                                            <small>Orders in last month</small>
                                            <div class="progress progress-mini">
                                                <div class="progress-bar" style="width: 60%;"></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div class="m-t-md">
                            <small class="pull-right">
                                <i class="fa fa-clock-o"> </i>
                                Update on 16.07.2015
                            </small>
                            <small>
                                <strong>Analysis of sales:</strong> The value has been changed over time, and last month reached a level over $50,000.
                            </small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-warning pull-right">Data has changed</span>
                        <h5>User activity</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-4">
                                <small class="stats-label">Pages / Visit</small>
                                <h4>236 321.80</h4>
                            </div>

                            <div class="col-xs-4">
                                <small class="stats-label">% New Visits</small>
                                <h4>46.11%</h4>
                            </div>
                            <div class="col-xs-4">
                                <small class="stats-label">Last week</small>
                                <h4>432.021</h4>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-4">
                                <small class="stats-label">Pages / Visit</small>
                                <h4>643 321.10</h4>
                            </div>

                            <div class="col-xs-4">
                                <small class="stats-label">% New Visits</small>
                                <h4>92.43%</h4>
                            </div>
                            <div class="col-xs-4">
                                <small class="stats-label">Last week</small>
                                <h4>564.554</h4>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-4">
                                <small class="stats-label">Pages / Visit</small>
                                <h4>436 547.20</h4>
                            </div>

                            <div class="col-xs-4">
                                <small class="stats-label">% New Visits</small>
                                <h4>150.23%</h4>
                            </div>
                            <div class="col-xs-4">
                                <small class="stats-label">Last week</small>
                                <h4>124.990</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Custom responsive table </h5>
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
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-9 m-b-xs">
                                <div data-toggle="buttons" class="btn-group">
                                    <label class="btn btn-sm btn-white"> <input type="radio" id="option1" name="options"> Day </label>
                                    <label class="btn btn-sm btn-white active"> <input type="radio" id="option2" name="options"> Week </label>
                                    <label class="btn btn-sm btn-white"> <input type="radio" id="option3" name="options"> Month </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group"><input type="text" placeholder="Search" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-primary"> Go!</button> </span></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>

                                        <th>#</th>
                                        <th>Project </th>
                                        <th>Name </th>
                                        <th>Phone </th>
                                        <th>Company </th>
                                        <th>Completed </th>
                                        <th>Task</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Project <small>This is example of project</small></td>
                                        <td>Patrick Smith</td>
                                        <td>0800 051213</td>
                                        <td>Inceptos Hymenaeos Ltd</td>
                                        <td><span class="pie">0.52/1.561</span></td>
                                        <td>20%</td>
                                        <td>Jul 14, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Alpha project</td>
                                        <td>Alice Jackson</td>
                                        <td>0500 780909</td>
                                        <td>Nec Euismod In Company</td>
                                        <td><span class="pie">6,9</span></td>
                                        <td>40%</td>
                                        <td>Jul 16, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Betha project</td>
                                        <td>John Smith</td>
                                        <td>0800 1111</td>
                                        <td>Erat Volutpat</td>
                                        <td><span class="pie">3,1</span></td>
                                        <td>75%</td>
                                        <td>Jul 18, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Gamma project</td>
                                        <td>Anna Jordan</td>
                                        <td>(016977) 0648</td>
                                        <td>Tellus Ltd</td>
                                        <td><span class="pie">4,9</span></td>
                                        <td>18%</td>
                                        <td>Jul 22, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Alpha project</td>
                                        <td>Alice Jackson</td>
                                        <td>0500 780909</td>
                                        <td>Nec Euismod In Company</td>
                                        <td><span class="pie">6,9</span></td>
                                        <td>40%</td>
                                        <td>Jul 16, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Project <small>This is example of project</small></td>
                                        <td>Patrick Smith</td>
                                        <td>0800 051213</td>
                                        <td>Inceptos Hymenaeos Ltd</td>
                                        <td><span class="pie">0.52/1.561</span></td>
                                        <td>20%</td>
                                        <td>Jul 14, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Gamma project</td>
                                        <td>Anna Jordan</td>
                                        <td>(016977) 0648</td>
                                        <td>Tellus Ltd</td>
                                        <td><span class="pie">4,9</span></td>
                                        <td>18%</td>
                                        <td>Jul 22, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Project <small>This is example of project</small></td>
                                        <td>Patrick Smith</td>
                                        <td>0800 051213</td>
                                        <td>Inceptos Hymenaeos Ltd</td>
                                        <td><span class="pie">0.52/1.561</span></td>
                                        <td>20%</td>
                                        <td>Jul 14, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Alpha project</td>
                                        <td>Alice Jackson</td>
                                        <td>0500 780909</td>
                                        <td>Nec Euismod In Company</td>
                                        <td><span class="pie">6,9</span></td>
                                        <td>40%</td>
                                        <td>Jul 16, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Betha project</td>
                                        <td>John Smith</td>
                                        <td>0800 1111</td>
                                        <td>Erat Volutpat</td>
                                        <td><span class="pie">3,1</span></td>
                                        <td>75%</td>
                                        <td>Jul 18, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Gamma project</td>
                                        <td>Anna Jordan</td>
                                        <td>(016977) 0648</td>
                                        <td>Tellus Ltd</td>
                                        <td><span class="pie">4,9</span></td>
                                        <td>18%</td>
                                        <td>Jul 22, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Alpha project</td>
                                        <td>Alice Jackson</td>
                                        <td>0500 780909</td>
                                        <td>Nec Euismod In Company</td>
                                        <td><span class="pie">6,9</span></td>
                                        <td>40%</td>
                                        <td>Jul 16, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Project <small>This is example of project</small></td>
                                        <td>Patrick Smith</td>
                                        <td>0800 051213</td>
                                        <td>Inceptos Hymenaeos Ltd</td>
                                        <td><span class="pie">0.52/1.561</span></td>
                                        <td>20%</td>
                                        <td>Jul 14, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Gamma project</td>
                                        <td>Anna Jordan</td>
                                        <td>(016977) 0648</td>
                                        <td>Tellus Ltd</td>
                                        <td><span class="pie">4,9</span></td>
                                        <td>18%</td>
                                        <td>Jul 22, 2013</td>
                                        <td><a href="#"><i class="fa fa-check text-navy"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div> -->