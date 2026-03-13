<!DOCTYPE html>
<html>

<head>
    <style>
        body {

            /* background-image: url("https://img.freepik.com/free-vector/realistic-colorful-galaxy-background_23-2148965681.jpg"); */
            /* background-image: url("{{ url('ladipage/images/94179.jpg') }}"); */
            background-size: 100% auto;
            background-repeat: repeat;
            background-position: center center;
        }

        .card {
            background-color: #fff;
            width: 90%;
            border-radius: 15px;
            padding: 1%;
            text-align: left;
            /* position: absolute; */
            /* top: 50%; */
            /* left: 50%; */
            /* right: 50%; */
            /* transform: translate(50%, 50%); */
        }

        .center_class {
            margin-top: 4%;
            margin-bottom: 5px;
        }

        .card-body {
            display: flex;
            flex-wrap: wrap;
        }

        .col-3 {
            width: 35%;
            box-sizing: border-box;
        }

        .col-4 {
            width: 25%;
            box-sizing: border-box;
        }

        .col-6 {
            width: 50%;
            box-sizing: border-box;
        }

        .col-7 {
            width: 65%;
            box-sizing: border-box;
            margin-left: 5px;
        }

        .col-8 {
            width: 75%;
            box-sizing: border-box;
        }

        .col-12 {
            /* width: 100%; */
            /* box-sizing: border-box; */
        }


        .card-item {
            /* background-color: #ffe8a6; */
            background-image: url("https://img.freepik.com/free-vector/realistic-colorful-galaxy-background_23-2148965681.jpg");
            background-repeat: repeat;
            background-position: center center;
            background-size: 100% auto;

            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
        }

        th {
            font-size: 10px;
            color: #fff;
        }

        .center {
            text-align: center;
            color: #fff;
        }
    </style>
</head>

<body>

    <center class="center_class">
        <div class="card">
            <h3 class="title">Thông tin thanh toán</h3>
            <hr>
            <div class="card-body">
                <table class="table" style="text-align: left; width: 100%">
                    <thead>
                        <td>Mã hóa đơn: <b style="color: red; float: right;">{{ $order->code_order }}</b>
                        </td>
                    </thead>
                    <thead>
                        <td>
                            Người mua vé: <b style="float: right;">{{ $customer->name }}</b></th>
                    </thead>
                    <thead>
                        <td>
                            Số điện thoại: <b style="float: right;">{{ $customer->phone }}</b></th>
                    </thead>
                    <thead>
                        <td>
                            Số lượng vé: <b style="float: right;">{{ count($ticket) }} vé</b></th>
                    </thead>
                    <thead>
                        <td>Tổng tiền:
                            <b style="float: right;">{{ number_format($order->real_amount, 0, '.', '.') . 'đ' }}</b>
                        </td>
                    </thead>
                    <thead style="color: blue">
                        <td>Phương thức thanh toán:

                            <b style="float: right;">Thanh toán online</b>
                        </td>
                    </thead>
                    <thead>
                        <td style="color: green">Trạng thái:
                            <b style="float: right;">Đã thanh toán!</b></th>
                    </thead>
                </table>
            </div>
        </div>
    </center>
    <center class="center_class">
        <div class="card">
            <h3 class="title">Thông tin vé mua</h3>
            <hr>
            <div>
                @foreach ($ticket as $item)
                    <div class="card-body card-item">
                        <div class="col-3">

                            {{-- <img class="qr_code"
                                src="https://media.istockphoto.com/id/828088276/vector/qr-code-illustration.jpg?s=612x612&w=0&k=20&c=FnA7agr57XpFi081ZT5sEmxhLytMBlK4vzdQxt8A70M="
                                width="100%" alt="{{ @$item->code }}" name_ticket= "{{ $item->ticket_type_name }}"> --}}
                            <img class="qr_code" src="{{ url($item->qr_code) }}" width="100%"
                                alt="{{ @$item->code }}" name_ticket= "{{ $item->ticket_type_name }}">
                        </div>
                        <div class="col-7">
                            <div class="center">
                                <b style="font-size: 10px;">KVC Ô Quy Hồ</b><br>
                                <b style="font-size: 10px;">Hostline: 0967 359 329 - 0213 730 6688</b><br>
                                <b style="font-size: 15px;">{{ $item->ticket_type_name }}</b>
                            </div>
                            <table class="table" style="text-align: left;width: 100%;">
                                <thead>
                                    <th>
                                        <b>
                                            Mã vé
                                        </b>
                                        <b style="float: right">
                                            {{ @$item->code }}
                                        </b>
                                    </th>
                                </thead>
                                <thead>
                                    <th><b>
                                            Giá vé
                                        </b>
                                        <b style="float: right">
                                            {{ number_format($item->price, 0, '.', '.') . 'đ' }}
                                        </b>
                                    </th>
                                </thead>

                                <thead>
                                    <th>
                                        @if (!empty($item->use_date))
                                            Ngày tham quan: <b
                                                style="float: right">{{ \Carbon\Carbon::parse($item->use_date)->format('d-m-Y') }}</b>
                                        @else
                                            Hạn sử dụng: <b
                                                style="float: right">{{ $item->expire_date->format('d-m-Y') }}</b>
                                        @endif
                                    </th>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </center>
</body>

</html>
