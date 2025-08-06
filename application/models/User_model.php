<?php

class User_model extends CI_Model
{
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

    public function create($data)
    {
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
