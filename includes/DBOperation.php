<?php
/**
 * Created by PhpStorm.
 * User: jamessunley
 * Date: 06/02/2018
 * Time: 14:41
 */

class DbOperation
{
    private $conn;

    //Constructor
    function __construct()
    {
        require_once dirname(__FILE__) . '/constants.php';
        require_once dirname(__FILE__) . '/DBConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function userLogin($username, $pass)
    {
        $password = md5($pass);
        $stmt = $this->conn->prepare("SELECT id FROM LogIn WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getUserByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT id, username, trainer FROM LogIn WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $uname, $trainer);
        $stmt->fetch();
        $user = array();
        $user['id'] = $id;
        $user['username'] = $uname;
        $user['trainer'] = $trainer;
        return $user;
    }

    //Function to create a new user
    public function createUser($username, $pass, $trainer, $firstName, $surname, $email, $dob, $goal, $weight, $height)
    {
        if (!$this->isUserExist($username)) {
            $password = md5($pass);
            $stmt = $this->conn->prepare("INSERT INTO LogIn (username, password, trainer) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $trainer);

            if ($stmt->execute()) {

                $user = $this->getUserByUsername($username);

                if ($user["trainer"] == 0) {
                    $stmt2 = $this->conn->prepare("INSERT INTO Client(client_id, client_first_name, client_surname, client_email, client_dob, 
                    client_goal, client_weight, client_height, number_of_cancellations)VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
                    $stmt2->bind_param("ssssssss", $user["id"], $firstName, $surname, $email, $dob, $goal, $weight, $height);

                    $stmt2->execute();
                }else if ($user ["trainer"] == 1){
                    $stmt3 = $this->conn->prepare("INSERT INTO Trainer(trainer_id, trainer_first_name, trainer_surname, trainer_dob, trainer_price, 
                    trainer_email, trainer_weight, trainer_height)VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt3->bind_param("ssssssss", $user["id"], $firstName, $surname, $dob, $goal, $email, $weight, $height);
                    $stmt3->execute();

                }
                return USER_CREATED;
            } else {
                return USER_NOT_CREATED;
            }
        } else {
            return USER_ALREADY_EXIST;
        }
    }


    private function isUserExist($username)
    {
        $stmt = $this->conn->prepare("SELECT id FROM LogIn WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
