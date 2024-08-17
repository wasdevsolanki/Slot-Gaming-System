<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Reading;
use App\Models\Room;
use App\Models\User;
use App\Models\Bank;

class TransactionController extends Controller
{
    public function index() {

        return view('admin.pages.transaction.index');
    }
}