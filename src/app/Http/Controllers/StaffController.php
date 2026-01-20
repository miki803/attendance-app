<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    public function index() {

        $staffs = User::where('is_admin', false)->get();

        return view('admin.staff.index',compact('staffs'));
    }
}
