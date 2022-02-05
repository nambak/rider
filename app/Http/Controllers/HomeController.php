<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $branchOfficeID = isset(Auth::user()->employee) ? Auth::user()->employee->branch_id : null;

        if (Auth::user()->role == 'admin') {
            return redirect()->route('employee_list');
        } else {
            return view('home', compact('branchOfficeID'));
        }
    }
}
