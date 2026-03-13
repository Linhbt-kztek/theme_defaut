<?php

namespace App\Http\Controllers;

use App\Models\ConferenceRoom;
use App\Models\Config;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use JsonException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $orderRepo;
    protected $ticketRepo;
    protected $ticketTypeRepo;


    public function __construct(
    ) {
    }


    public function index(Request $request)
    {

        // if (!$this->checkfile()) {
        //     return view('errors.hasntKey');
        // }

        Log::channel('check_api')->info('router home/index');
        Cache::flush();
        $user = Auth::user();
      
        if ($user) {
            return redirect()->route('user');
        }

        Session::flush();
        Auth::logout();
        return redirect()->route('login');


    }

    public function configTicket()
    {
        if (session('show_customer_classification')) {
            session(['show_customer_classification' => false]);
            return 2;
        } else {
            session(['show_customer_classification' => true]);
            return 1;
        }

    }
}
