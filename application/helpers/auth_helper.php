<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Restserver\Libraries\RESTController;

/**
 * Função de processo de login
 * @param $email
 * @param $senha
 * @return array
 */
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

/**
 * Função para recuperar dados do usuário logado
 * @return mixed|null
 */
function get_logged_user() {
    $CI =& get_instance();
    $token = get_bearer_token();

    if (!$token) {
        $this->response([
            'status' => false,
            'message' => 'Token não fornecido'
        ], RESTController::HTTP_UNAUTHORIZED);
        return null;
    }

    $decoded = decode_jwt_token($token);

    if (!$decoded) {
        $CI->response([
            'status' => false,
            'message' => 'Token inválido ou expirado'
        ], RESTController::HTTP_UNAUTHORIZED);
        return null;
    }

    // Assumindo que o payload tem 'id' ou 'email'
    $user_id = $decoded->id ?? null;
    $email = $decoded->email ?? null;

    if (!$user_id && !$email) {
        $CI->response([
            'status' => false,
            'message' => 'Token sem dados de usuário'
        ], RESTController::HTTP_UNAUTHORIZED);
        return null;
    }

    $CI->load->model('User_model');

    if ($user_id) {
        $user = $CI->User_model->get_by_id($user_id);
    } else {
        $user = $CI->User_model->get_by_email($email);
    }

    if (!$user) {
        $CI->response([
            'status' => false,
            'message' => 'Usuário não encontrado'
        ], RESTController::HTTP_UNAUTHORIZED);
        return null;
    }

    return $user; // array ou objeto com dados do usuário
}