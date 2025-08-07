<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function create_jwt($data)
{
    $CI =& get_instance();
    $CI->load->config('jwt');
    $issuedAt = time();
    $expiration = $issuedAt + $CI->config->item('jwt_expiration');

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expiration,
        'data' => $data
    ];

    return JWT::encode($payload, $CI->config->item('jwt_key'), $CI->config->item('jwt_algorithm'));
}

function validate_jwt($token)
{
    $CI =& get_instance();
    $CI->load->config('jwt');

    try {
        $decoded = JWT::decode($token, new Key($CI->config->item('jwt_key'), $CI->config->item('jwt_algorithm')));
        return $decoded->data;
    } catch (Exception $e) {
        return false;
    }
}

function get_bearer_token() {
    $ci = get_instance();
    $headers = $ci->input->request_headers();

    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function verify_jwt_token() {
    $ci = get_instance();
    $token = get_bearer_token();

    if (!$token) {
        return null; // ou lance erro de token ausente
    }

    try {
        $key = 'sua_chave_secreta'; // use a chave correta do seu projeto
        $decoded = \Firebase\JWT\JWT::decode($token, $key, ['HS256']);
        return $decoded; // objeto com os dados do payload
    } catch (Exception $e) {
        return null; // token inválido ou expirado
    }
}
