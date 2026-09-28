<?php

//Provide a reusable PDO connection.
class Database{
    //?PDO permits either a PDO connection or no connection.
    private static ?PDO $connection = null;

    //Call this without creating a Database object. This returns a PDO connection.
    public static function getConnection(): PDO{
        //Connect only if this request has no connection yet.
        if(self::$connection === null){
            // Local MySQL connection settings.
            $config = require dirname(__DIR__) . '/config.php';
            $host = $config['DB_HOST'];
            $database = $config['DB_NAME'];
            $username = $config['DB_USER'];
            $password = $config['DB_PASSWORD'];

            //The DSN tells PDO which driver, server, database, and text encoding to use.
            $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

            //Open the connection.
            self::$connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    //Let callers handle database failures with try/catch.
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    //Return rows with column names as keys, such as first_name.
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    //Use MySQL's prepared statements.
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }
        //Reuse this connection within this request.
        return self::$connection;
    }
}
