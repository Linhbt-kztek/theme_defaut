<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    //=============================================START FUNCTION=====================================================

    /* giải mã aes */
    private function decrypt_kztek($string)
    {
        $method = 'aes-256-cbc';
        $key = "uwar4ZkuazsQlIRzxw4kEwZtUegDUDZw";

        // IV must be exact 16 chars (128 bit)
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        $decrypted = openssl_decrypt(base64_decode($string), $method, $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }


    /* mã hóa aes */
    private function encrypt_kztek($string)
    {
        $method = 'aes-256-cbc';
        $key = "uwar4ZkuazsQlIRzxw4kEwZtUegDUDZw";

        // IV must be exact 16 chars (128 bit)
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        $encrypted = base64_encode(openssl_encrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv));

        return $encrypted;
    }

    /* mã hóa ko giải mã được */
    private function hash_kztek($text)
    {
        $text = mb_convert_encoding($text, 'UTF-16LE');
        $encrypted = hash("sha512", $text, true);
        return base64_encode($encrypted);
    }

    //===============================START FUNCTION=====================================================

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        AwsS3V3Adapter::macro('getClient', fn() => $this->client);

        //ghi log sql theo thời gian theo ngày-thang-năm----------------------------
        
        // DB::listen(function ($query) {

        //     $year = Carbon::now()->year;
        //     $month = Carbon::now()->month;
        //     $day = Carbon::now()->day;
        //     $pathStr = 'logs/' . 'nam_' . $year . '/' . 'thang_' . $month;
        //     $path = storage_path($pathStr);
        //     if (!File::isDirectory($path)) {
        //         File::makeDirectory($path, $mode = 0777, true, true);
        //     }
        //     File::append(
        //         storage_path($pathStr . '/' . $day . '_query.log'),
        //         '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
        //     );

        // });


        date_default_timezone_set('Asia/Ho_Chi_Minh');

        Paginator::useBootstrap();
        // Blade::component('ticket-tr-table', ticketTrTable::class);
        Schema::defaultStringLength(191);

        //nếu là máy của server thì ko lấy dc mainboard serialt , nên phải lấy rombios để thay vào địa chỉ mac nhé
        $mac_address = "";
        //=======================END CẤU HÌNH====================================

        $interal_key = $this->hash_kztek(config('software.interal_key'));

        //===========================START LẤY THÔNG TIN CỦA MAIN VÀ MÁY TÍNH =======================================
        {
            // Get device information in docker
            $device_information = [];
            $fp = fopen(storage_path("output.txt"), 'r');
            while (!feof($fp)) {
                $line = fgets($fp);
                $arr = explode(':', $line);
                if (count($arr) > 1) {
                    if (str_contains($arr[1], ',')) {
                        $arr2 = explode(',', $arr[1]);
                        foreach ($arr2 as $k => $item) {
                            $arr2[$k] = trim($item);
                        }
                        $device_information[][$arr[0]] = $arr2;
                    } else {
                        $device_information[][$arr[0]] = trim($arr[1]);
                    }
                }
            }
            fclose($fp);

            $name = $device_information[0]['Computer Name'];
            $cpu_id = $device_information[1]['CPU ID'];
            $baseboard_serialnumber_value = $device_information[2]['Motherboard ID'];
            $mac_address = $device_information[4]['MAC Addresses'][0];
            if (is_array($device_information[3]['IP Addresses'])) {
                $ip_address = end($device_information[3]['IP Addresses']);
            } else {
                $ip_address = $device_information[3]['IP Addresses'];
            }

            $string_device_code = '{"software_key":"' . config('software.software_key') . '","hardware_id":{"cpu_id":"' . $cpu_id . '","main_board_id":"' . $baseboard_serialnumber_value . '","mac_address":"' . $mac_address . '"},"device_info":{"name":"' . $name . '","ip_address":"' . $ip_address . '"}}';
            $string_hashed_key = '{"software_key":"' . config('software.software_key') . '","hardware_id":{"cpu_id":"' . $cpu_id . '","main_board_id":"' . $baseboard_serialnumber_value . '","mac_address":"' . $mac_address . '"}}';

            //PHẢI THÊM VÀO CHỖ NÀY , VÌ CÓ THỂ CÓ FILE LICENSE SAI TRƯỚC RỒI , THÌ NÓ SẼ KO CHẠY VÀO KIA(khi chưa có key license và chưa nhâp actice code) , KO TẠO DC DEVICE_CODE ĐỂ COPY
            //mã hoá $string_device_code
            $encode_device_code = $this->encrypt_kztek($string_device_code);
            //gán vào session , cho hiển thị ở màn hình nhập
            session(["device_code" => $encode_device_code]);
        }
        //===========================END LẤY THÔNG TIN CỦA MAIN VÀ MÁY TÍNH =======================================

        //màn hình thứ 1: khi chưa có key license và chưa nhâp actice code , chỉ hiện thị device_code -------
        if (!Storage::exists('kztech.txt') and !isset($request->active_code)) {
            //mã hoá $string_device_code
            $encode_device_code = $this->encrypt_kztek($string_device_code);

            //nó ko lưu session vào request đâu , nên xoá ko tác dụng ,nhưng cứ để
            session()->flush();

            //gán vào session , cho hiển thị ở màn hình nhập
            session(["device_code" => $encode_device_code]);

            //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
            Auth::logout();
            return redirect('active');
        } else {
            //Hoặc là đã nhập actice code ,hoặc là đã có file license key
            //màn hình thứ 2:  chức năng actice code
            if (isset($request->active_code)) {
                if ($request->active_code == "") {
                    return redirect('active')->with('error_active_code', 'Phải nhập active_code');
                } else {
                    //lấy giá tri người nhập active code từ input
                    $key_contents = $request->active_code;
                }
            } else {
                //màn hình thứ 3 : đã có file key license
                //đọc file kztech.txt
                $key_contents = Storage::get('kztech.txt');
            }

            //kiểm tra xem chuỗi nhập có chứa hash key ko
            if (strpos($key_contents, $interal_key) === false) {
                //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                Auth::logout();
                return redirect('active')->with('error_active_code', 'Lỗi : License không đúng !');
            }

            //bỏ đi $interal_key ở vị trí bắt đầu 21
            $active_code = substr($key_contents, 0, 21) . substr($key_contents, 21 + strlen($interal_key));

            //giải mã
            $active_code = json_decode($this->decrypt_kztek($active_code));

            //lấy thông tin
            $hashed_key = isset($active_code->hashed_key) ? $active_code->hashed_key : null;
            $identifier_code = isset($active_code->identifier_code) ? $active_code->identifier_code : null;
            $is_unlimited = isset($active_code->is_unlimited) ? $active_code->is_unlimited : null;
            $start_date = isset($active_code->start_date) ? $active_code->start_date : null;
            $end_date = isset($active_code->end_date) ? $active_code->end_date : null;   //"2022-11-18 00:00:00.000"
            $software_key = isset($active_code->software_key) ? $active_code->software_key : null;

            //kiểm tra điều kiện ===================================
            {
                $is_check = true;

                //software_key: có giống nhau không
                if ($software_key != config('software.software_key')) {

                    //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                    Auth::logout();
                    return redirect('active')->with('error_active_code', 'Lỗi : License không đúng !');
                } else {
                    //check hashed_key: có giống nhau không
                    if ($this->hash_kztek($string_hashed_key) != $hashed_key) {
                        //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                        Auth::logout();
                        return redirect('active')->with('error_active_code', 'Lỗi : License không đúng !');
                    } else {
                        //check thời gian; nếu nó không phải là vĩnh viễn
                        if (!$is_unlimited) {
                            //nếu cả 2 đều bằng null lá sai
                            if (is_null($start_date) and is_null($end_date)) {
                                //có thời hạn nhưng start_date và end_date đều là NULL

                                //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                                Auth::logout();
                                return redirect('active')->with('error_active_code', 'Lỗi : License không đúng !');
                            } else {
                                //lấy ngày hiện tại
                                $now = Carbon::now();

                                //kiẻm tra có khác null không
                                if (!is_null($start_date)) {
                                    $cb_start_date = Carbon::createFromFormat('Y-m-d G:i:s', substr($start_date, 0, 10) . " 00:00:00");
                                    //chưa đến ngày bắt đầu thì chưa dc dùng
                                    if ($now->lt($cb_start_date)) {
                                        //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                                        Auth::logout();
                                        return redirect('active')->with('error_active_code', 'Lỗi : License chưa đến ngày bắt đầy được sử dụng !');
                                    }
                                }

                                if (!is_null($end_date)) {
                                    $cb_end_date = Carbon::createFromFormat('Y-m-d G:i:s', substr($end_date, 0, 10) . " 23:59:59");
                                    //quá ngày đc sử dụng thì ko đc sử dụng
                                    if ($now->gt($cb_end_date)) {
                                        //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                                        Auth::logout();
                                        return redirect('active')->with('error_active_code', 'Lỗi : License đã hết hạn sử dụng !');
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //kiểm tra điều kiện ===================================


            //màn hình thứ 2:  chức năng actice code
            if (isset($request->active_code)) {
                //nếu lỗi :
                if (!$is_check) {
                    //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                    Auth::logout();
                    return redirect('active');
                } else {
                    //lưu file
                    Storage::disk('local')->put('kztech.txt', $key_contents);

                    //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                    Auth::logout();
                    return redirect('active');
                }
            } else {
                //màn hình thứ 3 : đã có file key license ----------------------------
                if (!$is_check) {
                    //vì có trường hợp đang chạy , bị mất file license, nó sẽ vào đâng , thì bắt nó phải logout ra nhé
                    Auth::logout();
                    return redirect('active');
                } else {
                    return redirect('/');

                    //cho chạy bình thường
                }
            }
        }

        if (env('APP_ENV') === 'production') {
            URL::forceSchema('https');
        }
    }

}
