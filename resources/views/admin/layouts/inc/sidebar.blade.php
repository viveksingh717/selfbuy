{{-- Rightside bar --}}

<div id="rightsidebar" class="right_sidebar">
    <a href="javascript:void(0)" class="p-3 settingbar float-right"><i class="fa fa-close"></i></a>
    <div class="p-4">
        <div class="mb-4">
            <h6 class="font-14 font-weight-bold text-muted">Font Style</h6>
            <div class="custom-controls-stacked font_setting">
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" name="font" value="font-opensans">
                    <span class="custom-control-label">Open Sans Font</span>
                </label>
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" name="font" value="font-montserrat"
                        checked="">
                    <span class="custom-control-label">Montserrat Google Font</span>
                </label>
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" name="font" value="font-roboto">
                    <span class="custom-control-label">Robot Google Font</span>
                </label>
            </div>
        </div>
        <hr>

        <div>
            <h6 class="font-14 font-weight-bold mt-4 text-muted">General Settings</h6>
            <ul class="setting-list list-unstyled mt-1 setting_switch">
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Night Mode</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-darkmode">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Fix Navbar top</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-fixnavbar">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Header Dark</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-pageheader"
                            checked="">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Min Sidebar Dark</span>
                        <input type="checkbox" name="custom-switch-checkbox"
                            class="custom-switch-input btn-min_sidebar">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Sidebar Dark</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-sidebar">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Icon Color</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-iconcolor">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Gradient Color</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-gradient">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Box Shadow</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-boxshadow">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">RTL Support</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-rtl">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
                <li>
                    <label class="custom-switch">
                        <span class="custom-switch-description">Box Layout</span>
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-boxlayout">
                        <span class="custom-switch-indicator"></span>
                    </label>
                </li>
            </ul>
        </div>
        {{-- <hr> --}}
    </div>
</div>

{{-- <div class="user_div">
    <h5 class="brand-name mb-4">Admin<a href="{{route('admin.logout')}}" class="user_btn"><i class="fa fa-sign-out"></i></a></h5>
    <div class="card-body">
        <a href="page-profile.html"><img class="card-profile-img" src="{{asset('admin_assets/images/sm/avatar1.jpg')}}" alt=""></a>
        <h6 class="mb-0">{{Auth::guard('admin')->user()->name}}</h6>
        <span>{{Auth::guard('admin')->user()->email}}</span>
    </div>
</div> --}}

{{-- Leftside bar --}}
<div id="left-sidebar" class="sidebar ">
    <h5 class="brand-name">
        SelfBuy
        <a href="javascript:void(0)" class="menu_option float-right">
            <i class="icon-grid font-16" data-toggle="tooltip" title="Grid & List Toggle"></i>
        </a>
    </h5>

    <nav id="left-sidebar-nav" class="sidebar-nav">
        <ul class="metismenu">

            <li class="g_heading">Services</li>

            <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-tachometer"></i><span>Dashboard</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/category') || Request::is('admin/create_category') ? 'active' : '' }}">
                <a href="{{ route('admin.category') }}">
                    <i class="fa fa-list"></i><span>Category List</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/sub_category') ? 'active' : '' }}">
                <a href="{{ route('admin.sub_category') }}">
                    <i class="fa fa-sitemap"></i><span>Sub Category</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/product') ? 'active' : '' }}">
                <a href="{{ route('admin.product') }}">
                    <i class="fa fa-cubes"></i><span>Product List</span>
                </a>
            </li>

            <li
                class="{{ Request::is('admin/color') ||
                Request::is('admin/create_color') ||
                Request::is('admin/edit_color*') ||
                Request::is('admin/size') ||
                Request::is('admin/create_size') ||
                Request::is('admin/brand') ||
                Request::is('admin/create_brand') ||
                Request::is('admin/edit_brand*') ||
                Request::is('admin/coupon') ||
                Request::is('admin/tax')
                    ? 'active'
                    : '' }}">
                <a href="javascript:void(0)" class="has-arrow arrow-c">
                    <i class="fa fa-sliders"></i><span>Product Attribute</span>
                </a>
                <ul
                    class="{{ Request::is('admin/color') ||
                    Request::is('admin/create_color') ||
                    Request::is('admin/edit_color*') ||
                    Request::is('admin/size') ||
                    Request::is('admin/create_size') ||
                    Request::is('admin/brand') ||
                    Request::is('admin/create_brand') ||
                    Request::is('admin/edit_brand*') ||
                    Request::is('admin/coupon') ||
                    Request::is('admin/tax')
                        ? 'in'
                        : '' }}">
                    <li
                        class="{{ Request::is('admin/color') || Request::is('admin/create_color') || Request::is('admin/edit_color*') ? 'active' : '' }}">
                        <a href="{{ route('admin.color') }}"><i class="fa fa-paint-brush"></i> Colour</a>
                    </li>
                    <li class="{{ Request::is('admin/size') || Request::is('admin/create_size') ? 'active' : '' }}">
                        <a href="{{ route('admin.size') }}"><i class="fa fa-text-width"></i> Size</a>
                    </li>
                    <li
                        class="{{ Request::is('admin/brand') || Request::is('admin/create_brand') || Request::is('admin/edit_brand*') ? 'active' : '' }}">
                        <a href="{{ route('admin.brand') }}"><i class="fa fa-tags"></i> Brand</a>
                    </li>

                    <li
                        class="{{ Request::is('admin/coupon') || Request::is('admin/create_coupon') || Request::is('admin/edit_coupon*') ? 'active' : '' }}">
                        <a href="{{ route('admin.coupon') }}"><i class="fa fa-ticket"></i> Coupons</a>
                    </li>

                    <li class="{{ Request::is('admin/tax') ? 'active' : '' }}">
                        <a href="{{ route('admin.tax') }}"><i class="fa fa-percent"></i> Tax</a>
                    </li>
                </ul>
            </li>

            <li class="{{ Request::is('admin/order') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-shopping-cart"></i><span>Order List</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/transation_history') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-exchange"></i><span>Transaction History</span>
                </a>
            </li>

            <li class="g_heading">App</li>

            <li class="{{ Request::is('admin/profile') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-user-circle"></i><span>Profile</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/setting') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-cog"></i><span>Setting</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-image"></i><span>Gallery</span>
                </a>
            </li>

            <li class="g_heading {{ Request::is('admin/support') ? 'active' : '' }}">
                Support
            </li>

            <li class="{{ Request::is('admin/help') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-question-circle"></i><span>Need Help?</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/contact_us') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-envelope"></i><span>Contact Us</span>
                </a>
            </li>

        </ul>
    </nav>
</div>
