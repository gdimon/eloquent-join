<?php

namespace Gdimon;

trait EloquentJoin {
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new EloquentJoin\Builder\QueryJoinBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

    public function newEloquentBuilder($query)
    {
        $newEloquentBuilder = new EloquentJoin\Builder\EloquentJoinBuilder($query);
        return $newEloquentBuilder;
    }

}