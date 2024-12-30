<?php
namespace App\Services;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryService
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        return $this->category->index();
    }

    public function store(StoreCategoryRequest $request)
    {
        return $this->category->store($request);
    }

    public function show($id)
    {
        return $this->category->show($id);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        return $this->category->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->category->destroy($id);
    }
}
