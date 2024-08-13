<?php
namespace Core;
date_default_timezone_set('Europe/Paris');

require __DIR__ . '/../config.php';
class DB
{
    private static ?DB $instance = null;
    private \PDO $pdo;
    private string $table;

    public function __construct() {
        $dbname = DB_NAME;
        $dbuser = DB_USER;
        $dbpassword = DB_PASSWORD;
        $dbhost = DB_HOST;
        $dbport = DB_PORT;

        try{
            $this->pdo = new \PDO("pgsql:host=$dbhost;port=$dbport;dbname=$dbname;user=$dbuser;password=$dbpassword");
        }catch (\PDOException $e) {
            echo "Erreur SQL : ".$e->getMessage();
        }

        $table = get_called_class(); //pour récupérer le nom de la classe qui a appelé la méthode
        $table = explode("\\", $table); //pour séparer le namespace du nom de la classe
        $table = array_pop($table); //pour récupérer le nom de la classe
        $this->table = TABLE_PREFIX."_".strtolower($table); //pour mettre le nom de la table en minuscule et ajouter le préfixe gfm_
    }

    public static function getInstance(): DB {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    // Empêcher le clonage de l'instance
    private function __clone() {}

    // Empêcher la désérialisation de l'instance
    public function __wakeup() {}

    public function getAllData(string $return = "array") //pour récupérer tous les enregistrements de la bdd

    {
        $sql = "SELECT * FROM " . $this->table;
        $queryPrepared = $this->pdo->prepare($sql);
        $queryPrepared->execute();

        if($return == "object") {
            // les resultats seront sous forme d'objet de la classe appelée
            $queryPrepared->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        } else {
            // pour récupérer un tableau associatif
            $queryPrepared->setFetchMode(\PDO::FETCH_ASSOC);
        }

        return $queryPrepared->fetchAll();
    }


    public function getPublishedPost($type, $id = null)
    {
        $sql = "SELECT * FROM gfm_post WHERE type = :type and isdeleted = 0 and published = 1";
        $params = [':type' => $type];

        if ($id !== null) {
            $sql .= " AND id = :id";
            $params[':id'] = $id;
        }

        $queryPrepared = $this->pdo->prepare($sql);
        $queryPrepared->execute($params);

        return $queryPrepared->fetchAll();
    }

    public function getDataObject(): array //pour récupérer les données de l'objet
    {
        return array_diff_key(get_object_vars($this), get_class_vars(get_class())); //mettre dans un tableau les données de l'objet
    }

    public function setDataFromArray(array $data): void //pour mettre à jour les données de l'objet
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function save() //pour insérer ou mettre à jour les données de l'objet dans la bdd
    {
        $data = $this->getDataObject();

        if(empty($this->getId())){ //si l'id est vide, on insère
            unset($data['id']);
            $sql = "INSERT INTO " . $this->table . "(" . implode(",", array_keys($data)) . ")
            VALUES (:" . implode(",:", array_keys($data)) . ")";
        } else { //sinon on met à jour
            $isUpdate = true;
            $sql = "UPDATE " . $this->table . " SET ";
            foreach ($data as $column => $value){
                $sql.= $column. "=:".$column. ",";
            }
            $sql = substr($sql, 0, -1);
            $sql.= " WHERE id = ".$this->getId();
        }

        $queryPrepared = $this->pdo->prepare($sql); //pour préparer la requête
        //pour associer les valeurs aux paramètres de la requête préparée
        foreach ($data as $key => $value) {
            $type = is_bool($value) ? \PDO::PARAM_BOOL : (is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            $queryPrepared->bindValue(":$key", $value, $type);
        }
        $queryPrepared->execute($data); //pour exécuter la requête
        if (isset($isUpdate)) {
            return $this->getId();
        }
        return $this->pdo->lastInsertId($this->table."_id_seq");
    }

    public static function populate(int $id): object
    {
        $class = get_called_class();
        $object = new $class();
        return $object->getOneBy(["id"=>$id], "object");
    }

    public function getOneBy(array $data, string $return = "array")
    {
        $sql = "SELECT * FROM ".$this->table. " WHERE ";
        foreach ($data as $column => $value) {
            $sql .= " ".$column."=:".$column. " AND";
        }
        $sql = substr($sql, 0, -3); // pour enlever le dernier AND
        $queryPrepared = $this->pdo->prepare($sql); // pour préparer la requête
        $queryPrepared->execute($data); // pour exécuter la requête

        if($return == "object") {
            // les resultats seront sous forme d'objet de la classe appelée
            $queryPrepared->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        } else {
            // pour récupérer un tableau associatif
            $queryPrepared->setFetchMode(\PDO::FETCH_ASSOC);
        }

        return $queryPrepared->fetch(); // pour récupérer le résultat de la requête (un seul enregistrement)
    }

    public function delete(array $data)
    {
        // Use the getOneBy function to find the record to delete
        $recordToDelete = $this->getOneBy($data);

        if (!$recordToDelete) {
            // The record to delete doesn't exist
            return false;
        }

        // Build the DELETE SQL statement
        $sql = "DELETE FROM " . $this->table . " WHERE ";
        foreach ($data as $column => $value) {
            $sql .= " " . $column . "=:" . $column . " AND";
        }
        $sql = substr($sql, 0, -3); // Remove the last AND

        // Prepare and execute the DELETE query
        $queryPrepared = $this->pdo->prepare($sql);
        $queryPrepared->execute($data);

        // Check if the record was successfully deleted
        return $queryPrepared->rowCount() > 0;
    }

    public function checkUserCredentials(string $email, string $password): ?object
    {
        $user = $this->getOneBy(['email' => $email], 'object');
        if ($user && password_verify($password, $user->getPwd())) {
            return $user;
        }
        return null;
    }

    public function countElements($typeColumn = null, $typeValue = null): int {
        if ($typeColumn && $typeValue) {
            // Compter seulement les éléments d'un type spécifique
            $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE " . $typeColumn . " = :typeValue";
            $queryPrepared = $this->pdo->prepare($sql);
            $queryPrepared->execute(['typeValue' => $typeValue]);
        } else {
            // Compter tous les éléments si aucun type n'est spécifié
            $sql = "SELECT COUNT(*) FROM " . $this->table;
            $queryPrepared = $this->pdo->prepare($sql);
            $queryPrepared->execute();
        }

        return $queryPrepared->fetchColumn();
    }

    public function emailExists($email): bool {
        $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE email = :email";
        $queryPrepared = $this->pdo->prepare($sql);
        $queryPrepared->execute(['email' => $email]);
        $number = $queryPrepared->fetchColumn();

        return $number > 0;
    }

}
