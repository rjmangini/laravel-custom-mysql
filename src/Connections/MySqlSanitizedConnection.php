<?php

namespace rjmangini\Connections;

use Illuminate\Database\MySqlConnection;

class MySqlSanitizedConnection extends MySqlConnection
{
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix( new MySqlSanitizedGrammar );
    }
}
