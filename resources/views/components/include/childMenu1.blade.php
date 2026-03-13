@php $active = false;$show_nav_item = false; @endphp
@foreach ($val['child_menu'] as $key_val => $value)
    @php
        if (in_array($value['route_group'], $array_route_group)) {
            # code...
            $active = true;
        }
    @endphp
    @can(@$value['permission'])
        @php($show_nav_item = true)
    @endcan
@endforeach

<!-- nếu bât cứ menu cấp 2 mà có quyền thì mới hiện menu cấp 1 -->
@if ($show_nav_item)
    <li class="nav-item">
        <!-- hiện menu cấp 1 -->
        <a class="nav-link {{ $active ? 'active' : 'collapse' }}" href="#sidebarDashboardss{{ $count }}"
            data-bs-toggle="collapse" aria-expanded="{{ $active ? 'true' : 'false' }}" aria-controls="sidebarDashboardss"
            style="font-size: .875rem !important; ">
            <span>
                <i class="{{ $val['class'] }}"></i>
                {{ $keyVal }} <!-- tên menu -->
            </span>
        </a>
        <!-- hiện menu cấp 1 -->

        <!-- hiện menu cấp 2 -->
        <div class="menu-dropdown {{ $active ? '' : 'collapse' }}" id="sidebarDashboardss{{ $count }}"
            style="">
            <ul class="nav nav-sm flex-column">
                @foreach ($val['child_menu'] as $key_val => $value)
                    @can(@$value['permission'], 'web')
                        <li class="nav-item">
                            <a href="{{ route($value['route']) }}"
                                class="nav-link {{ strpos($route_group, $value['route_group']) === 0 ? 'active' : '' }}"
                                style="font-size: .875rem !important;">
                                {{-- @if (!empty($value['class']))
                                    <i class="{{ $value['class'] }}">
                                    </i>
                                @endif --}}
                                {{ $key_val }}
                            </a>
                        </li>
                    @endcan
                @endforeach
            </ul>
        </div>
        <!-- hiện menu cấp 2 -->
    </li>
@endif
