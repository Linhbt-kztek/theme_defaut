<div class="card">
    <div class="d-flex justify-content-between px-2"
        style="background-color: #fff;border-top-left-radius: 15px; border-top-right-radius: 15px;">
        <div class="flex-grow" style="margin-top: 16px; margin-bottom: 16px;">
            <h4 class="card-title mb-0 flex-grow-1">Thời gian: <b class="text-danger text_date"></b><br>
            </h4>
        </div>
        <div class="d-flex justify-content-end gap-3" style="width: 400px">
            <div class="" style="margin-top: 16px; margin-bottom: 16px; ">
                <div style="float: right" class="input-group">
                    <div class="input-group-text">Chọn tháng</div>
                    <input class="form-control" id="month_home" name="month" type="month" value="{{ date('Y-m') }}"
                        onchange="choseMonth()">
                </div>
            </div>
            <div class="" style="margin-top: 8px;">
                <div class="d-flex justify-content-end mt-2">
                    <input type="text" name="is_export" hidden="" value="1">
                    <button type="button"
                        class="btn btn-success d-flex justify-content-end align-items-center text-nowrap"
                        style="border-radius: 10px;" onclick="handleExport()">
                        <i class="mdi mdi-microsoft-excel px-1"></i>
                        Xuất Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="w-100">
        <div class="row p-0 m-0" style="background-color: #fff;">
            <div class="col-xl-9">
                <div class="d-flex gap-4">
                    <div class="" style="width: 270px">
                        <div class="d-flex flex-column gap-2">
                            <div class="revenue-sum-card">
                                <div class="revenue-title">Tổng doanh thu</div>

                                <div class="revenue-value">
                                    <span id="total_revenue">0</span> đ
                                </div>

                                <div class="revenue-compare">
                                    <span id="revenue_trend" class="trend-up">
                                        <i class="mdi mdi-arrow-top-right-thin"></i> 0%
                                    </span>
                                    <span class="trend-note">so với tháng trước</span>
                                </div>
                            </div>

                            <div class="revenue-sum-card">
                                <div class="revenue-title">Hoàn tiền</div>

                                <div class="revenue-value">
                                    <span id="total_refund">0</span><span> đ</span>
                                </div>
                            </div>

                            <div class="revenue-sum-card">
                                <div class="revenue-title">Số hóa đơn</div>
                                <div class="revenue-value">
                                    <span id="total_order_count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="card card-height-100">

                            <div class="card-body">
                                <div class="court-summary-item" style="font-weight: 600; color: #6c757d;">
                                    <div class="">Sân</div>
                                    <div class="text-end">Doanh thu</div>

                                    <div class="text-end">Thời gian sử dụng TB</div>
                                    <div class="text-end">Tỷ lệ tăng trưởng</div>
                                </div>


                                <div class="w-100" id="total_use_time">
                                    <!-- Danh sách doanh thi theo sân sử dụng render tại đây -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-header align-items-center d-flex" style="background-color: #46b6c8;">
                        <h4 class="card-title mb-0 flex-grow-1" style="color: #fff">Thống kê lượt sử dụng</h4>
                    </div>

                    <div class="card-body">
                        <div id="store-visits-source"
                            data-colors='["--vz-danger","--vz-primary", "--vz-success",  "--vz-info", "--vz-warning","--vz-secondary"]'
                            class="apex-charts" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="w-100">
        <div class="row p-0 m-0" style="background-color: #fff;">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body p-0 pb-2 border-top">
                        <div class="w-100">
                            <div id="customer_impression_charts"
                                data-colors='["--vz-success", "--vz-primary", "--vz-danger"]' class="apex-charts"
                                dir="ltr">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="court-summary-template">
    <div class="court-summary-item">
        <div class="court-name"></div>

        <div class="court-money"></div>
        <div class="court-avg-duration"></div>

        <div class="court-compare">
            <span class="court-percent"></span>
        </div>
    </div>
</template>


<template id="room-template">
    <div class="p-3 w-100 room-label">
        <p class=" mb-0 text-center"></p>
        <h5 class="mb-1 text-center fw-semibold">
            <span class="counter-value" data-target="">0</span>
        </h5>


    </div>
</template>

<style>
    .apexcharts-legend-text {
        font-family: 'Roboto', sans-serif !important;
    }
</style>
<script src="{{ url('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ url('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ url('assets/libs/swiper/swiper.min.js') }}"></script>
<!-- dashboard init -->
<script src="{{ url('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

<link href="{{ url('assets/css/chart.css') }}" rel="stylesheet">

