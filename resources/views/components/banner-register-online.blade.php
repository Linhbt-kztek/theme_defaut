<div>
    @if (!empty($banner))
        <div class="ldform-banner">
            <a href="javascript:;" class="img">
                <img src="{{ $banner }}" alt="" class="img-fluid" style="width: 100%">
                {{-- <img src="{{ url('ladipage/images/banner_03.jpg') }}" alt="" class="img-fluid"> --}}
            </a>
        </div>
    @endif
    @if (!empty($config_web['home_content']))
        <div class="ldform-info">
            <div class="container">
                @if ($config_web['home_content']['title'])
                    <h2 class="title text-center title-color">
                        {{ $config_web['home_content']['title'] }}
                    </h2>
                @endif
                @if (!empty($config_web['home_content']['content']))
                    <div class="list-info">
                        <ul>
                            @foreach ($config_web['home_content']['content'] as $item)
                                <li style="margin-bottom: -10px">
                                    <p style="text-align: justify;">
                                        {!! $item !!}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
<style>
    .list-info ul {
        list-style: unset;
    }
</style>
