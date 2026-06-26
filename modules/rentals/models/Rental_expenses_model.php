<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rental_expenses_model extends App_Model
{
    protected $table = 'rental_expenses';
    protected $allowed = ['property_id','expense_date','concept','amount','supplier','reference','notes'];

    public function get($id = '')
    {
        if ($id !== '') { return $this->db->where('id', (int)$id)->get(db_prefix() . $this->table)->row(); }
        return $this->db->order_by('id', 'DESC')->get(db_prefix() . $this->table)->result_array();
    }

    protected function filter_data($data)
    {
        $clean = [];
        foreach ($this->allowed as $field) { if (array_key_exists($field, $data)) { $clean[$field] = rentals_clean($data[$field]); } }
        return $clean;
    }

    public function add($data)
    {
        if (!rentals_check_license()) { return false; }
        $data = $this->filter_data($data);
        $data['created_at'] = rentals_now();
        $data['created_by'] = rentals_current_staff_id();
        $this->db->insert(db_prefix() . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!rentals_check_license()) { return false; }
        $data = $this->filter_data($data);
        $data['updated_at'] = rentals_now();
        $data['updated_by'] = rentals_current_staff_id();
        $this->db->where('id', (int)$id)->update(db_prefix() . $this->table, $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($id)
    {
        if (!rentals_check_license()) { return false; }
        $this->db->where('id', (int)$id)->delete(db_prefix() . $this->table);
        return $this->db->affected_rows() > 0;
    }
}
