@if (!empty($breadcrumb) || !empty($create_btn))
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                @if (!empty($breadcrumb))
                    @foreach ($breadcrumb as $key => $val)
                        <li class="breadcrumb-item"><a
                                href="{{ !empty($val['route']) ? ($val['route'] == 'category.index' ? route($val['route'], ['type' => $type]) : route($val['route'])) : '#' }}">
                                <b>{{ $val['title'] }}</b>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ol>
        </div>
        <div class="page-title-right">
            <!-- btn create -->
            @if (!empty($create_btn))
                <!-- neu là mảng -->
                @if (!empty($create_btn[0]))
                    @foreach ($create_btn as $item)
                        <div style="float: right;margin-right: 5px;">
                            <a id="{{ !empty($item['id']) ? $item['id'] : '' }}"
                                class="btn {{ !empty($item['btn-sm']) ? 'btn-sm' : '' }} btn-{{ $item['class'] ?? 'primary' }}"
                                {{ $item['type']
                                    ? 'href=' .
                                        ($item['link'] ??
                                            route($item['route']) .
                                                (!empty($item['query_params']) ? '?' . http_build_query($item['query_params']) : ''))
                                    : 'onclick=' . $item['function'] }}>
                                <i
                                    class="{{ $item['icon'] ?? 'ri-add-line' }} align-bottom me-1"></i>{{ $item['name'] ?? 'Thêm mới' }}
                            </a>
                        </div>
                    @endforeach
                @else
                    <!-- chỉ có 1 phan tu -->
                    <div style="float: right">
                        <a id="{{ !empty($item['id']) ? $item['id'] : '' }}"
                            class="btn {{ !empty($create_btn['btn-sm']) ? 'btn-sm' : '' }} btn-{{ $create_btn['class'] ?? 'primary' }}"
                            {{ $create_btn['type'] ? 'href=' . ($create_btn['link'] ?? route($create_btn['route'])) : 'onclick=' . $create_btn['function'] }}>
                            <i
                                class="{{ $create_btn['icon'] ?? 'ri-add-line' }} align-bottom me-1"></i>{{ $create_btn['name'] ?? 'Thêm mới' }}
                        </a>
                    </div>
                @endif
            @endif
            <!-- btn create -->

            <!-- btn excel -->
            @if (!empty($create_excel_btn))
                <div style="float: right;margin-right:5px">
                    <form class="float-end" id="excel-form" action="{{ route($create_excel_btn['route']) }}"
                        method="post" style="float: right"
                        target="{{ !empty($create_excel_btn['target']) ? $create_excel_btn['target'] : '_blank' }}">
                        @csrf
                        <input hidden="hidden" name="is_export" value="1">
                        @if (!empty($create_excel_btn['input_hidden']))
                            @foreach ($create_excel_btn['input_hidden'] as $item)
                                <input type="hidden" name="{{ $item['name'] }}" id="{{ $item['id'] }}"
                                    value="{{ $item['value'] }}">
                            @endforeach
                        @endif
                        <button
                            class="btn {{ !empty($create_excel_btn['btn-sm']) ? 'btn-sm' : '' }} btn-{{ $create_excel_btn['class'] ?? 'primary' }}"
                            id="excel-btn">
                            <i class="{{ $create_btn['icon'] ?? 'ri-add-line' }} align-bottom me-1"></i>Excel
                            {{--                                <i class="{{ $create_excel_btn['icon'] ?? 'ri-file-excel-line' }}"></i> --}}
                        </button>
                    </form>
                </div>
            @endif
            <!-- btn excel -->


        </div>
    </div>
@endif
<style>
    .breadcrumb-item {
        font-size: 14px;
    }

    .breadcrumb-item .breadcrumb-item:before {
        font-size: 20px;
    }
</style>
