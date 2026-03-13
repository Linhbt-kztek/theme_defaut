<div class="row">
    <h3>Danh sách quyền</h3>
    <?php $count = 0; ?>
    @foreach ($permission as $key => $value)
        <?php $count++; ?>

        <div class="col-2 card" style="margin:20px;background-color: #f7f7f7;">
            <div class="card-header" style="background-color: #f7f7f7;padding-left: 0px !important;margin-bottom: 10px;">
                <div class="form-check form-switch form-switch-success" dir="ltr">
                    <input type="checkbox" class="form-check-input" onclick="checkBoxAll('{{ $key . $count }}')"
                        id="idCheckAll{{ $key . $count }}">
                    <b style="font-weight: 500;float: right;font-size: 17px;">
                        @foreach (config('permission_name') as $keyMenu => $menus)
                            {{-- @dd($menus) --}}
                            @if ($key == $keyMenu)
                                {{ $menus }}
                            @endif
                        @endforeach
                    </b>
                </div>

            </div>
            <div class="card-body" style="padding: 0px !important;">
                @foreach ($value as $item)
                    <?php
                    $checked = false;
                    if (!empty($data->permissions)) {
                        # code...
                        foreach ($data->permissions as $val) {
                            # code...
                            if ($item->name == $val->name) {
                                # code...
                                $checked = true;
                            }
                        }
                    }
                    ?>

                    <div class="form-check form-switch" style="margin-bottom: 10px;">
                        <input type="checkbox" class="form-check-input my-checkbox {{ $key . $count }}"
                            value="{{ $item->id }}" name="permission[]" id="customSwitchsizesm{{ $item->id }}"
                            onclick="updateCheckAll('{{ $key . $count }}')" {{ $checked ? 'checked' : '' }}>
                        <label class="form-check-label"
                            for="customSwitchsizesm{{ $item->id }}">{{ $item->description }}</label>
                    </div>
                @endforeach
            </div>

        </div>
    @endforeach
</div>
