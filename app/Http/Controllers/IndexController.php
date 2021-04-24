<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {

        if (Auth::check()) {
            return redirect()->route('admin.portfolio.index');
        }

        return redirect()->route('login');
        abort(404);
        return redirect()->route('public.portfolio');
    }
}
