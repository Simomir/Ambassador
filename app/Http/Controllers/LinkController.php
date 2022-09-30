<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkProduct;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function index($id)
    {
        return Link::where('user_id', $id)->get();
    }

    public function store(Request $request)
    {
        $link = Link::create([
            'user_id' => Auth::id(),
            'code' => Str::random(10)
        ]);

        foreach ($request->input('products') as $product_id)
        {
            LinkProduct::create([
                'link_id' => $link->id,
                'product_id' => $product_id
            ]);
        }

        return $link;
    }

    public function show(string $code)
    {
        return Link::with('user', 'products')->where('code', $code)->get();
    }
}
