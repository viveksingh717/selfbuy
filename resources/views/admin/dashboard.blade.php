@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('subTitle', 'Dashboard')

@section('style')  
@endsection

@section('content')
<div class="section-body mt-3">
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="mb-4">
                    <h4>Welcome {{$adminDetails->name}}!</h4>
                    <small>Here is Admin Panel Shop Management. <a href="javascript:void(0)">Go TO Shop</a></small>
                </div>                        
            </div>
        </div>
        <div class="row clearfix row-deck">
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Orders</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="number mb-0 font-32 counter">31</h5>
                        <span class="font-12">Measure How Fast... <a href="#">More</a></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pending Orders</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="number mb-0 font-32 counter">245</h5>
                        <span class="font-12">Measure How Fast... <a href="#">More</a></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Process</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="number mb-0 font-32 counter">17</h5>
                        <span class="font-12">Measure How Fast... <a href="#">More</a></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Success Status</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="number mb-0 font-32 counter">12</h5>
                        <span class="font-12">Measure How Fast... <a href="#">More</a></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Open Requests</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="number mb-0 font-32 counter">19</h5>
                        <span class="font-12">Measure How Fast... <a href="#">More</a></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Active Orders</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="number mb-0 font-32 counter">284</h5>
                        <span class="font-12">Measure How Fast... <a href="#">More</a></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix row-deck">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order Analytics</h3>
                        <div class="card-options">
                            <button class="btn btn-sm btn-outline-secondary mr-1" id="one_month">1M</button>
                            <button class="btn btn-sm btn-outline-secondary mr-1" id="six_months">6M</button>
                            <button class="btn btn-sm btn-outline-secondary mr-1" id="one_year" class="active">1Y</button>
                            <button class="btn btn-sm btn-outline-secondary mr-1" id="ytd">YTD</button>
                            <button class="btn btn-sm btn-outline-secondary" id="all">ALL</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="apex-timeline-chart"></div>
                    </div>
                </div>                
            </div>
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Current Ticket Status</h3>
                        <div class="card-options">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fa fa-expand"></i></a>
                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-sm-flex justify-content-between">
                            <div class="font-12">as of 10th to 17th of Jun 2019</div>
                            <div class="selectgroup w250">
                                <label class="selectgroup-item">
                                    <input type="radio" name="intensity" value="Day" class="selectgroup-input" checked="">
                                    <span class="selectgroup-button">1D</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="intensity" value="Week" class="selectgroup-input">
                                    <span class="selectgroup-button">1W</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="intensity" value="Month" class="selectgroup-input">
                                    <span class="selectgroup-button">1M</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="intensity" value="Year" class="selectgroup-input">
                                    <span class="selectgroup-button">1Y</span>
                                </label>
                            </div>
                        </div>
                        <div id="chart-combination" style="height: 205px"></div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6 col-xl-3 col-md-6">
                                <h5>05</h5>
                                <div class="clearfix">
                                    <div class="float-left"><strong>35%</strong></div>
                                    <div class="float-right"><small class="text-muted">Yesterday</small></div>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-gray" role="progressbar" style="width: 35%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-uppercase font-10">New Tickets</span>
                            </div>
                            <div class="col-6 col-xl-3 col-md-6">
                                <h5>18</h5>
                                <div class="clearfix">
                                    <div class="float-left"><strong>61%</strong></div>
                                    <div class="float-right"><small class="text-muted">Yesterday</small></div>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-gray" role="progressbar" style="width: 61%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-uppercase font-10">Open Tickets</span>
                            </div> 
                            <div class="col-6 col-xl-3 col-md-6">
                                <h5>06</h5>
                                <div class="clearfix">
                                    <div class="float-left"><strong>100%</strong></div>
                                    <div class="float-right"><small class="text-muted">Yesterday</small></div>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-gray" role="progressbar" style="width: 100%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-uppercase font-10">Solved Tickets</span>
                            </div>
                            <div class="col-6 col-xl-3 col-md-6">
                                <h5>11</h5>
                                <div class="clearfix">
                                    <div class="float-left"><strong>87%</strong></div>
                                    <div class="float-right"><small class="text-muted">Yesterday</small></div>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-gray" role="progressbar" style="width: 87%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-uppercase font-10">Unresolved</span>
                            </div>                                                                       
                        </div>
                    </div>
                </div>                
            </div>
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Project Statistics</h3>
                        <div class="card-options">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fa fa-expand"></i></a>
                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4 border-right pb-4 pt-4">
                                <label class="mb-0 font-13">Total Project</label>
                                <h4 class="font-30 font-weight-bold text-col-blue counter">42</h4>
                            </div>
                            <div class="col-4 border-right pb-4 pt-4">
                                <label class="mb-0 font-13">On Going</label>
                                <h4 class="font-30 font-weight-bold text-col-blue counter">23</h4>
                            </div>
                            <div class="col-4 pb-4 pt-4">
                                <label class="mb-0 font-13">Pending</label>
                                <h4 class="font-30 font-weight-bold text-col-blue counter">8</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter mb-0">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>35%</strong></div>
                                            <div class="float-right"><small class="text-muted">Design Team</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-azure" role="progressbar" style="width: 35%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>25%</strong></div>
                                            <div class="float-right"><small class="text-muted">Developer Team</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-green" role="progressbar" style="width: 25%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>15%</strong></div>
                                            <div class="float-right"><small class="text-muted">Marketing</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-orange" role="progressbar" style="width: 15%" aria-valuenow="36" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>20%</strong></div>
                                            <div class="float-right"><small class="text-muted">Management</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-indigo" role="progressbar" style="width: 20%" aria-valuenow="6" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>11%</strong></div>
                                            <div class="float-right"><small class="text-muted">Other</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-pink" role="progressbar" style="width: 11%" aria-valuenow="6" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  
@endsection

@section('script') 
<script src="{{asset('admin_assets/bundles/apexcharts.bundle.js')}}"></script>
<script src="{{asset('admin_assets/bundles/counterup.bundle.js')}}"></script>
<script src="{{asset('admin_assets/bundles/knobjs.bundle.js')}}"></script>
<script src="{{asset('admin_assets/bundles/c3.bundle.js')}}"></script>
<script src="{{asset('admin_assets/js/page/project-index.js')}}"></script>
@endsection
