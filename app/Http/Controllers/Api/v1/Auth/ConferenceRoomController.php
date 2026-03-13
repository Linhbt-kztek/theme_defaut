<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Camera\CameraRepositoryInterface;
use App\Repositories\ConferenceRoom\ConferenceRoomRepository;
use App\Services\MinioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ConferenceRoomController extends Controller
{
    public function __construct(
        protected ConferenceRoomRepository $conferenceRoomRepo,
        protected MinioService $minioService,
    ) {
    }

    private function getSeachData($request)
    {
        $filter = [];
        if (!empty($request->name)) {
            $filter[] = ['name', '=', $request->name];
        }

        if (!empty($request->capacity)) {
            $filter[] = ['capacity', '=', $request->capacity];
        }
        if (!empty($request->status)) {
            $filter[] = ['status', '=', $request->status];
        }
        return [
            'filter' => $filter
        ];
    }

    public function getData(Request $request)
    {
        $limit = $request->limit ?? 0;
        $where = $this->getSeachData($request);

        $rooms = $this->conferenceRoomRepo->getDataAllOption($where, $limit);

        $rooms->transform(function ($room) {
            $room->image_url = $this->minioService->getCloudImage('room_image', $room->image);
            return $room;
        });

        return $rooms;
    }

    private function saveData($request, $id = '')
    {

        try {
            DB::beginTransaction();
            $has_error = false;
            $message = 'Lưu dữ liệu thành công!';



            $validate = $this->validation($request, $id);
            if (!$validate['status']) {
                $has_error = !$validate['status'];
                $message = $validate['message'];
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image_name = $this->minioService->pushImageCloud($image, 'room_image');
            }

            if (!$has_error) {
                $data_store = [
                    'name' => $request->name,
                    'device_register_code' => $request->device_register_code,
                    'image' => $image_name ?? null,
                    'capacity' => $request->capacity,
                    'content' => $request->content,
                    'status' => $request->status ? 1 : 2, //1: hoạt động, 2: không hoạt động 
                ];

                if (!empty($id)) {
                    $room_old = $this->conferenceRoomRepo->getById($request->id);
                    if ($room_old) {

                        $data_store = [
                            'name' => $request->name,
                            'device_register_code' => $request->device_register_code,
                            'image' => $image_name ?? $room_old->image,
                            'capacity' => $request->capacity ?? $room_old->capacity,
                            'content' => $request->content ?? $room_old->content,
                            'status' => $request->status ? 1 : 2, //1: hoạt động, 2: không hoạt động 
                        ];
                        $room_old->update($data_store);
                    } else {
                        $has_error = true;
                        $message = 'Không tìm thấy room với id bạn gửi!';
                    }
                } else {
                    $data_store['id'] = getGUID();

                    $result = $this->conferenceRoomRepo->create($data_store);
                    if (!$result) {
                        $has_error = false;
                        $message = 'Xảy ra lỗi trong quá trình lưu dữ liệu';
                    }
                }
            }
        } catch (\Throwable $th) {
            $has_error = true;
            $message = 'Xảy ra lỗi trong quá trình lưu dữ liệu';
        }


        $has_error ? DB::rollBack() : DB::commit();

        return [
            'status' => $has_error ? 90 : 200,
            'data' => $result ?? [],
            'message' => $message ?? 'Lưu dữ liệu thành công!'
        ];
    }

    public function store(Request $request)
    {
        return response()->json($this->saveData($request), 200);
    }

    public function update(Request $request, $id)
    {
        return response()->json($this->saveData($request, $id), 200);
    }

    public function show($id)
    {
        $data = $this->conferenceRoomRepo->getById($id);

        $data->image_url = $this->minioService->getCloudImage('room_image', $data->image);

        return response()->json([
            'status' => !empty($data) ? 200 : 90,
            'data' => $data,
            'message' => !empty($data) ? '' : 'Không tim thấy dữ liệu với id bạn gửi!',
        ]);
    }

    public function delete($id)
    {
        $data = $this->conferenceRoomRepo->getById($id);

        if ($data) {
            $result = $data->delete();
        }

        return response()->json([
            'success' => !empty($data),
            'message' => !empty($data)
                ? 'Xóa thành công!'
                : 'Không tìm thấy dữ liệu với ID bạn gửi!',
        ]);
    }


    private function validation($request, $id = null)
    {

        $ignoreId = !empty($id) ? $id : null;
        $rules = [
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('conference_room', 'name')
                    ->ignore($ignoreId, 'id')
                    ->where(fn($query) => $query->where('deleted_at', '=', null)),
            ],
            'description' => 'nullable|string',
            'capacity' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'

        ];

        $messages = [
            'name.required' => 'Tên phòng họp không được để trống',
            'capacity.required' => 'Sức chứa không được để trống',

            'capacity.numberic' => 'Sức chưa phải là số',
            'name.max' => 'Tên phòng họp tối đa 191 ký tự',
            'name.unique' => 'Tên phòng họp đã tồn tại',
            'image.mimes' => 'File phải là ảnh jpg, jpeg, png',



        ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $has_error = true;
            $error_message = $validator->errors()->first();
        } else {
            $has_error = false;
        }

        return [
            'status' => !$has_error,
            'message' => $error_message ?? ''
        ];
    }
}
