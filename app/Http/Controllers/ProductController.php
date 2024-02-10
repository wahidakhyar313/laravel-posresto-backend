<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        $query = $request->input('query');
        $productsQuery = Product::query();
        if ($query) {
            $productsQuery->where('name', 'like', '%' . $query . '%');
        }

        $products = $productsQuery->paginate(10);

       return view('pages.products.index', compact('products'));
    }

    //create
    public function create()
    {
        $categories = DB::table('categories')->get();
        return view('pages.products.create', compact('categories'));
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;

        $product->save();

        if ($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
            $product->image = 'storage/product/' . $product->id . '.' . $image->getClientOriginalExtension();

            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    // show
    public function show()
    {
        return view('pages.products.show');
    }

    // edit
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = DB::table('categories')->get();
        return view('pages.products.edit', compact('product','categories'));
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

        if ($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
            $product->image = 'storage/product/' . $product->id . '.' . $image->getClientOriginalExtension();

            $product->save();
        }

        return redirect()->route('products.index')->with('success','Product updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        $user = Product::find($id);
        $user->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
