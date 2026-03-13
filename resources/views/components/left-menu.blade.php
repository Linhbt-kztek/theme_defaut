<?php
$route_group = Request::route()->getName();
$array_route_group = explode('.', $route_group);
$count = 1;
?>
@foreach (config('menus') as $k => $item)
    <?php $count++; ?>
    <!-- nếu có menu con -->
    @if (!empty($item['child_menu']))
        @include('components.include.childMenu')
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

    [data-layout=vertical][data-sidebar=light] .navbar-nav .nav-sm .nav-link:before {
        background-color: #19314900 !important;
    }

    [data-layout=vertical][data-sidebar=light] .navbar-nav .nav-sm .nav-link:hover:before {
        background-color: #19314900 !important;
    }
</style>
