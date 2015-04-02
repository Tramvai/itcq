<?php
require_once('db_connect.php');
restrictFunctionToAccount("admin");

switch ($_POST['request']) {
    case 'saveImage':
        saveImage();
    break;

    case 'deleteImage':
        deleteImage();
    break;

    case 'uploadTemp':
        uploadTemp();
    break;

    default:
        returnError("Request not defined.");
    break;
}

function saveImage() {
    if (!isset($_POST['questionId'])) {
        returnError("Question ID not set.");
    } else {
        //if (!unlink('../img/questions/'.$_POST['questionId'].'.jpg')) returnError("Failed to delete the old file.");
        if (!rename('../img/questions/'.$_POST['questionId'].'_temp.jpg', '../img/questions/'.$_POST['questionId'].'.jpg')) returnError("Renaming new file failed");

        $connection = connectToDatabase();
        $unpreparedSQL = "UPDATE questions SET has_image = 1 WHERE id = :id";
        $query = $connection->prepare($unpreparedSQL);
        $query->bindParam(':id', $_POST['questionId']);
        $query->execute();
    }
}

function deleteImage() {
    if (!isset($_POST['questionId'])) {
        returnError("Question ID not set.");
    } else {
        if (!unlink('../img/questions/'.$_POST['questionId'].'.jpg')) returnError("Failed to delete the file.");

        $connection = connectToDatabase();
        $unpreparedSQL = "UPDATE questions SET has_image = 0 WHERE id = :id";
        $query = $connection->prepare($unpreparedSQL);
        $query->bindParam(':id', $_POST['questionId']);
        $query->execute();
    }
}

function uploadTemp() {
    if (!isset($_FILES['file']['error']) || is_array($_FILES['file']['error'])) {
        returnError("Invalid parameters.");
    } else if ($_FILES['file']['error'] == 0) {
        if ($_FILES['file']['type'] != 'image/jpeg') returnError("Image type not supported.");
        else if ($_FILES['file']['size'] > 500000) returnError("Image too big.");
        else if (!isset($_POST['questionId'])) returnError("Question ID not set.");
        else if (!is_numeric($_POST['questionId'])) returnError("Question ID not a number.");
        else {
            if (!move_uploaded_file($_FILES['file']['tmp_name'], '../img/questions/'.$_POST['questionId'].'_temp.jpg')) {
                returnError("Failed to move file.");
            } else {
                returnSuccess("Image uploaded");
            }
        }
    } else {
        returnError("Errors found.");
    }
}

function returnSuccess($message) {
    http_response_code(200);
    die(json_encode(array('success' => $message)));
}

function restrictFunctionToAccount($account) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['account']) || $_SESSION['account'] != $account) returnError("Not authorized to query this.");
}
