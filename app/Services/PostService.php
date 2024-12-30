<?php
namespace App\Services;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Traits\CrudTrait; // CrudTraitをインポート

class PostService
{
    use CrudTrait; // CrudTraitを使用

    // コンストラクタで$modelClassを設定
    public function __construct()
    {
        $this->modelClass = Post::class;
    }

    public function index()
    {
        return $this->indexCrud(); // CrudTraitのindexメソッドを呼び出し
    }

    public function store(StorePostRequest $request)
    {
        return $this->storeCrud($request); // CrudTraitのstoreメソッドを呼び出し
    }

    public function show($id, array $columns = ['*'])
    {
        return $this->showCrud($id, $columns); // CrudTraitのshowメソッドを呼び出し
    }

    public function update(UpdatePostRequest $request, $id)
    {
        return $this->updateCrud($request->validated(), ['id' => $id]); // CrudTraitのupdateメソッドを呼び出し
    }

    public function destroy($id)
    {
        return $this->removeCrud($id); // CrudTraitのremoveメソッドを呼び出し
    }
}