<script>
    $(document).ready(() => {
        choseMonth();
        // pie_chart()
    });

    flatpickr('.month_home', {
        enableTime: false,
        dateFormat: 'm-Y',
        locale: 'vn',
    })

    let home = {
        validateDate: function () {
            var dateInput = document.getElementById('month_home').value;

            // Kiểm tra định dạng yyyy-mm bằng biểu thức chính quy
            const dateFormat = /^\d{4}-\d{2}$/;
            if (!dateFormat.test(dateInput)) {
                // validationResult.textContent = 'Định dạng ngày tháng không hợp lệ. Hãy nhập theo định dạng yyyy-mm.';
                console.log('Định dạng ngày tháng không hợp lệ. Hãy nhập theo định dạng yyyy-mm.');
                return {
                    'status': false
                };
            }

            const [year, month] = dateInput.split('-');

            // Kiểm tra tính hợp lệ của năm và tháng
            if (isNaN(year) || isNaN(month) || year < 1900 || year > 2099 || month < 1 || month > 12) {
                console.log('Năm hoặc tháng không hợp lệ.');
                return {
                    'status': false
                };
            }

            var data_return = {
                'status': true,
                'time': dateInput
            };

            return data_return;
        },
        formattedMonth: function (dateStr) {
            var year = dateStr.substring(0, 4);
            var month = parseInt(dateStr.substring(5, 7));

            // Mảng tên các tháng trong năm
            var monthNames = [
                "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
                "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
            ];

            // Định dạng lại theo yêu cầu
            var formattedDate = monthNames[month - 1] + " năm " + year;
            return formattedDate;
        }
    }

    function choseMonth() {
        var data_affter_validate = home.validateDate()
        if (data_affter_validate.status) {
            var month = data_affter_validate.time;
            $('.text_date').text(home.formattedMonth(month));

            $.ajax({
                url: "{{ route('report.getDataForChart') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    month: month
                },
                success: function (result) {
                    line_graph(result);
                    pie_chart(result);

                    renderRevenueSum(result);
                    renderCourtSummary(result);
                    renderExtraStats(result);
                }

            });
        }
    }


    function handleExport() {
        var data_affter_validate = home.validateDate();
        if (data_affter_validate.status) {
            var month = data_affter_validate.time;
            $('.text_date').text(home.formattedMonth(month));

            var form = $('<form>', {
                action: "{{ route('revenue_report.exportExcel') }}",
                method: 'POST'
            }).append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            })).append($('<input>', {
                type: 'hidden',
                name: 'month',
                value: month
            })).append($('<input>', {
                type: 'hidden',
                name: 'is_export',
                value: 1
            }));

            $('body').append(form);
            form.submit();
            form.remove();
        }
    }

    let pieChart = null;

    function pie_chart(data) {
        const storeVisitsDiv = document.getElementById('store-visits-source');

        // destroy 
        if (pieChart) {
            pieChart.destroy();
            pieChart = null;
        }

        // clear DOM
        storeVisitsDiv.innerHTML = '';

        let series = Array.isArray(data?.pie_chart?.count)
            ? data.pie_chart.count.map(Number)
            : [];

        let labels = Array.isArray(data?.pie_chart?.key)
            ? data.pie_chart.key
            : [];

        const total = series.reduce((a, b) => a + b, 0);

        let colors = [];
        let showLegend = true;
        let isEmpty = false;

        // KHÔNG CÓ DATA
        if (!series.length || total === 0) {
            series = [1];
            labels = ['Không có dữ liệu'];
            colors = ['#d1d5db'];
            showLegend = false;
            isEmpty = true;
        } else {
            colors = generateThemeColors(themeColors, series.length);
        }

        const options = {
            chart: {
                height: 333,
                type: 'donut'
            },
            series,
            labels,
            legend: {
                position: 'bottom',
                show: showLegend
            },
            stroke: { show: false },
            dataLabels: { enabled: true },
            tooltip: {
                enabled: !isEmpty,
                y: {
                    formatter: val => val
                }
            },
            colors
        };

        pieChart = new ApexCharts(storeVisitsDiv, options);
        pieChart.render();
    }


    let lineChart = null;

    function line_graph(data) {
        const chartDiv = document.getElementById('customer_impression_charts');
        chartDiv.innerHTML = '';

        const courts = Array.isArray(data.courts) ? data.courts : [];

        let days = [];

        if (courts.length && courts[0].daily_amount) {
            days = Object.keys(courts[0].daily_amount);
        } else {
            // không có data => tạo 31 ngày mặc định
            days = Array.from({ length: 31 }, (_, i) =>
                String(i + 1).padStart(2, '0')
            );
        }

        const hasData = courts.some(court =>
            Object.values(court.daily_amount || {}).some(v => v > 0)
        );

        let series = [];
        let colors = [];

        if (!courts.length || !hasData) {
            // RESET LINE
            series = [{
                name: 'Doanh thu',
                data: days.map(() => 0)
            }];
            colors = ['#d0d0d0'];
        } else {
            series = courts.map(court => ({
                name: court.court_name,
                data: days.map(d => court.daily_amount[d] ?? 0)
            }));

            colors = courts.map((_, index) =>
                themeColors[index % themeColors.length]
            );
        }

        const options = {
            chart: {
                type: 'line',
                height: 400,
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            series,
            colors,
            xaxis: {
                categories: days.map(d => d.split('-')?.[2] || d),
                title: { text: 'Ngày' }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return val.toLocaleString('vi-VN');
                    }
                },
                title: {
                    text: 'Doanh thu (VND)'
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                hover: { size: 6 }
            },
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                y: {
                    formatter: val =>
                        val.toLocaleString('vi-VN') + ' ₫'
                }
            }
        };
        // destroy chart 
        if (lineChart) {
            lineChart.destroy();
        }

        lineChart = new ApexCharts(chartDiv, options);
        lineChart.render();
    }

    var themeColors = [
        '#00cfc1', // cyan bright
        '#9be36d', // green neon
        '#005b96', // deep blue
        '#00a8a8', // teal strong
        '#003f44', // dark teal
        '#ff6f00', // orange vivid
        '#ff3d00', // red orange
        '#c62828', // red deep
        '#ffca28',  // yellow bright


        '#5e2b97', // purple deep
        '#b39ddb', // lavender bright
        '#d81b60', // pink strong
        '#8e004d', // magenta dark


    ];



    function generateThemeColors(baseColors, totalColors) {
        const result = [];

        baseColors.forEach(base => {
            result.push(base);                    // original
            result.push(adjustColor(base, -90));  // darker
        });

        return result.slice(0, totalColors);
    }

    function renderRevenueSum(data) {
        const totalEl = document.getElementById('total_revenue');
        const trendEl = document.getElementById('revenue_trend');

        if (!data || !totalEl || !trendEl) return;

        const total = Number(data.total_revenue || 0);
        const percent = Number(data.revenue_trend || 0);

        totalEl.innerText = total.toLocaleString('vi-VN');

        if (percent >= 0) {
            trendEl.className = 'trend-up';
            trendEl.innerHTML = `<i class="mdi mdi-arrow-top-right-thin"></i> ${percent}%`;
        } else {
            trendEl.className = 'trend-down';
            trendEl.innerHTML = `<i class=" mdi mdi-arrow-bottom-right-thin"></i> ${Math.abs(percent)}%`;
        }
    }
    function renderCourtSummary(data) {
        const container = document.getElementById('total_use_time');
        const tpl = document.getElementById('court-summary-template');

        container.innerHTML = '';

        if (!data.court_summary || !data.court_summary.length) {
            container.innerHTML = '<p class="text-center p-3">Không có dữ liệu</p>';
            return;
        }

        data.court_summary.forEach(court => {
            const node = tpl.content.cloneNode(true);

            node.querySelector('.court-name').innerText = court.court_name;
            node.querySelector('.court-avg-duration').innerText = (court.avg_duration).toFixed(2) + ' giờ';
            node.querySelector('.court-money').innerText = court.total_amount.toLocaleString('vi-VN') + ' ₫';

            const percentEl = node.querySelector('.court-percent');

            if (court.percent_change >= 0) {
                percentEl.classList.add('up');
                percentEl.innerHTML = `<i class="mdi mdi-arrow-top-right-thin"></i> ${court.percent_change}%`;
            } else {
                percentEl.classList.add('down');
                percentEl.innerHTML = `<i class="mdi mdi-arrow-bottom-right-thin"></i> ${Math.abs(court.percent_change)}%`;
            }

            container.appendChild(node);
        });
    }
    function renderExtraStats(data) {
        $('#total_refund').text(Number(data.refund || 0).toLocaleString('vi-VN'));
        $('#total_order_count').text(Number(data.order_count || 0).toLocaleString('vi-VN'));
        $('#total_revenue').text(Number(data.revenue || 0).toLocaleString('vi-VN'));
    }
</script>