<?php
require_once '../models/GenPassword.php';


class GenPassResource
{
    private $generator;
    public function __construct()
    {
        $generator = new PasswordGenerator();
    }

    //GET /api/password?length=16&upper=true&lower=true&digits=true&symbols=true&avoid_ambiguous=true&exclude=abAB12&require_each=true
    public function get()
    {
        $this->generator->length = isset($_GET['length']) ? (int)$_GET['length'] : 16;
        $this->generator->upper_enabled = isset($_GET['upper']) ? filter_var($_GET['upper'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->lower_enabled = isset($_GET['lower']) ? filter_var($_GET['lower'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->digits_enabled = isset($_GET['digits']) ? filter_var($_GET['digits'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->symbols_enabled = isset($_GET['symbols']) ? filter_var($_GET['symbols'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->avoid_ambiguous = isset($_GET['avoid_ambiguous']) ? filter_var($_GET['avoid_ambiguous'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->exclude = isset($_GET['exclude']) ? $_GET['exclude'] : '';
        $this->generator->require_each = isset($_GET['require_each']) ? filter_var($_GET['require_each'], FILTER_VALIDATE_BOOLEAN) : true;

        try {
            $password = $this->generator->generate_password();
            http_response_code(200);
            echo json_encode(['password' => $password]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getMultiple()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $this->generator->count = !empty($data['count']) ? (int)$data['count'] : 1;
        $this->generator->length = !empty($data['length']) ? (int)$data['length'] : 16;
        $this->generator->upper_enabled = !empty($data['upper']) ? filter_var($data['upper'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->lower_enabled = !empty($data['lower']) ? filter_var($data['lower'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->digits_enabled = !empty($data['digits']) ? filter_var($data['digits'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->symbols_enabled = !empty($data['symbols']) ? filter_var($data['symbols'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->avoid_ambiguous = !empty($data['avoid_ambiguous']) ? filter_var($data['avoid_ambiguous'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->generator->exclude = !empty($data['exclude']) ? $data['exclude'] : '';
        $this->generator->require_each = !empty($data['require_each']) ? filter_var($data['require_each'], FILTER_VALIDATE_BOOLEAN) : true;

        try {
            $passwords = $this->generator->generateMultiple();
            http_response_code(200);
            echo json_encode(['passwords' => $passwords]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

?>