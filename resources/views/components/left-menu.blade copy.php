<?php
$route_group = Request::route()->getName();
$array_route_group = explode('.', $route_group);
$count = 1;
?>
@foreach (config('menus') as $k => $item)
    <?php $count++; ?>
    <!-- nếu có menu con -->
    @if (!empty($item['child_menu']))
        <?php
        $active = false;
        $show_nav_item = false;
        ?>

        @foreach ($item['child_menu'] as $keyVal => $val)
            <?php
            if (in_array($val['route_group'], $array_route_group)) {
                # code...
                $active = true;
            }
            ?>
            @can(@$val['permission'])
                <?php
                $show_nav_item = true;
                ?>
            @endcan
        @endforeach

        <!-- nếu bât cứ menu cấp 2 mà có quyền thì mới hiện menu cấp 1 -->
        @if ($show_nav_item)
            <li class="nav-item">
                <!-- hiện menu cấp 1 -->
                <a class="nav-link menu-link {{ $active ? 'active' : 'collapse' }}"
                    href="#sidebarDashboards{{ $count }}" data-bs-toggle="collapse"
                    aria-expanded="{{ $active ? 'true' : 'false' }}" aria-controls="sidebarDashboards"
                    style="color: #6d7080">
                    <span>
                        <i class="{{ $item['class'] }}"></i>
                        {{ $k }} <!-- tên menu -->
                    </span>
                </a>
                <!-- hiện menu cấp 1 -->

                <!-- hiện menu cấp 2 -->
                <div class="menu-dropdown {{ $active ? '' : 'collapse' }}" id="sidebarDashboards{{ $count }}"
                    style="">
                    <ul class="nav nav-sm flex-column">
                        @foreach ($item['child_menu'] as $keyVal => $val)
                            @can(@$val['permission'], 'web')
                                <li class="nav-item">
                                    <a href="{{ route($val['route']) }}"
                                        class="nav-link {{ strpos($route_group, $val['route_group']) === 0 ? 'active' : '' }}"
                                        style="font-size: .875rem !important; background-color: #0f1c2f00 !important;">
                                        @if (!empty($val['class']))
                                            <i class="{{ $val['class'] }}">
                                            </i>
                                        @endif
                                        {{ $keyVal }}
                                    </a>
                                </li>
                            @endcan
                        @endforeach
                    </ul>
                </div>
                <!-- hiện menu cấp 2 -->
            </li>
        @endif
    @else
        <!-- nếu menu con rỗng -->
        <li class="nav-item">
            {{-- menu have do not have submenu --}}
            <a class="nav-link menu-link {{ strpos($route_group, $item['route_group']) === 0 ? 'active' : '' }}"
                href="{{ route($item['route']) }}">
                <span>
                    <i class="{{ $item['class'] }}"> </i> {{ $k }}
                </span>
            </a>
        </li>
    @endif
@endforeach

<style>
    .navbar-menu .navbar-nav .nav-link[data-bs-toggle=collapse][aria-expanded=true]:after {
        color: #6d7080;
    }

    [data-layout=vertical][data-sidebar=dark] .navbar-nav .nav-sm .nav-link:before {
        background-color: #19314900 !important;
    }

    [data-layout=vertical][data-sidebar=dark] .navbar-nav .nav-sm .nav-link:hover:before {
        background-color: #19314900 !important;
    }
</style>
