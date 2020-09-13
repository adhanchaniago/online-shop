<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ImageResize;
use File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['product'] = Product::all();
        return view('admin/product/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['category'] = Category::all();
        return view('admin/product/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $productRequest)
    {
        $productRequest->validated();

        $picture = $productRequest->file('picture');

        $name = uniqid() . '_' . trim($picture->getClientOriginalName());

        $img = ImageResize::make($picture->path());

        $img->resize(195, 243)->save('assets/photo/'.$name);

        Product::create([
           'product_name' => $productRequest->product_name,
           'price' => $productRequest->price,
           'stock' => $productRequest->stock,
           'category_id' => $productRequest->category,
           'description' => $productRequest->description,
           'slug' => Str::slug($productRequest->product_name),
           'picture' => $name
        ]);

        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['category'] = Category::all();
        $data['product'] = Product::findOrFail($id);
        return view('admin/product/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $productRequest, $id)
    {
        $productRequest->validated();

        $picture = $productRequest->file('picture');

        $name = uniqid() . '_' . trim($picture->getClientOriginalName());

        $img = ImageResize::make($picture->path());

        $img->resize(195, 243)->save('assets/photo/'.$name);

        $product = Product::find($id);

        if ($productRequest->hasFile('picture')) {
            File::delete('assets/photo/' . $product->picture);
        }

        $product->update([
            'product_name' => $productRequest->product_name,
            'price' => $productRequest->price,
            'stock' => $productRequest->stock,
            'category_id' => $productRequest->category,
            'description' => $productRequest->description,
            'slug' => Str::slug($productRequest->product_name),
            'picture' => $name
        ]);

        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->delete()) {
            File::delete('assets/photo/' . $product->picture);
            return redirect()->route('product.index');
        }
    }
}
