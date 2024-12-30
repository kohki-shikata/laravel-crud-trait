<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;

trait CrudTrait
{
    // use LockableTrait; // LockableTraitを使用

    /** @var string $modelClass */
    protected $modelClass;

    /**
     * モデルの取得
     *
     * @return \Illuminate\Database\Eloquent\Model モデルインスタンス
     */
    protected function getModel()
    {
        if (empty($this->modelClass)) {
            throw new \Exception('Model class not defined. Please set $modelClass in the using class.');
        }

        if (!class_exists($this->modelClass)) {
            throw new \Exception('Model class ' . $this->modelClass . ' does not exist.');
        }

        return new $this->modelClass;
    }



    /**
     * JSONレスポンスを返すかどうかを判定
     *
     * @return bool
     */
    protected function isJsonResponse()
    {
        return request()->expectsJson();
    }

    /**
     * カラムの検証と取得
     *
     * @param  Model $model モデルインスタンス
     * @param  array $columns 検証するカラム名の配列
     * @return \Illuminate\Database\Eloquent\Builder クエリビルダー
     * @throws \InvalidArgumentException 無効なカラムが指定された場合
     */
    protected function selectColumns($model, $columns)
    {
        $validColumns = $model->getFillable(); // fillableを取得
        $guardedColumns = $model->getGuarded(); // guardedを取得

        foreach ($columns as $column) {
            // validColumnsに含まれているか、またはguardedに含まれていない場合は許可
            if (in_array($column, $guardedColumns) && $column !== '*' && !in_array($column, $validColumns)) {
                throw new \InvalidArgumentException("The column '$column' is not a valid column.");
            }
        }
        return $model::select($columns);
    }

    /**
     * 一覧表示
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View レスポンス
     */
    public function indexCrud()
    {
        return $this->handleRequest(function () {
            $model = $this->getModel();
            $maxResults = (int) env('MAX_RESULTS', 0);

            $records = $maxResults > 0
                ? $model::limit($maxResults)->get()
                : $model::all();

            return $this->response($records);
        });
    }

    /**
     * 詳細表示
     *
     * @param  int $id モデルのID
     * @param  array|null $columns 取得するカラムの配列（任意）
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View レスポンス
     */
    public function showCrud($id, array $columns = ['*'])
    {
        return $this->handleRequest(function () use ($id, $columns) {
            $model = $this->getModel();
            $query = $this->selectColumns($model, $columns);
            // デバッグ用にクエリを出力
            \Log::info($query->toSql(), $query->getBindings());
            $record = $query->where('id', $id)->firstOrFail();

            return $this->response($record);
        });
    }

    /**
     * 新規作成
     *
     * @param  Request $request リクエストインスタンス
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View レスポンス
     */
    public function storeCrud(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $model = $this->getModel();
            $record = $model::create($request->validated());

            return $this->response($record);
        });
    }

    /**
     * 更新
     *
     * @param  int $id 更新対象のモデルのID
     * @param  array $attributes 更新する属性
     * @param  array $options オプション
     * @return Model 更新されたモデル
     */
    public function updateCrud($id, array $attributes = [], array $options = [])
    {
        return $this->handleRequest(function () use ($id, $attributes, $options) {
            $model = $this->getModel()->findOrFail($id); // IDでモデルを取得
            $model->update($attributes, $options);
            return $model;
        });
    }

    /**
     * 削除
     *
     * @param  int $id 削除対象のモデルのID
     * @return array 成功メッセージ
     */
    public function removeCrud($id)
    {
        return $this->handleRequest(function () use ($id) {
            $model = $this->getModel();
            $record = $model::findOrFail($id);

            // if ($this->isLocked($record)) {
            //     return ['message' => 'Resource is locked by another user.'];
            // }

            $record->delete();
            return ['message' => 'Resource deleted'];
        });
    }

    /**
     * 複数レコードの一括作成
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function bulkCreateCrud(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $model = $this->getModel();

            // リクエストデータの取得
            $records = $request->validated()['records']; // バリデーション後のデータ

            try {
                // 一括挿入
                $model::insert($records);
            } catch (\Exception $e) {
                // 例外が発生した場合のエラーハンドリング
                return $this->handleError($e, 500); // エラーメッセージと500ステータスコード
            }

            // 成功した場合のレスポンス
            return $this->response($records);
        });
    }

    /**
     * 複数同時更新
     *
     * @param  array $updates 更新するデータの配列
     * @return bool 更新成功の場合はtrue、それ以外はfalse
     */
    public function bulkUpdateCrud(array $updates): bool
    {
        return $this->handleRequest(function () use ($updates) {
            foreach ($updates as $id => $data) {
                $this->getModel()::where('id', $id)->update($data);
            }
            return true;
        });
    }

    /**
     * 複数同時削除
     *
     * @param  array $ids 削除するモデルのID配列
     * @return bool 削除成功の場合はtrue、それ以外はfalse
     */
    public function bulkDeleteCrud(array $ids): bool
    {
        return $this->handleRequest(function () use ($ids) {
            $deletedCount = $this->getModel()::whereIn('id', $ids)->delete();
            return $deletedCount > 0;
        });
    }


    /**
     * 複数カラムで文字列検索
     * @param  Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \InvalidArgumentException
     */
    protected function searchCrud(Request $request)
    {
        $model = $this->getModel();
        $query = $model::query();

        // 検索条件の適用
        $searchTerm = $request->input('search');
        $columns = $request->input('columns', ['name']);  // デフォルトで 'name' カラムを検索

        // 各カラムがモデルのfillableに存在するか確認
        $validColumns = $model->getFillable();
        foreach ($columns as $column) {
            if (!in_array($column, $validColumns)) {
                throw new \InvalidArgumentException("The column '$column' is not a valid column in the model.");
            }
        }

        // 指定されたカラムで検索
        if ($searchTerm) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%' . $searchTerm . '%');
            }
        }

        return $query;
    }


    /**
     * リクエストの処理を共通化
     *
     * @param  callable $callback コールバック関数
     * @return mixed コールバックの戻り値
     */
    protected function handleRequest(callable $callback)
    {
        try {
            return $callback();
        } catch (\InvalidArgumentException $e) {
            return $this->handleError($e, 400);
        } catch (\Exception $e) {
            return $this->handleError($e, 500);
        }
    }

    /**
     * レスポンスの返却処理
     *
     * @param  mixed $data レスポンスデータ
     * @param  Request|null $request リクエストインスタンス（任意）
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View レスポンス
     */
    protected function response($data, Request $request = null)
    {
        if ($this->isJsonResponse()) {
            return response()->json($data);
        }

        return $request ? view($request->get('view', 'default.show'), ['record' => $data]) : $data;
    }

    /**
     * エラーハンドリング
     *
     * @param  \Exception $e 発生した例外
     * @param  int $statusCode HTTPステータスコード
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View エラーレスポンス
     */
    protected function handleError(\Exception $e, int $statusCode = 500)
    {
        $message = [
            'message' => $e->getMessage(),
            'error' => $e->getTraceAsString(),
        ];

        if ($this->isJsonResponse()) {
            return response()->json($message, $statusCode);
        }

        throw $e; // エラーがページ上で発生した場合は通常のエラーハンドリングに委譲
    }
}
