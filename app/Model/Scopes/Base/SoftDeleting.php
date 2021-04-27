<?php

namespace App\Model\Scopes\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class SoftDeleting extends SoftDeletingScope
{

    protected $_model = null;

    /**
     * @return null
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param null $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }


    public function apply(Builder $builder, Model $model)
    {
        $this->_apply($builder, $model);
    }

    protected function _apply($query, $model)
    {
        if ($model->hasDelFlag()) {
            return $this->withoutTrashed($query, $model);
        }
        $query->whereNull($model->getQualifiedDeletedAtColumn());
    }

    public function withoutTrashed($query, $model)
    {
        $query = $query->where($model->getQualifiedDelFlagColumn(), '=', $model->getDelFlagValue());
        if ($this->_hasJoin($query)) {
            foreach ($query->getQuery()->joins as $join) {
                $tableName = str_replace(' AS ', '_x_x_', str_replace(' as ', '_x_x_', $join->table));
                $tableName = explode('_x_x_', $tableName);
                $newModel = $this->getModelFromTable($tableName[0]);
                if (!$newModel) {
                    continue;
                }
                $tableName = isset($tableName[1]) ? $tableName[1] : $tableName[0];
                $newModel->setTable($tableName);
                $join->where($newModel->getQualifiedDelFlagColumn(), '=', DB::raw($newModel->getDelFlagValue()));
            }
        }
    }

    public function getModelFromTable($table)
    {
        $scopes = $this->getModel()->getAllGlobalScopes();
        foreach ($scopes as $scope) {
            foreach ($scope as $value) {
                if ($value instanceof \App\Model\Scopes\Base\SoftDeleting) {
                    $model = $value->getModel();
                    if ($model->getTable() == $table) {
                        return $model;
                    }
                    break;
                }
            }
        }
        return false;
    }

    protected function _hasJoin($builder)
    {
        try {
            return count((array)$builder->getQuery()->joins) > 0;
        } catch (\Exception $e) {

        }
        return false;
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $columns = $this->_getUpdateColumn($builder, $builder->getModel()->freshTimestampString(), $this->getDeletedAtColumn($builder), true);
            return $builder->update($columns);
        });
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addRestore(Builder $builder)
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();
            return $builder->update($this->_getUpdateColumn($builder));
        });
    }


    /**
     * Add the without-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addWithoutTrashed(Builder $builder)
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            if ($model->hasDelFlag()) {
                $builder->withoutGlobalScope($this)->where(function ($q) use ($model) {
                    $q->where($model->getQualifiedDelFlagColumn(), '=', $model->getDelFlagValue())->orWhereNull(
                        $model->getQualifiedDelFlagColumn());
                });
                return $builder;
            }
            $builder->withoutGlobalScope($this)->orWhereNull(
                $model->getQualifiedDeletedAtColumn()
            );

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function addOnlyTrashed(Builder $builder)
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();
            if ($model->hasDelFlag()) {
                $builder->withoutGlobalScope($this)->where(function ($q) use ($model) {
                    $q->where($model->getQualifiedDelFlagColumn(), '=', $model->getDelFlagValue(true));
                });
                return $builder;
            }

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedDeletedAtColumn()
            );

            return $builder;
        });
    }

    protected function _getUpdateColumn(Builder $builder, $value = null, $column = null, $delete = false)
    {
        if ($builder->getModel()->hasDelFlag()) {
            if ($delete) {
                return $builder->getModel()->getUpdateColumnsWhenHasDelFlag(true);
            }
            return [$builder->getModel()->getDeleteFlagColumn() => $builder->getModel()->getDelFlagValue()];
        }
        return [$column ? $column : $builder->getModel()->getDeletedAtColumn() => $value];
    }
}
