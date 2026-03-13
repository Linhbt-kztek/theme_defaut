<div class="card">
    <form id="form-setting" action="{{ route('config.update') }}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="card-header title_content">
            <b class="text-center">Cấu hình Website</b>
        </div>
        <input type="hidden" name="id" value="{{ $id }}" />
        <div class="card-body border border-dashed border-start-0 border-end-0 row mb-3">
            <div class="col-6">
                <div class="row">
                    <div class="col-12">
                        <div class="form_input">
                            <label class="form-label">Tiêu đề trang:</label>
                            <input class="form-control" type="text" name="title_web" value="{{ @$data['title_web'] }}">
                            @if ($errors->has('title_web'))
                                <div class="bg-danger text-white text-center py-1">
                                    <span>{{ $errors->first('title_web') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-5">
                        <div class="form_input">
                            <label class="form-label">Tiêu đề trang:</label>
                            <input class="form-control" type="text" name="title_login"
                                value="{{ @$data['title_login'] }}">
                            @if ($errors->has('title_login'))
                                <div class="bg-danger text-white text-center py-1">
                                    <span>{{ $errors->first('title_login') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="col-7">
                        <div class="form_input">
                            <label class="form-label">Mô tả login</label>
                            <input class="form-control" type="text" name="description_login"
                                value="{{ @$data['description_login'] }}">
                            @if ($errors->has('description_login'))
                                <div class="bg-danger text-white text-center py-1">
                                    <span>{{ $errors->first('description_login') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- <div class="col-12">
                        <div class="form_input">
                            <label class="form-label">Tiêu đề chân trang:</label>
                            <input class="form-control" type="text" name="title_footer"
                                value="{{ @$data['title_footer'] }}">
                            @if ($errors->has('title_footer'))
                            <div class="bg-danger text-white text-center py-1">
                                <span>{{ $errors->first('title_footer') }}</span>
                            </div>
                            @endif
                        </div>
                    </div> --}}
                    <div class="col-12">
                        <label class="form-label">Banner login</label>
                        <input class="form-control" style="width: 50%" type="file" name="banner_login">
                        @if (!empty($data['banner_login']))
                            <div class="form_input" style="margin-top: 15px">
                                <img src="{{ $data['banner_login'] }}" style="max-width: 100%" alt="" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <div class="form_input">
                            <label class="form-label">Logo hệ thống</label>
                            <input class="form-control" type="file" name="logo_system">
                            @if ($errors->has('logo_system'))
                                <div class="bg-danger text-white text-center py-1">
                                    <span>{{ $errors->first('logo_system') }}</span>
                                </div>
                            @endif
                        </div>
                        <div style="margin-top: 15px">
                            @if (!empty($data['logo_system']))
                                <div class="form_input">
                                    <img src="{{ $data['logo_system'] }}" alt=""
                                        style="width:50%; background-color: black" />
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form_input">
                            <label class="form-label">Logo trang login</label>
                            <input class="form-control" type="file" name="logo_login">
                            @if ($errors->has('logo_login'))
                                <div class="bg-danger text-white text-center py-1">
                                    <span>{{ $errors->first('logo_login') }}</span>
                                </div>
                            @endif
                        </div>
                        <div style="margin-top: 15px">
                            @if (!empty($data['logo_login']))
                                <div class="form_input">
                                    <img style="width:50%; background-color: black" src="{{ $data['logo_login'] }}"
                                        alt="" />
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" type="button" onclick="$('#form-setting').submit()">
                Cập nhật
            </button>
        </div>
    </form>
</div>
<style>
    input[type='file'] {
        position: unset !important;
        opacity: 1 !important;
        width: auto !important;
        height: auto !important;
    }
</style>