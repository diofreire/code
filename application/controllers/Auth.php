<?php
require APPPATH . 'libraries/REST_Controller.php';;
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\RESTController;

class Auth extends RESTController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model');
        $this->load->helper(['security', 'jwt']);
    }

    // POST /auth/login
    public function login_post()
    {
        $email = $this->security->xss_clean($this->post('email'));
        $senha = $this->security->xss_clean($this->post('senha'));

        if (!$email || !$senha) {
            $this->response(
                [
                    'status' => false,
                    'message' => 'Email e senha são obrigatórios'
                ],
                RESTController::HTTP_BAD_REQUEST
            );
            return;
        }

        $user = $this->User_model->get_by_email($email);

        if ($user && password_verify($senha, $user['senha'])) {
            $token = create_jwt([
                'id' => $user['id'],
                'email' => $user['email']
            ]);

            $this->response(
                [
                    'status' => true,
                    'token' => $token
                ],
                RESTController::HTTP_OK
            );
        } else {
            $this->response(
                [
                    'status' => false,
                    'message' => 'Credenciais inválidas'
                ],
                RESTController::HTTP_UNAUTHORIZED
            );
        }
    }
}