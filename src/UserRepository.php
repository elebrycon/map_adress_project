<?php

//Keep the SQL for reading and saving users in one place.
class UserRepository{
    
    private PDO $connection;

    //Receiving PDO from outside.
    public function __construct(PDO $connection){
        $this->connection = $connection;
    }

    //Return a list of saved users.
    public function findAll(): array{
        $statement = $this->connection->query(
            'SELECT id, first_name, last_name, street_number, city, country
             FROM users ORDER BY id DESC'
        );
        return $statement->fetchAll();
    }

    //Save one validated user.
    public function create(array $data): void{
        //Prepare SQL with placeholders.
        $statement = $this->connection->prepare(
            'INSERT INTO users (first_name, last_name, street_number, city, country)
             VALUES (:first_name, :last_name, :street_number, :city, :country)'
        );

        //Supply values separately so they are treated as data, not SQL instructions.
        $statement->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'street_number' => $data['street_number'],
            'city' => $data['city'],
            'country' => $data['country']
        ]);
    }
}
