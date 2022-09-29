<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Cache;
use Illuminate\Http\Request;
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
        return response($product, Response::HTTP_CREATED);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->only('title', 'description', 'image', 'price'));
        return response($product, Response::HTTP_ACCEPTED);
    }

    public function destroy(Product $product)
    {
        $product->delete();
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

    public function backend()
    {
        return Cache::remember('products_backend', 1800, function () {
            return Product::paginate();
        });
    }
}
