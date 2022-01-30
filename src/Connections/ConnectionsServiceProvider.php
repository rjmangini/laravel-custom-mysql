<?php

namespace rjmangini\Connections;

use Illuminate\Database\Connectors\MySqlConnector;
use Illuminate\Support\ServiceProvider;

class ConnectionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerArchiveConnection();
        $this->registerCustomMySqlConnection();
    }

    public function registerArchiveConnection()
    {
        $this->app->singleton(
            'db.connector.mysql-archive',
            function () {
                return new MysqlConnector;
            }
        );

        // Register the MySql connection class as a singleton
        // because we only want to have one, and only one,
        // MySql database connection at the same time.
        $this->app->singleton(
            'db.connection.mysql-archive',
            function ( $app, $parameters ) {
                // First, we list the passes parameters into single
                // variables. I do this because it is far easier
                // to read than using it as eg $parameters[0].
                list( $connection, $database, $prefix, $config ) = $parameters;

                // Next we can initialize the connection.
                return new MySqlArchiveConnection( $connection, $database, $prefix, $config );
            }
        );
    }

    public function registerCustomMySqlConnection()
    {
        $this->app->singleton(
            'db.connector.mysql-sanitized',
            function () {
                return new MysqlConnector;
            }
        );

        // Register the MySql connection class as a singleton
        // because we only want to have one, and only one,
        // MySql database connection at the same time.
        $this->app->singleton(
            'db.connection.mysql-sanitized',
            function ( $app, $parameters ) {
                // First, we list the passes parameters into single
                // variables. I do this because it is far easier
                // to read than using it as eg $parameters[0].
                list( $connection, $database, $prefix, $config ) = $parameters;

                // Next we can initialize the connection.
                return new MySqlSanitizedConnection( $connection, $database, $prefix, $config );
            }
        );
    }
}
