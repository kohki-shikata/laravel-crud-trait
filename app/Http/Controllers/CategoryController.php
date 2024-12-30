<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return $this->categoryService->index();
    }

    public function store(StoreCategoryRequest $request)
    {
        return $this->categoryService->store($request);
    }

    public function show($id)
    {
        return $this->categoryService->show($id);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        return $this->categoryService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->categoryService->destroy($id);
    }
}
