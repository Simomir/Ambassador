<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Link;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Cartalyst\Stripe\Stripe;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class OrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OrderResource::collection(Order::with('orderItems')->get());
    }

    /**
     * @throws Throwable
     */
    public function store(Request $request): Order
    {
        if(!$link = Link::where('code', $request->input('code'))->first()) {
            abort(400, 'Invalid code');
        };

        try {
            DB::beginTransaction();

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

            $lineItems = [];

            foreach ($request->input('products') as $item) {
                $product = Product::find($item['product_id']);

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_title = $product->title;
                $orderItem->price = $product->price;
                $orderItem->quantity = $item['quantity'];
                $orderItem->ambassador_revenue = $product->price * $item['quantity'] * 0.1;
                $orderItem->admin_revenue = $product->price * $item['quantity'] * 0.9;

                $orderItem->save();

                $lineItems[] = [
                    'name' => $product->title,
                    'description' => $product->description,
                    'images' => [
                        $product->image
                    ],
                    'amount' => $product->price * 100,
                    'currency' => 'usd',
                    'quantity' => $item['quantity'],
                ];
            }

            $stripe = Stripe::make(env('STRIPE_SECRET'));
            $source = $stripe->checkout()->sessions()->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'success_url' => env('CHECKOUT_URL') . '/success?source={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('CHECKOUT_URL') . '/error'
            ]);

            $order->transaction_id = $source['id'];
            $order->save();

            DB::commit();
            return $source;
        } catch (Throwable $exception) {
            DB::rollBack();
            abort(500, 'An error occurred');
        }
    }
}
