<?php

namespace Gdimon\EloquentJoin\Builder;

use Illuminate\Database\Query\Builder as Builder;

class QueryJoinBuilder extends Builder
{
    protected $alias = false;

    function setAlias($alias = false)
    {
        $this->alias= $alias;
        return $this;
    }

    function getAlias()
    {
        return $this->alias;
    }

    function addSelect($column)
    {
        if($this->alias && strpos($column, '.') === false) {
            $column = $this->alias .'.'.$column;
        }
        return parent::addSelect($column);
    }
}