<?php

/**
 *@copyright : ASk. < http://arshresume.epizy.com/ >
 *@author	 : Arshdeep Singh < arshdeepsinghjoshan84@gmail.com >
 *
 * All Rights Reserved.
 * Proprietary and confidential :  All information contained herein is, and remains
 * the property of ASK. and its partners.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Notification\Http\Models\Notification;
use Modules\Smtp\Http\Models\SmtpEmailQueue;

class DashboardController extends Controller
{



    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the customer page
     *
     */
    public function index()
    {
        // Session::flash('error', 'This is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a messageThis is a message!'); 
        return view('dashboard.index');
    }

    public function getChartData()
    {
       
        $customersData = User::Where(['role_id'=> User::ROLE_USER])->pluck('id')->toArray();

        $chartData = [
            'customers' => $customersData,
        ];

        return response()->json($chartData);
    }
}
