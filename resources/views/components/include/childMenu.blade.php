@php
    $active = false;
    $show_nav_item = false;
@endphp
@foreach ($item['child_menu'] as $keyVal => $val)
    @if (!empty($val['child_menu']))
        @foreach ($val['child_menu'] as $key_val => $value)
            @php
                if (in_array($value['route_group'], $array_route_group)) {
                    $active = true;
                }
            @endphp
        @endforeach
    @endif

    @php
        if (in_array($val['route_group'], $array_route_group)) {
            $active = true;
        }
    @endphp

    @can(@$val['permission'])
        @php($show_nav_item = true)
    @endcan
@endforeach


@if ($show_nav_item)
    <li class="nav-item">
        <!-- menu cấp 1 -->
        <a class="nav-link {{ $active ? '' : 'collapsed' }}"
           href="#sidebarDashboards{{ $count }}"
           data-bs-toggle="collapse"
           aria-expanded="{{ $active ? 'true' : 'false' }}"
           aria-controls="sidebarDashboards{{ $count }}"
           style="font-size: .875rem !important; background-color: transparent !important;">
            <span>
                <i class="{{ $item['class'] }}"></i>
                {{ $k }}
            </span>
        </a>

        <!-- menu cấp 2 -->
        <div class="menu-dropdown collapse {{ $active ? 'show' : '' }}"
             id="sidebarDashboards{{ $count }}">
            <ul class="nav nav-sm flex-column">
                @foreach ($item['child_menu'] as $keyVal => $val)
                    @if (!empty($val['child_menu']))
                        <?php $count++; ?>
                        @include('components.include.childMenu1')
                    @else
                        @can(@$val['permission'], 'web')
                            <li class="nav-item">
                                <a href="{{ route($val['route']) }}"
                                   class="nav-link {{ strpos($route_group, $val['route_group']) === 0 ? 'active' : '' }}"
                                   style="font-size: .875rem !important;">
                                    @if (!empty($val['class']))
                                        <i class="{{ $val['class'] }}"></i>
                                    @endif
                                    {{ $keyVal }}
                                </a>
                            </li>
                        @endcan
                    @endif
                @endforeach
            </ul>
        </div>
    </li>
@endif
