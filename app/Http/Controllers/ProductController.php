<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdatedEvent;
use App\Models\Product;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $product = Product::create($request->only('title', 'description', 'image', 'price'));
        event(new ProductUpdatedEvent);
        return response($product, Response::HTTP_CREATED);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->only('title', 'description', 'image', 'price'));
        event(new ProductUpdatedEvent);
        return response($product, Response::HTTP_ACCEPTED);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        event(new ProductUpdatedEvent);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function frontend()
    {
        if($products = Cache::get('products_frontend')) {
            return $products;
        }
        sleep(3);
        $products = Product::all();
        Cache::set('products_frontend', $products, 1800);
        return $products;
    }

    public function backend(Request $request)
    {
        $page = $request->input('page', 1);

        /** @var Collection $products */
        $products = Cache::remember('products_backend', 1800, fn () => Product::all());

        // search by str fragment in title or description
        if($s = $request->input('s')) {
            $products = $products->filter(
                fn(Product $product) => Str::contains($product->title, $s) || Str::contains($product->description, $s)
            );
        }

        $total = $products->count();

        // sort by price in asc or desc order
        if($sort = $request->input('sort')) {
            if($sort === 'asc') {
                $products = $products->sortBy([
                    fn($a, $b) => $a['price'] <=> $b['price']
                ]);
            } elseif ($sort === 'desc') {
                $products = $products->sortBy([
                    fn($a, $b) => $b['price'] <=> $a['price']
                ]);
            }
        }

        // return statement with manual pagination
        return [
            'data' => $products->forPage($page, 9)->values(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'last_page' => ceil($total / 9)
            ],
        ];
    }
}
