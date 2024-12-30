<?php
namespace App\Services;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostService
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function index()
    {
        return $this->post->index();
    }

    public function store(StorePostRequest $request)
    {
        return $this->post->store($request);
    }

    public function show($id)
    {
        return $this->post->show($id);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        return $this->post->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->post->destroy($id);
    }
}
