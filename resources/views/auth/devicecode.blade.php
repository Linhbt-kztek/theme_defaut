@extends('layouts.auth')
@section('content')
<div class="container">
    <div class="row" id="login">
        <div class="col-md-4 col-md-offset-4">
            <form role="form" action="{{route('check_active_post') }}" method="POST" class='form-horizontal'>
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="login-panel panel panel-primary">

                    <div class="panel-body panel-body-kz">
                        <div class="logo">
                            <img src="/images/kztek_logo_web.png" />
                        </div>

                        @if($error_active_code != "")
                        <div class='alert alert-danger'>
                            <p>{{$error_active_code}}</p>
                        </div>
                        <br />
                        @endif

                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <fieldset>
                            <div class="form-group">
                                <div class='col-md-12 col-sm-12'>
                                    <div class="input-group" style="display: block">
                                        <label style="color:lightseagreen;font-size:20px"><b>Device code</b></label>
                                        <div >
                                            <textarea rows="6" readonly id="device_code" name="device_code" style="width:100%" class="form-control">{{$device_code}}</textarea>
                                        </div>
                                        <div style="text-align:center" >
                                               <input type="button" value="Copy" class="btn btn-info" style="background-color: #20B2AA" onclick="myFunction3()"/>
                                        </div>

                                        <label style="color: red;font-size:20px"><b>Active code</b></label>
                                        <textarea rows="6" id="active_code" name="active_code" style="width:100%" class="form-control"></textarea>
                                    </div>

                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-lg btn-primary btn-block btn-kz">Kích hoạt</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function myFunction3() {
        // Get the text field
        var copyText = document.getElementById("device_code");

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field

        navigator.clipboard.writeText(copyText.value)
    }
</script>
@stop
