<?php
require APPPATH . 'libraries/REST_Controller.php';;
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\RESTController;

class Usuario extends RESTController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model');
        $this->load->helper(['security', 'jwt', 'auth']);
    }

    // CRUD protegido
    public function users_get() {
        $this->verify_request();
        // Verifica se admin
        $result = get_logged_user();
        if($result['admin'] == '1') {
            $users = $this->User_model->get_all();
            $this->response($users, RESTController::HTTP_OK);
        } else {
            $this->response(
                ['status' => false, 'message' => 'Usuário não autorizado'],
                RESTController::HTTP_UNAUTHORIZED
            );
        }
    }

    public function users_post() {
        // Verifica se está logado
        $this->verify_request();

        $nome = $this->security->xss_clean($this->post('nome'));
        $email = $this->security->xss_clean($this->post('email'));
        $senha = $this->security->xss_clean($this->post('senha'));

        if (!$nome || !$email || !$senha) {
            $this->response(
                ['status' => false, 'message' => 'Todos os campos são obrigatórios'],
                RESTController::HTTP_BAD_REQUEST
            );
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response(
                ['status' => false, 'message' => 'Email inválido'],
                RESTController::HTTP_BAD_REQUEST
            );
            return;
        }

        $data = [
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_BCRYPT)
        ];

        if ($this->User_model->create($data)) {
            $this->response(
                ['status' => true, 'message' => 'Usuário criado com sucesso'],
                RESTController::HTTP_CREATED);
        } else {
            $this->response(
                ['status' => false, 'message' => 'Erro ao criar usuário (email já existe?)'],
                RESTController::HTTP_INTERNAL_ERROR
            );
        }
    }

    public function users_put($id) {
        $this->verify_request();

        // Verifica se o ID informado é do usuário
        // Caso não seja, verifica se é admin e permite a alteração;
        $result = get_logged_user();
        if($result['id'] != $id && $result['admin'] != '1') {
            $this->response(
                ['status' => false, 'message' => 'Ação não autorizada'],
                RESTController::HTTP_UNAUTHORIZED
            );
        }

        $nome = $this->security->xss_clean($this->put('nome'));
        $email = $this->security->xss_clean($this->put('email'));
        $senha = $this->security->xss_clean($this->put('senha'));

        if (!$nome && !$email && !$senha) {
            $this->response(
                ['status' => false, 'message' => 'Nenhum dado para atualizar'],
                RESTController::HTTP_BAD_REQUEST
            );
            return;
        }

        $data = [];
        if ($nome) $data['nome'] = $nome;
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) $data['email'] = $email;
        if ($senha) $data['senha'] = password_hash($senha, PASSWORD_BCRYPT);

        if ($this->User_model->update_user($id, $data)) {
            $this->response(
                ['status' => true, 'message' => 'Usuário atualizado'],
                RESTController::HTTP_OK);
        } else {
            $this->response(
                ['status' => false, 'message' => 'Erro ao atualizar usuário'],
                RESTController::HTTP_BAD_REQUEST
            );
        }
    }

    public function users_delete($id) {
        $this->verify_request();
        // Verifica se admin
        $result = get_logged_user();
        if($result['admin'] != '1') {
            $this->response(
                ['status' => false, 'message' => 'Usuário não autorizado'],
                RESTController::HTTP_UNAUTHORIZED
            );
        }

        if ($this->User_model->delete_user($id)) {
            $this->response(
                ['status' => true, 'message' => 'Usuário deletado'],
                RESTController::HTTP_OK
            );
        } else {
            $this->response(
                ['status' => false, 'message' => 'Erro ao deletar usuário'],
                RESTController::HTTP_NOT_FOUND
            );
        }
    }

    private function verify_request() {
        $authHeader = $this->input->get_request_header('Authorization');
        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            $this->response(
                ['status' => false, 'message' => 'Token não fornecido'],
                RESTController::HTTP_UNAUTHORIZED
            );
            exit;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $data = validate_jwt($token);

        if (!$data) {
            $this->response(
                ['status' => false, 'message' => 'Token inválido ou expirado'],
                RESTController::HTTP_UNAUTHORIZED
            );
            exit;
        }

        return $data;
    }
}