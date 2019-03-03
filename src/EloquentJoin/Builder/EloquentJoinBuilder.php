<?php

namespace Gdimon\EloquentJoin\Builder;

use Illuminate\Database\Eloquent\Builder;

class EloquentJoinBuilder extends Builder
{
    /**
     * @var QueryJoinBuilder
     */
    protected $query;

    protected function getColumns($table)
    {
        $columns = $this->model->getConnection()->getSchemaBuilder()->getColumnListing($table);
        return $columns;
    }

    public function joinRelation($relation, $closure = null, $type = 'left')
    {
        $relations = explode('.', $relation);

        $relation = $this;

        $lastIndex = count($relations)-1;
        $relatedAliases = [];

        foreach($relations as $k=>$joinTable) {
            $model = $relation->getModel();
            $table = $k ? $relatedAlias : $model->getTable();

            $relatedAliases []= $joinTable;

            $relation = $model->$joinTable();

            $relatedModel = $relation->getRelated();
            $relatedTable = $relatedModel->getTable();

            if (is_null($this->query->columns)) {
                $this->query->addSelect($table . '.*');
            } else if (!$this->query->getAlias()) {
                foreach ($this->query->columns as &$column) {
                    if (strpos('.', $column) === false) {
                        $column = $table . '.' . $column;
                    }
                }
                unset($column);
            }

            $relatedAlias = implode('_', $relatedAliases);
            $this->query->setAlias($relatedAlias);
            if($k==$lastIndex) {
                if ($closure instanceof \Closure) {
                    $closure($this->query);
                } else if (is_null($closure)) {
                    $columns = $this->getColumns($relatedTable);
                    foreach ($columns as $column) {
                        $this->query->addSelect($relatedTable . '.' . $column . ' AS ' . $relatedTable . '_' . $column);
                    }
                }
            }

            $relatedAlias = $this->query->getAlias();

            $ownerKey = $relatedAlias . '.' . $relation->getOwnerKeyName();
            $relatedKey = $table . '.' . $relation->getForeignKeyName();

            $this->join($relatedTable . ' AS ' . $relatedAlias, $ownerKey, '=', $relatedKey, $type);
        }

        return $this;
    }
}