<footer class="footer">
    <hr>
    <div class="container" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div class="footer__address">
            @if (!empty($config_web['link_logo']))
                <a href="{{ route('register_online.index') }}" class="logo"><img src="{{ $config_web['link_logo'] }}"
                        alt="" style="width:6em" /></a>
                <a href="{{ route('register_online.index') }}" class="title_logo"
                    style="font-size: 30px; margin-left: 5px;color: #e9bf72;">Heaven Gate O Quy Ho</a>
            @endif
        </div>
        <div class="footer__address">
            <h3 style="color: #8f2624;">Thông tin liên hệ:</h3>
            <ul>
                <li>
                    <div class="icon"><img src="{{ url('template/images/header-icon-phone.png') }}" alt="" />
                    </div>
                    <div class="txt">Hotline: <strong>{{ @$config_web['hotline'] }}</strong></div>
                </li>
                <li>
                    <div class="icon"><img src="{{ url('template/images/footer-icon-mail.png') }}" alt="" />
                    </div>
                    <div class="txt">
                        Email: <strong> <a href="">{{ @$config_web['email'] }}</a></strong>
                    </div>
                </li>
                <li>
                    <div class="icon"><img src="{{ url('template/images/footer-icon-address.png') }}"
                            alt="" /></div>
                    <div class="txt">Địa chỉ:<strong> {{ @$config_web['address'] }}</strong></div>
                </li>
                <li>
                    <div class="icon"><img src="{{ url('template/images/footer-icon-web.png') }}" alt="" />
                    </div>
                    <div class="txt">
                        Website: <strong><a href="{{ @$config_web['website'] }}"> {{ @$config_web['website'] }}</a></strong>
                    </div>
                </li>
            </ul>
        </div>

        <div class="footer__follow" align="center">
            <div style="width:100%;text-align: left;color: #8f2624;">
                <h3 style="color: #8f2624;">Theo dõi chúng tôi tại:</h3>
                <div class="socials d-flex align-items-center gap-3 flex-wrap">
                    @if (!empty($config_web['facebook']))
                        <a href="{{ $config_web['facebook'] }}">
                            <img src="{{ url('template/images/facebook_icon.jpg') }}" alt=""
                                style="width: 48px" />
                            {{-- <i class="ri-facebook-circle-fill" style="color: #8f2624;font-size: 50px"></i> --}}
                        </a>
                    @endif

                    @if (!empty($config_web['instagram']))
                        <a href="{{ $config_web['instagram'] }}">
                            <img src="{{ url('template/images/insta_icon.jpg') }}" alt=""
                                style="width: 45px" />
                            {{-- <i class="ri-instagram-fill" style="color: #8f2624;font-size: 50px"></i> --}}
                        </a>
                    @endif

                    @if (!empty($config_web['tiktok']))
                        <a href="{{ $config_web['tiktok'] }}">
                            <img src="{{ url('template/images/tiktok.png') }}" alt="" style="width: 42px" />
                        </a>
                    @endif

                    @if (!empty($config_web['zalo']))
                        <a href="{{ $config_web['zalo'] }}">
                            <img src="{{ url('template/images/zalo.png') }}" alt="" style="width: 45px" />
                        </a>
                    @endif
                </div>

                {{-- <h3>Chứng nhận</h3>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <img src="{{ url('template/images/img-bct.png') }}" alt="" />
                    <img src="{{ url('template/images/img-bct2.png') }}" alt="" />
                </div> --}}
            </div>
        </div>
        {{-- <div class="footer__payment">
            <h3 style="color: #8f2624;">Phương thức thanh toán</h3>
            <div class="payment">
                <div class="payment-item"><img width="85px" height="70px"
                        src="{{ url('template/images/momo_icon.png') }}" alt="" /></div>
                <div class="payment-item"><span>Thanh toán khi nhận hàng</span></div>
                <div class="payment-item"><span>Chuyển khoản</span></div>
            </div>
        </div> --}}
    </div>
</footer>
