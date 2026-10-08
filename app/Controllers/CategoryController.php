<?php

namespace App\Controllers;

use App\Models\Category;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class CategoryController extends BaseController
{
    // Controller logic here
    public function index()
    {
        return $this->view('admin/category');
    }

    public function list()
    {
        $categories = Category::query()
            ->withCount(['documents'])
            ->orderBy('name', 'ASC')
            ->get();
            
        return $this->json([
            'data' => Category::toCleanArrayCollection($categories),
        ],200);
    }

    public function store(Request $request)
    {
        $ctg = Category::create([
            'name' => $request->name,
            'color' => $request->color,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_active' => 1,
        ]);
        return $this->json([
            'message' => 'Success added category',
            'data' => $ctg
        ],201);
    }

    public function update(Request $request, $id)
    {
        $ctg = Category::findOrFail($id);
        $ctg->name = $request->name;
        $ctg->color = $request->color;
        $ctg->icon = $request->icon;
        $ctg->description = $request->description;
        $ctg->save();
        return $this->json([
            'message' => 'Success update category'
        ],200);
    }

    public function destroy($id)
    {
        $ctg = Category::findOrFail($id);
        $ctg->delete();
        return $this->json([
            'message' => 'Success delete category',
        ],200);
    }
}
