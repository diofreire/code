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
