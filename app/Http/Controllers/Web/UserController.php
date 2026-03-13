<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Models\CustomerClassification;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\MinioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class UserController extends Controller
{

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected MinioService            $minioService,
    )
    {

    }

    public function processLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ], [
            'user_name.required' => 'Tên đăng nhập không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
            'user_name.string' => 'Tên đăng nhập không hợp lệ'
        ]);
        $credentials = $request->only('user_name', 'password');

        Session::flush();
        Auth::logout();

        $checkLogin = Auth::guard('web')->attempt($credentials);
        if ($checkLogin) {
            $user = Auth::guard('web')->user();
            $customerClassification = $user->customerClassification;
            if ((int)$user->type === 1 && (int)$user->is_classification_customer === 1 && $customerClassification) {
                return redirect()->route('register_online.index');
            }
            Session::flush();
            Auth::guard('web')->logout();
            return redirect()->back()->with('error', 'Tài khoản không được cho phép');
        }
        Session::flush();
        Auth::guard('web')->logout();
        return redirect()->route('register_online.index')->with('error', 'Tên đăng nhập hoặc mật khẩu không đúng');
    }

    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::guard('web')->logout();
        return redirect()->route('register_online.index');
    }


    public function profile()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return view('registerOnlineV2.user.profile', compact('user'));
        }
        return redirect()->route('register_online.index')->with('error', 'Vui lòng đăng nhập trước');
    }

    public function changeAvatar(Request $request): ?JsonResponse
    {
        try {
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                if ($request->hasFile('user_avatar')) {
                    $image = $request->file('user_avatar');
                    $this->minioService->deleteImageCloud($image, $user->user_avatar);
                    $image_name = $this->minioService->pushImageCloud($image, 'user_avatar');
                    $user->update(['user_avatar' => $image_name]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Upload success'
                    ]);
                }
            }
            return response()->json([
                'status' => 404,
                'message' => 'Upload failed'
            ]);

        } catch (\Exception $e)  {
            return response()->json([
                'status' => 404,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(UserUpdateRequest $request): ?RedirectResponse
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone
            ];
            auth()->user()->update($data);

            if ($request->address || $request->taxCode) {
                CustomerClassification::query()
                    ->firstWhere('user_id', auth()->user()->id)?->update([
                        'address' => $request->address,
                        'taxCode' => $request->taxCode
                    ]);
            }
            DB::commit();
            return back()->with('success', 'Cập nhật thành công');
        } catch (\Exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình cập nhật');
        }

    }


    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|min:6|string|confirmed',
            'new_password_confirmation' => 'bail|required|min:6'
        ], [
            '*.required' => 'Vui lòng điền thông tin',
            '*.string' => 'Thông tin không hợp lệ',
            'new_password.min' => 'Mật khẩu tối thiếu 6 ký tự',
            'min.confirmed' => 'Mật khẩu tối thiếu 6 ký tự',
            'new_password.confirmed' => 'Mật khẩu xác nhận không đúng',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with("error-change-pwd", "Mật khẩu hiện tại không đúng");
        }

        #Update the new Password
        User::query()->find(auth()->user()->id)?->update([
            'password' => Hash::make($request->new_password)
        ]);
        return back()->with("success-change-pwd", "Cập nhật mật khẩu thành công");

    }

    public function historyPayment(Request $request)
    {

    }

}
