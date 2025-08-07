<?php
require APPPATH . 'libraries/REST_Controller.php';;
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\RESTController;

class Auth extends RESTController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['login', 'jwt']);
    }

    // POST /auth/login
    public function login_post()
    {
        $email = $this->post('email');
        $senha = $this->post('senha');

        $result = process_login($email, $senha);

        if ($result['status']) {
            $this->response(
                ['status' => true, 'token' => $result['token']],
                $result['http_code']
            );
        } else {
            $this->response(
                ['status' => false, 'message' => $result['message']],
                $result['http_code']
            );
        }
    }
}