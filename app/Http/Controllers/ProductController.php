<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    public function products(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $products = $this->productService->getResults($this->pagination);

        return  $this->success(ProductResource::collection($products)->response()->getData(true));
    }
}
