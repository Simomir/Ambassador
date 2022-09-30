<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Link;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OrderResource::collection(Order::with('orderItems')->get());
    }

    public function store(Request $request)
    {
        if(!$link = Link::where('code', $request->input('code'))->first()) {
            abort(400, 'Invalid code');
        };

        $order = new Order();

        $order->code = $link->code;
        $order->user_id = $link->user->id;
        $order->ambassador_email = $link->user->email;
        $order->first_name = $request->input('first_name');
        $order->last_name = $request->input('last_name');
        $order->email = $request->input('email');
        $order->address = $request->input('address');
        $order->country = $request->input('country');
        $order->city = $request->input('city');
        $order->zip = $request->input('zip');

        $order->save();
    }
}
