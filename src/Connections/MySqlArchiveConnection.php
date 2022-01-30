<?php

namespace rjmangini\Connections;

use Illuminate\Database\MySqlConnection;

class MySqlArchiveConnection extends MySqlConnection
{
    public function insert( $query, $bindings = [ ] )
    {
        $query = preg_replace( '/insert into /i', 'insert delayed into ', $query );

        return parent::insert( $query, $bindings );
    }
}
