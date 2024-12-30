<?php

namespace App\Http\Controllers;
use App\Services\PostService;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        return $this->postService->index();
    }

    public function store(StorePostRequest $request)
    {
        return $this->postService->store($request);
    }

    public function show($id)
    {
        return $this->postService->show($id);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        return $this->postService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->postService->remove($id);
    }
}
