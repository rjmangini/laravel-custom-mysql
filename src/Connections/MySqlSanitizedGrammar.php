<?php

namespace rjmangini\Connections;

use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Builder;

class MySqlSanitizedGrammar extends MySqlGrammar
{
    public function compileSelect( Builder $query )
    {
        $sanitizedFrom = $this->sanitizeValue( $query->from, true );
        // joins should be done correctly

        $this->sanitizeColumns( $query, $sanitizedFrom );
        $this->sanitizeWhere( $query, $sanitizedFrom );
        $this->sanitizeGroupBy( $query, $sanitizedFrom );
        $this->sanitizeHaving( $query, $sanitizedFrom );
        $this->sanitizeOrderBy( $query, $sanitizedFrom );

        return parent::compileSelect( $query );
    }

    protected function sanitizeColumns( Builder $query, $from )
    {
        if (empty( $query->columns )) {
            $query->columns = [ '*' ];
        }

        foreach ($query->columns as $key => $column) {
            $query->columns[ $key ] = $this->sanitizeColumn( $column, $from );
        }
    }

    protected function sanitizeWhere( Builder $query, $from )
    {
        if (empty( $query->wheres )) {
            return;
        }

        foreach ($query->wheres as $key => $where) {
            if (isset( $where[ 'column' ] )) {
                $query->wheres[ $key ][ 'column' ] = $this->sanitizeColumn( $where[ 'column' ], $from );
            }

            if (isset( $where[ 'value' ] )) {
                $query->wheres[ $key ][ 'value' ] = $this->sanitizeColumn( $where[ 'value' ], $from );
            }
        }
    }

    protected function sanitizeGroupBy( Builder $query, $from )
    {
        if (empty( $query->groups )) {
            return;
        }

        foreach ($query->groups as $key => $group) {
            $query->groups[ $key ] = $this->sanitizeColumn( $group, $from );
        }
    }

    protected function sanitizeHaving( Builder $query, $from )
    {
        if (empty( $query->havings )) {
            return;
        }

        foreach ($query->havings as $key => $having) {
            if (isset( $having[ 'column' ] )) {
                $query->havings[ $key ][ 'column' ] = $this->sanitizeColumn( $having[ 'column' ], $from );
            }

            if (isset( $having[ 'value' ] )) {
                $query->havings[ $key ][ 'value' ] = $this->sanitizeColumn( $having[ 'value' ], $from );
            }
        }
    }

    protected function sanitizeOrderBy( Builder $query, $from )
    {
        if (empty( $query->orders )) {
            return;
        }

        foreach ($query->orders as $key => $order) {
            if (isset( $order[ 'column' ] )) {
                $query->orders[ $key ][ 'column' ] = $this->sanitizeColumn( $order[ 'column' ], $from );
            }
        }
    }

    protected function isSimpleValue( $value )
    {
        return is_string( $value )
               && ( !!preg_match( '/^[a-z_][a-z0-9_]+$/', strtolower( trim( $value ) ) )
                    || $value === '*' );
    }

    protected function sanitizeColumn( $column, $from )
    {
        $value = $this->sanitizeValue( $column );

        if ($this->isSimpleValue( $value )) {
            return $from . '.' . $value;
        }

        return $column;
    }

    protected function sanitizeValue( $original, $extractAs = false )
    {
        $value = $this->isExpression( $original ) ? $this->getValue( $original ) : $original;

        if ($extractAs && is_string( $value ) && strpos( strtolower( $value ), ' as ' ) !== false) {
            $segments = explode( ' as ', $value );

            $value = array_pop( $segments );
        }

        if ($this->isSimpleValue( $value )) {
            return $value;
        }

        return $original;
    }
}
