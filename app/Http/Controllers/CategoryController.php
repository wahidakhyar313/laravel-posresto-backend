<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    //index
    public function index(Request $request)
    {
        $query = $request->input('query');
        $categoriesQuery = Category::query();
        if ($query) {
            $categoriesQuery->where('name', 'like', '%' . $query . '%');
        }

        $categories = $categoriesQuery->paginate(10);

       return view('pages.categories.index', compact('categories'));
    }

    //create
    public function create()
    {
        return view('pages.categories.create');
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;

        $category->save();

        if ($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();

            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

     // show
     public function show()
     {
         return view('pages.categories.show');
     }

      // edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.categories.edit', compact('category'));
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:png,jpg,jpeg,gif,svg|max:2048',
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->description = $request->description;

        $category->save();

        if ($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();

            $category->save();
        }

        return redirect()->route('categories.index')->with('success','Category updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        $user = Category::find($id);
        $user->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }

}
