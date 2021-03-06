<?php

require_once "config_bd.php";
include '../entity/ServerResponse.php';
include 'const.php';

try {
    $post = file_get_contents('php://input');

    $arr = json_decode($post, true);
    $log = "GIVE_CONCESSION : ";
    file_put_contents('log.txt', $post, FILE_APPEND);
    $response = new ServerResponse();

    $renew_state = CONST_ACCEPT_STATE;
    $expireDate =  date('Y-m-d H-i-s', strtotime('+370 day'));

    $renewConcession = 1;
    $renewConcessionDate = date('Y-m-d H-i-s');
    $renewConcessionBy = CONST_CREATEDBY;

    if (isset($_POST['idConcession'])) {

        $sql = "UPDATE concession 
                SET state = :state, 
                expireDate = :expireDate,
                renewConcession = :renewConcession,
                renewConcessionDate = :renewConcessionDate,
                renewConcessionBy = :renewConcessionBy
                WHERE id = :idConcession";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":state", $renew_state);
            $stmt->bindParam(":expireDate", $expireDate);
            $stmt->bindParam(":renewConcession", $renewConcession);
            $stmt->bindParam(":renewConcessionDate", $renewConcessionDate);
            $stmt->bindParam(":renewConcessionBy", $renewConcessionBy);
            $stmt->bindParam(":idConcession", $_POST['idConcession']);


            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $log .= "succes";
                // Records created successfully. Redirect to landing page
                $response->setSucces(true);
                $response->setMessage("Concession given");
            } else {
                $response->setSucces(false);
                $response->setMessage("Error while giving");
            }
            unset($stmt);
        }
    } else {
        $log .= "Missing info";

        $response->setSucces(false);
        $response->setMessage("All mandatory fields must be complete");
    }

    //file_put_contents('log.txt', $log . "\n", FILE_APPEND);
    echo json_encode($response);

} catch (Exception $e) {

    file_put_contents('log.txt', "erreur_giving_concession : " . $e . "\n", FILE_APPEND);
}
