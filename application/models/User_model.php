<?php

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['security', 'jwt']);
    }

    public function get_all()
    {
        return $this->db->get('users')->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row_array();
    }

    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row_array();
    }

    /**
     * Cria um novo usuário.
     * @param array $data ['nome' => string, 'email' => string, 'senha' => string (hashed)]
     * @return bool true se inseriu, false caso contrário
     */
    public function create($data)
    {
        // Verifica se email já existe
        $this->db->where('email', $data['email']);
        $exists = $this->db->get('users')->row();

        if ($exists) {
            return false; // email já cadastrado
        }

        // Insere novo usuário
        return $this->db->insert('users', $data);
    }

    public function update($id, $data)
    {
        return $this->db->update('users', $data, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete('users', ['id' => $id]);
    }
}
