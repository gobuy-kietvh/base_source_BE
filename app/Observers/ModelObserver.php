<?php

namespace App\Observers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{Auth, Log};

class ModelObserver
{
    /**
     * Handle the model "creating" event.
     * @param Model $model
     */
    public function creating(Model $model): void {
        $userId = Auth::id(); // Lưu ID của người dùng hiện tại
        $now = Carbon::now();

        // Gán giá trị cho các trường nếu có
        $attributes = [
            'created_at' => $now,
            'updated_at' => $now,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        $fillableAttributes = array_flip($model->getFillable());

        foreach ($attributes as $key => $value) {
            if (isset($fillableAttributes[$key])) {
                $model->{$key} = $value;
            }
        }

        Log::info('Creating model', [
            'model' => get_class($model),
            'attributes' => $model,
            'user_id' => $userId,
        ]);
    }

    /**
     * Handle the model "updating" event.
     * @param Model $model
     */
    public function updating(Model $model): void {
        $userId = Auth::id(); // Lưu ID của người dùng hiện tại
        $now = Carbon::now();

        // Gán giá trị cho các trường nếu có
        $attributes = [
            'updated_at' => $now,
            'updated_by' => $userId,
        ];

        // Kiểm tra trường deleted_at để xử lý xóa mềm
        if ($model->isDirty('deleted_at') && $model->deleted_at !== null) {
            $attributes['deleted_at'] = $now;
            $attributes['deleted_by'] = $userId;
        }

        $fillableAttributes = array_flip($model->getFillable());

        foreach ($attributes as $key => $value) {
            if (isset($fillableAttributes[$key])) {
                $model->{$key} = $value;
            }
        }

        Log::info('Updating model', [
            'model' => get_class($model),
            'attributes' => $model,
            'user_id' => $userId,
        ]);
    }

    /**
     * Handle the model "updating" event.
     * @param Model $model
     */
    public function deleting(Model $model): void {
        $userId = Auth::id(); // Lưu ID của người dùng hiện tại

        // Gán giá trị cho các trường nếu có
        $attributes = [
            'deleted_by' => $userId,
        ];

        $fillableAttributes = array_flip($model->getFillable());

        foreach ($attributes as $key => $value) {
            if (isset($fillableAttributes[$key])) {
                $model->{$key} = $value;
            }
        }
        $model->saveQuietly();

        Log::info('Deleting model', [
            'model' => get_class($model),
            'user_id' => $userId,
        ]);
    }
}
