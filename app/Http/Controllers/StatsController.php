<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Order;
use Auth;
use Illuminate\Support\Facades\Redis;

class StatsController extends Controller
{
    public function index()
    {
        $links = Link::where('user_id', Auth::id())->get();
        return $links->map(
            function (Link $link) {
                $orders = Order::where('code', $link->code)->where('complete', 1)->get();
                return [
                    'code' => $link->code,
                    'count' => $orders->count(),
                    'revenue' => round($orders->sum(fn(Order $order) => $order->ambassador_revenue), 2),
                ];
            }
        );
    }

    public function rankings()
    {
        Redis::zrevrange('rankings', 0, -1, 'WITHSCORES');
    }
}
