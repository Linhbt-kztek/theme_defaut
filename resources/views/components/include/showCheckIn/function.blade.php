<hr>
@php
    $auth_user = auth()->user();
    $staff_auth = auth()->user()->staff->id ?? '';
    $checkin_on_website = $auth_user->can('checkin_on_website');
@endphp
@if ($staff->id == $staff_auth)
    <div class="row g-0">
        <div class="col-4">
            <div class="dropdown-icon-item-class">
                <a class="dropdown-icon-item" href="#" onclick="showCreateModal()">
                    <svg width="100%" height="100%" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg"
                        fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#ff7900"
                            stroke-width="0.5">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: #ff7900;
                                    }
                                </style>
                            </defs>
                            <g id="schedule">
                                <path class="cls-1"
                                    d="M22.5,3H21V2a1,1,0,0,0-1-1H19a1,1,0,0,0-1,1V3H14V2a1,1,0,0,0-1-1H12a1,1,0,0,0-1,1V3H7V2A1,1,0,0,0,6,1H5A1,1,0,0,0,4,2V3H2.5A1.5,1.5,0,0,0,1,4.5v18A1.5,1.5,0,0,0,2.5,24h16A5.51,5.51,0,0,0,24,18.5s0-.08,0-.13,0,0,0,0V4.5A1.5,1.5,0,0,0,22.5,3ZM19,2l1,0,0,3L19,5ZM12,2l1,0V3.44s0,0,0,.06,0,0,0,.07L13,5,12,5ZM5,2,6,2,6,5,5,5ZM2.5,4H4V5A1,1,0,0,0,5,6H6A1,1,0,0,0,7,5V4h4V5a1,1,0,0,0,1,1H13a1,1,0,0,0,1-1V4h4V5a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V4h1.5a.5.5,0,0,1,.5.5V8H2V4.5A.5.5,0,0,1,2.5,4Zm16,19A4.5,4.5,0,1,1,23,18.5,4.51,4.51,0,0,1,18.5,23Zm0-10a5.49,5.49,0,0,0-3.15,10H2.5a.5.5,0,0,1-.5-.5V9H23v6.35A5.49,5.49,0,0,0,18.5,13Z">
                                </path>
                                <path class="cls-1"
                                    d="M20.72,19.05,19,18.19V16.5a.5.5,0,0,0-1,0v2a.51.51,0,0,0,.28.45l2,1a.54.54,0,0,0,.22.05.5.5,0,0,0,.22-.95Z">
                                </path>
                            </g>
                        </g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: #ff7900;
                                    }
                                </style>
                            </defs>
                            <g id="schedule">
                                <path class="cls-1"
                                    d="M22.5,3H21V2a1,1,0,0,0-1-1H19a1,1,0,0,0-1,1V3H14V2a1,1,0,0,0-1-1H12a1,1,0,0,0-1,1V3H7V2A1,1,0,0,0,6,1H5A1,1,0,0,0,4,2V3H2.5A1.5,1.5,0,0,0,1,4.5v18A1.5,1.5,0,0,0,2.5,24h16A5.51,5.51,0,0,0,24,18.5s0-.08,0-.13,0,0,0,0V4.5A1.5,1.5,0,0,0,22.5,3ZM19,2l1,0,0,3L19,5ZM12,2l1,0V3.44s0,0,0,.06,0,0,0,.07L13,5,12,5ZM5,2,6,2,6,5,5,5ZM2.5,4H4V5A1,1,0,0,0,5,6H6A1,1,0,0,0,7,5V4h4V5a1,1,0,0,0,1,1H13a1,1,0,0,0,1-1V4h4V5a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V4h1.5a.5.5,0,0,1,.5.5V8H2V4.5A.5.5,0,0,1,2.5,4Zm16,19A4.5,4.5,0,1,1,23,18.5,4.51,4.51,0,0,1,18.5,23Zm0-10a5.49,5.49,0,0,0-3.15,10H2.5a.5.5,0,0,1-.5-.5V9H23v6.35A5.49,5.49,0,0,0,18.5,13Z">
                                </path>
                                <path class="cls-1"
                                    d="M20.72,19.05,19,18.19V16.5a.5.5,0,0,0-1,0v2a.51.51,0,0,0,.28.45l2,1a.54.54,0,0,0,.22.05.5.5,0,0,0,.22-.95Z">
                                </path>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>
            <center> <span>Xin nghỉ</span></center>
        </div>

        @if ($checkin_on_website)
            <div class="col-4">
                <div class="dropdown-icon-item-class">
                    <a class="dropdown-icon-item" href="#" onclick="getLocationAndCheckIn()">
                        <svg width="100%" height="100%" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg"
                            fill="#000000">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"
                                stroke="#ff7900" stroke-width="0.5">
                                <defs>
                                    <style>
                                        .cls-1 {
                                            fill: #ff7900;
                                        }
                                    </style>
                                </defs>
                                <g data-name="calendar check" id="calendar_check">
                                    <path class="cls-1"
                                        d="M22.5,3H21V2a1,1,0,0,0-1-1H19a1,1,0,0,0-1,1V3H14V2a1,1,0,0,0-1-1H12a1,1,0,0,0-1,1V3H7V2A1,1,0,0,0,6,1H5A1,1,0,0,0,4,2V3H2.5A1.5,1.5,0,0,0,1,4.5v18A1.5,1.5,0,0,0,2.5,24h20A1.5,1.5,0,0,0,24,22.5V4.5A1.5,1.5,0,0,0,22.5,3ZM19,2l1,0,0,3L19,5ZM12,2l1,0V3.44s0,0,0,.06,0,0,0,.07L13,5,12,5ZM5,2,6,2,6,5,5,5ZM2.5,4H4V5A1,1,0,0,0,5,6H6A1,1,0,0,0,7,5V4h4V5a1,1,0,0,0,1,1H13a1,1,0,0,0,1-1V4h4V5a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V4h1.5a.5.5,0,0,1,.5.5V8H2V4.5A.5.5,0,0,1,2.5,4Zm20,19H2.5a.5.5,0,0,1-.5-.5V9H23V22.5A.5.5,0,0,1,22.5,23Z">
                                    </path>
                                    <path class="cls-1"
                                        d="M16.75,12.07a.49.49,0,0,0-.68.18l-3.68,6.43L8.85,15.15a.49.49,0,0,0-.7.7l4,4a.47.47,0,0,0,.35.15h.07a.5.5,0,0,0,.36-.25l4-7A.49.49,0,0,0,16.75,12.07Z">
                                    </path>
                                </g>
                            </g>
                            <g id="SVGRepo_iconCarrier">
                                <defs>
                                    <style>
                                        .cls-1 {
                                            fill: #ff7900;
                                        }
                                    </style>
                                </defs>
                                <g data-name="calendar check" id="calendar_check">
                                    <path class="cls-1"
                                        d="M22.5,3H21V2a1,1,0,0,0-1-1H19a1,1,0,0,0-1,1V3H14V2a1,1,0,0,0-1-1H12a1,1,0,0,0-1,1V3H7V2A1,1,0,0,0,6,1H5A1,1,0,0,0,4,2V3H2.5A1.5,1.5,0,0,0,1,4.5v18A1.5,1.5,0,0,0,2.5,24h20A1.5,1.5,0,0,0,24,22.5V4.5A1.5,1.5,0,0,0,22.5,3ZM19,2l1,0,0,3L19,5ZM12,2l1,0V3.44s0,0,0,.06,0,0,0,.07L13,5,12,5ZM5,2,6,2,6,5,5,5ZM2.5,4H4V5A1,1,0,0,0,5,6H6A1,1,0,0,0,7,5V4h4V5a1,1,0,0,0,1,1H13a1,1,0,0,0,1-1V4h4V5a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V4h1.5a.5.5,0,0,1,.5.5V8H2V4.5A.5.5,0,0,1,2.5,4Zm20,19H2.5a.5.5,0,0,1-.5-.5V9H23V22.5A.5.5,0,0,1,22.5,23Z">
                                    </path>
                                    <path class="cls-1"
                                        d="M16.75,12.07a.49.49,0,0,0-.68.18l-3.68,6.43L8.85,15.15a.49.49,0,0,0-.7.7l4,4a.47.47,0,0,0,.35.15h.07a.5.5,0,0,0,.36-.25l4-7A.49.49,0,0,0,16.75,12.07Z">
                                    </path>
                                </g>
                            </g>
                        </svg>
                    </a>
                </div>
                <center> <span>Chấm công</span></center>
            </div>
        @endif
    </div>
@endif
<style>
    .dropdown-icon-item {
        border-radius: 10px;
        background-color: rgb(255, 255, 255);
        margin-left: 10%;
        margin-right: 10%;
        margin-bottom: 5%;
        padding: 8px;
    }

    .dropdown-icon-item-class :hover {
        background-color: rgb(247, 247, 247);
    }
</style>
