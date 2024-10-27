<?php

namespace App\Repositories;

use App\Libs\ValueUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository
{
    protected $model;

    public function __construct() {
        $this->setModel();
    }

    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel() {
        $this->model = app()->make($this->getModel());
    }

    /**
     * Find by id
     *
     * @param string|int $id
     * @param bool $isFindAll
     * @param mixed $ids
     * @return object|bool
     */
    /**
     * Find by id
     *
     * @param string|int $id
     * @param bool $isFindAll
     * @return object|bool
     */
    public function findById($id, $isFindAll = false)
    {
        if (!is_numeric($id)) {
            return false;
        }

        try {
            $query = $this->model->where($this->model->getKeyName(), $id);
            if (!$isFindAll) {
                $query->whereNull('deleted_at');
            }

            $founded = $query->first();

            if (empty($founded)) {
                throw new ModelNotFoundException(trans('messages.not_found'));
            }

            return $founded;

        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * Update data
     *
     * @param int $id
     * @param array $params: params need match all fields of model
     * @param bool $isFindAll
     * @return object|mixed|boolean
     */
    public function update($id, $params, $isFindAll = false) {
        try {
            $query = $this->findById($id, $isFindAll);
            $query->fill($params);
            DB::beginTransaction();
            $result = $query->save($params);
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $query;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Create data
     *
     * @param array $params: params need match all fields of model
     * @return object|mixed|boolean
     */
    public function create($params) {
        try {
            DB::beginTransaction();
            $result = $this->model->create($params);
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Delete by id
     *
     * @param string|int $id
     * @return mixed|null
     */
    // use this for save deleted_by userId
    public function deleteById($id) {
        try {
            if ($query = $this->findById($id)) {
                $query->fill([
                    'deleted_at' => Carbon::now(),
                ]);

                return $query->save();
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Save many
     *
     * @param object[array] $collections
     * @return object[array]|bool
     */
    public function saveMany($collections) {
        try {
            DB::transaction(function () use ($collections) {
                foreach ($collections as $key => $collection) {
                    if (! $collection->save()) {
                        return false;
                    }
                }
            });

            return $collections;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Update many record
     *
     * @param array $params
     * @param bool $isFindAll
     * @return object[array]|bool
     */
    public function updateMany($params, $isFindAll = false) {
        try {
            $result = collect();
            DB::transaction(function () use ($params, $isFindAll, &$result) {
                foreach ($params as $key => $items) {
                    $query = $this->findById($params[$key][$this->model->getKeyName()], $isFindAll);
                    $query->fill($items);
                    if (! $query->save($items)) {
                        return false;
                    }
                    $result->push($query);
                }
            });

            return $result;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
