@php
    $active ?? ($active = null);
    $data = [
        1 => [
            'class' => 'ri-ticket-2-line',
            'text' => 'Chọn dịch vụ',
        ],
        2 => [
            'class' => 'ri-bank-card-line',
            'text' => 'Thanh toán',
        ],
        3 => [
            'class' => 'ri-check-double-line',
            'text' => 'Hoàn thành',
        ],
    ];
@endphp

<div class="div_policy">
    <section class="policy">
        @foreach ($data as $key => $item)
            <div class="policy__item {{ $key <= $active ? 'active' : '' }}">
                <div class="policy__image">
                    <i class="{{ $item['class'] }}" style="font-size: 20px"></i>
                </div>
                <div class="policy__data">
                    <span>{{ $item['text'] }}</span>
                </div>
            </div>
        @endforeach
    </section>
</div>
