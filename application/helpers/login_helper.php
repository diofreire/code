<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Restserver\Libraries\RESTController;

function process_login($email, $senha) {
    $CI =& get_instance();
    $CI->load->database();
    $CI->load->model('User_model');
    $CI->load->helper('security');

    $email = $CI->security->xss_clean($email);
    $senha = $CI->security->xss_clean($senha);

    if (!$email || !$senha) {
        return [
            'status' => false,
            'message' => 'Email e senha são obrigatórios',
            'http_code' => RESTController::HTTP_BAD_REQUEST
        ];
    }

    $user = $CI->User_model->get_by_email($email);

    if ($user && password_verify($senha, $user['senha'])) {
        $token = create_jwt(['id' => $user['id'], 'email' => $user['email']]);
        return [
            'status' => true,
            'token' => $token,
            'http_code' => RESTController::HTTP_OK
        ];
    } else {
        return [
            'status' => false,
            'message' => 'Credenciais inválidas',
            'http_code' => RESTController::HTTP_UNAUTHORIZED
        ];
    }
}