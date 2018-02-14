<?php
/**
 * Created by PhpStorm.
 * User: jamessunley
 * Date: 06/02/2018
 * Time: 14:42
 */
//importing required script
require_once 'DbOperation.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyRequiredParams(array('username', 'password', 'trainer', 'first', 'surname', 'email', 'dob', 'goal', 'weight', 'height'))) {
        //getting values
        $username = $_POST['username'];
        $password = $_POST['password'];
        $trainer = $_POST['trainer'];
        $first = $_POST['first'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $goal = $_POST['goal'];
        $weight = $_POST['weight'];
        $height = $_POST['height'];

        //creating db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->createUser($username, $password, $trainer, $first, $surname, $email, $dob, $goal, $weight, $height);

        //making the response accordingly
        if ($result == USER_CREATED) {
            $response['error'] = false;
            $response['message'] = 'Your account has been created';
        } elseif ($result == USER_ALREADY_EXIST) {
            $response['error'] = true;
            $response['message'] = 'User already exist. Please try again';
        } elseif ($result == USER_NOT_CREATED) {
            $response['error'] = true;
            $response['message'] = 'Sorry, your request could not be processed at this time.';
        }elseif ($result == LOGIN_CREATED_USER_NOT_CREATED){
            $response['error'] = true;
            $response['message'] = 'LogIn Created, Profile could not be created.';
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Please enter all fields';
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request';
}

//function to validate the required parameter in request
function verifyRequiredParams($required_fields)
{

    //Getting the request parameters
    $request_params = $_REQUEST;

    //Looping through all the parameters
    foreach ($required_fields as $field) {
        //if any requred parameter is missing
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {

            //returning true;
            return true;
        }
    }
    return false;
}

echo json_encode($response);
