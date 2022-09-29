<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Order;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $links = Link::where('user_id', $request->user()->id)->get();

        return $links->map(
            function (Link $link) {
                $orders = Order::where('code', $link->code)->where('complete', 1)->get();
                return [
                    'code' => $link->code,
                    'count' => $orders->count(),
                    'revenue' => $orders->sum(fn(Order $order) => $order->ambassador_revenue),
                ];
            }
        );
    }
}
