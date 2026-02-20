<?php
require_once '../models/GenPassword.php';


class GenPassResource
{

    //GET /api/genpass?length=16&upper=true&lower=true&digits=true&symbols=true&avoid_ambiguous=true&exclude=abAB12&require_each=true
    public function get()
    {

        $length = isset($_GET['length']) ? (int)$_GET['length'] : 16;
        $opts = [
            'upper' => isset($_GET['upper']) ? filter_var($_GET['upper'], FILTER_VALIDATE_BOOLEAN) : true,
            'lower' => isset($_GET['lower']) ? filter_var($_GET['lower'], FILTER_VALIDATE_BOOLEAN) : true,
            'digits' => isset($_GET['digits']) ? filter_var($_GET['digits'], FILTER_VALIDATE_BOOLEAN) : true,
            'symbols' => isset($_GET['symbols']) ? filter_var($_GET['symbols'], FILTER_VALIDATE_BOOLEAN) : true,
            'avoid_ambiguous' => isset($_GET['avoid_ambiguous']) ? filter_var($_GET['avoid_ambiguous'], FILTER_VALIDATE_BOOLEAN) : true,
            'exclude' => isset($_GET['exclude']) ? $_GET['exclude'] : '',
            'require_each' => isset($_GET['require_each']) ? filter_var($_GET['require_each'], FILTER_VALIDATE_BOOLEAN) : true
        ];

        try {
            $password = generate_password($length, $opts);
            echo $password;
            return ['password' => $password];
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }
}

?>