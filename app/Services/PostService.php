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


    /**
     * 複数レコードの一括作成
     *
     * @param  array $records 作成するレコードの配列
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View レスポンス
     */
    public function bulkCreate(array $records)
    {
        return $this->bulkCreateCrud($records);
    }

    /**
     * 複数同時更新
     *
     * @param  array $updates 更新するデータの配列
     * @return bool 更新成功の場合はtrue、それ以外はfalse
     */
    public function bulkUpdate(array $updates): bool
    {
        return $this->bulkUpdateCrud($updates);
    }

    /**
     * 複数同時削除
     *
     * @param  array $ids 削除するモデルのID配列
     * @return bool 削除成功の場合はtrue、それ以外はfalse
     */
    public function bulkDelete(array $ids): bool
    {
        return $this->bulkDeleteCrud($ids);
    }

    /**
     * 複数カラムで文字列検索
     *
     * @param  Request $request 検索リクエスト
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search($searchTerm, array $columns = ['title', 'description'])
    {
        return $this->searchCrud(new \Illuminate\Http\Request(['search' => $searchTerm, 'columns' => $columns]));
    }
}
