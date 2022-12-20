<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal  menu bg-menu-theme flex-grow-0">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            <!-- Dashboards -->
            <li class="menu-item @if(Request::path() == 'user/dashboard') active @endif">
                <a href="{{route('dashboard')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item @if(Request::path() == 'user/products') active @endif">
                <a href="{{route('products.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Products</div>
                </a>
            </li>

            <li class="menu-item @if(Request::path() == 'user/orders') active @endif">
                <a href="{{route('orders.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Orders</div>
                </a>
            </li>

            <li class="menu-item @if(Request::path() == 'user/users') active @endif">
                <a href="{{route('users.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Users</div>
                </a>
            </li>

            <!-- Misc -->
            <li class="menu-item @if(Request::is('user/lookup/*') || Request::is('user/lookup-image/*')) active @endif">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-shape-circle"></i>
                    <div data-i18n="Misc">Configuration</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item @if(Request::path() == 'user/sizes') active @endif">
                        <a href="{{route('lookup.types.index','size')}}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Size</div>
                        </a>
                    </li>

                    <li class="menu-item @if(Request::path() == 'user/search-term') active @endif">
                        <a href="{{route('lookup.types.index','search-term')}}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Search Term</div>
                        </a>
                    </li>

                    <li class="menu-item @if(Request::path() == 'user/lookup/code') active @endif">
                        <a href="{{route('lookup.types.index','code')}}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Code</div>
                        </a>
                    </li>

                    <li class="menu-item @if(Request::path() == 'user/lookup-image/category') active @endif">
                        <a href="{{route('lookup.image_types.index','category')}}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Category</div>
                        </a>
                    </li>

                    <li class="menu-item @if(Request::path() == 'user/lookup-image/category/sub-category') active @endif">
                        <a href="{{route('lookup.sub_image_types.index',['category','sub-category'])}}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Sub Category</div>
                        </a>
                    </li>

                    <li class="menu-item @if(Request::path() == 'user/sub-sub-categories') active @endif">
                        <a href="{{route('sub_sub_categories.index')}}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-support"></i>
                            <div data-i18n="Support">Sub Sub Category</div>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>