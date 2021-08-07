<?php

namespace App\Http\Controllers;

use App\Models\app_purchase;
use App\Models\User;
use App\Models\Devices;
use AppPurchase;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Statement;


class AppiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(["email" => $email, "password" => $password])) {

            $user = Auth::user();
            $success["token"] = $user->createToken("login")->accessToken;
            return response()->json(['success' => $success

            ], 200);
        } else {

            return response()->json(["error" => "Unauthorized user"], 401);

        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|max:255', 'email' => 'required|string|email|max:255|unique:users', 'password' => 'required|string|min:6']);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 400);
        } else {

            // user uid check and Response
            $validator = Validator::make($request->all(), ['uid' => 'required|string|max:128', 'app_id' => 'required|string|email|max:128|unique:users', 'os' => 'required|string|min:3']);
            if (!$validator->fails()) {


                $user = Devices::where('uid', request('uid'))->first();


                if ($user !== null) {

                    $request['password'] = Hash::make($request['password']);
                    $user = User::create($request->toArray());
                    $token = $user->createToken('register')->accessToken;
                    return response()->json(['token' => $token], 200);


                } else {
                    $request['password'] = Hash::make($request['password']);
                    $user = User::create($request->toArray());
                    $token = $user->createToken('register')->accessToken;
                    return response()->json(['token' => $token], 200);


                }


                return response()->json(["error" => "Unauthorized user"], 400);


            }
            $request['password'] = Hash::make($request['password']);
            $user = User::create($request->toArray());
            $token = $user->createToken('register')->accessToken;
            $response = ['token' => $token];
            return response()->json($response, 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add_app_purchase(Request $request)
    {
        $validator = Validator::make($request->all(), ['expire_date' => 'required|datetime', 'receipt' => 'required|string|max:255|']);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 400);
        }
        if (Auth::guard('api')->check()) {


            if (!intval(substr($request->input("receipt"), -1, 1) % 2) == 0) {

                $user_id = $request->user()->id;
                $Date = date(strtotime(now() . ' + 15 days'));
                $status = (bool)random_int(0, 1);

                $data = ['status' => $status, 'expire_date' => $Date, 'receipt' => $request->input('receipt'), 'user_id' => $user_id];

                app_purchase::create($data);
                $dondur = ["status" => $status, "expire-date" => date("Y-m-d H:i:s", strtotime($Date . '- 6 hours'))];
                return response()->json($dondur, 200);
            }


        } else {


            return response()->json(["error" => "Unauthorized user"], 401);
        }


    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function app_purchase(Request $request)
    {

        if (Auth::guard('api')->check()) {

            $user_id = $request->user()->id;
            $data = AppPurchase::all()->where("user_id", "=", $user_id)->toArray();
            $dondur = $data;
            return response()->json($dondur, 200);
        } else {

            return response()->json(["error" => "Unauthorized user"], 400);
        }


    }


    /**
     * expire-date checker
     *  select all expire-date > now date and time get 100 rows with chunk;
     */
    public function worker()
    {
        Devices::query()->where(['expire_date', '>', date(now() . "- 6 hours")], 'and', ['status', '=', true])->chunk(100, function ($devices) {
            foreach ($devices as $device) {
                if ($this->expire_date_check($device->receipt)) {
                    $devices->update(['status' => true]);

                }


                $devices->update(['status' => false]);
            }
        });
    }


    // google/ios receipt response
    function expire_date_check()
    {
        return (bool)random_int(0, 1);

    }

}
