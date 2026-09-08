<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {

        $users = User::select('nia','name','email','password','role','phone','kta_qr_code')
        ->latest()
        ->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }
}