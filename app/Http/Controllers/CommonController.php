<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommonController extends Controller
{
    public function generate_slug(Request $request)
    {
        $slug = Str::slug($request->title);

        return response()->json([
            'status' => true,
            'slug' => $slug
        ]);
    }
}
