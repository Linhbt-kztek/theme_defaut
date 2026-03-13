<?php

namespace App\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MinioService
{
    public function createBucket($bucketName)
    {
        // Lấy client S3 từ Laravel storage driver
        $client = Storage::disk('s3')->getClient();

        try {
            // Kiểm tra xem bucket đã tồn tại chưa
            if (!$client->doesBucketExist($bucketName)) {
                // Nếu chưa tồn tại, tạo bucket mới
                $client->createBucket([
                    'Bucket' => $bucketName,
                ]);
                return 'Bucket created successfully: ' . $bucketName;
            } else {
                return 'Bucket already exists: ' . $bucketName;
            }
        } catch (Exception $th) {
            return 'Error: ' . $th->getMessage();
        }
    }

    public function getCloudImage($folder, $imagePath, $base64Image = false): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        $client = Storage::disk('s3')->getClient();
        $bucket = config('filesystems.disks.s3.bucket');
        $key = rtrim($folder, '/') . '/' . ltrim($imagePath, '/');

        try {
            // Kiểm tra sự tồn tại của object
            $head = $client->headObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            if ($base64Image) {
                $result = $client->getObject([
                    'Bucket' => $bucket,
                    'Key' => $key,
                ]);

                // Lấy dữ liệu nhị phân
                $body = $result['Body']->getContents();

                // Lấy mime type nếu có (tùy vào nhu cầu, có thể 'image/jpeg', 'image/png', ...)
                $mimeType = isset($result['ContentType']) ? $result['ContentType'] : 'image/jpeg';

                $base64 = base64_encode($body);
                return 'data:' . $mimeType . ';base64,' . $base64;
            } else {
                $command = $client->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key' => $key,
                ]);
                $request = $client->createPresignedRequest($command, '+10060 minutes'); // hoặc '10060 minutes' tùy format
                return (string) $request->getUri();
            }

        } catch (\Aws\S3\Exception\S3Exception $e) {
            // Đối tượng không tồn tại hoặc lỗi truy cập
            return url('images/noimage.jpg');
        }
    }

    public function pushImageCloud($image, $folder = 'logo', $image_name = null): string
    {
        $image_name = $image_name ?? time() . '.' . $image?->extension();
        $imagePath = $folder . '/' . $image_name;
        Storage::cloud()->put($imagePath, file_get_contents($image));

        return $image_name;
    }

    public function pushBase64ImageCloud($base64Image, $folder = 'logo'): string
    {
        if (str_starts_with($base64Image, 'data:')) {
            $base64Image = preg_replace('#^data:.*?base64,#i', '', $base64Image);
        }

        $imageData = base64_decode($base64Image);
        $image_name = time() . '.png'; // hoặc định dạng phù hợp
        $imagePath = $folder . '/' . $image_name;

        Storage::cloud()->put($imagePath, $imageData);

        return $image_name;
    }

    public function deleteImageCloud($folder, $imagePath): bool
    {
        return Storage::cloud()->delete($folder . "/" . $imagePath);
    }

    private function handleImageBanner($image, $nameImage): ?string
    {
        $name_image = $nameImage . '.' . $image->extension();
        $imagePath = 'banner' . '/' . $name_image;
        Storage::cloud()->put($imagePath, file_get_contents($image));
        return $name_image;
    }


    public function pushBanner($request): bool|string
    {
        $banners = [];
        for ($i = 0; $i <= 5; $i++) {
            if ($request->hasFile('image' . $i)) {
                $image = $request->file('image' . $i);
                $name_image = $this->handleImageBanner($image, 'image' . $i);
                $banners['image' . $i] = $name_image;
            } elseif (isset($request['no_edit_image' . $i]) && $request['no_edit_image' . $i] == "1") {
                $banners['image' . $i] = $request['previous_image' . $i];
            } else {
                $banners['image' . $i] = null;
            }
        }
        return json_encode($banners, JSON_THROW_ON_ERROR);
    }


    public function getJsonBanner($json = true)
    {
        $config = Config::query()->find(1);
        $arr_url_banner = [];

        if (!empty($config->banners)) {
            foreach (json_decode($config->banners) as $banner) {
                $arr_url_banner[] = $this->getCloudImage('banner', $banner);
            }
        }

        if ($json == true) {
            return json_encode($arr_url_banner);
        } else {
            return $arr_url_banner;
        }
    }

    public function readExcelFromMinio($path)
    {
        if (!Storage::disk('s3')->exists($path)) {
            throw new \Exception("File not found: " . $path);
        }

        $stream = Storage::disk('s3')->readStream($path);
        if ($stream === false) {
            throw new \Exception("Cannot read stream for " . $path);
        }

        $contents = stream_get_contents($stream);
        fclose($stream);

        $tmp = tempnam(sys_get_temp_dir(), 'excel_');
        file_put_contents($tmp, $contents);

        $spreadsheet = IOFactory::load($tmp);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        unlink($tmp);
        return $data;
    }
}
