<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rentals_model extends App_Model
{
    public function get($id = '', $own = false)
    {
        $this->db->select('r.*, c.company as tenant_name, p.name as property_name, u.name as unit_name, u.type as unit_type');
        $this->db->from(db_prefix() . 'rentals r');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = r.clientid', 'left');
        $this->db->join(db_prefix() . 'rental_properties p', 'p.id = r.property_id', 'left');
        $this->db->join(db_prefix() . 'rental_units u', 'u.id = r.unit_id', 'left');
        if ($own) { $this->db->where('r.created_by', rentals_current_staff_id()); }
        if ($id !== '') { return $this->db->where('r.id', (int)$id)->get()->row(); }
        return $this->db->order_by('r.id','DESC')->get()->result_array();
    }

    public function has_overlap($unit_id, $start_date, $end_date = null, $exclude_id = null)
    {
        $this->db->where('unit_id', (int)$unit_id)->where('status', 'active');
        if ($exclude_id) { $this->db->where('id !=', (int)$exclude_id); }
        $end = $end_date ?: '9999-12-31';
        $this->db->where('start_date <=', $end)->group_start()->where('end_date IS NULL', null, false)->or_where('end_date >=', $start_date)->group_end();
        return $this->db->count_all_results(db_prefix() . 'rentals') > 0;
    }

    protected function prepare($data)
    {
        $fields = ['contract_reference','clientid','property_id','unit_id','start_date','end_date','monthly_price','status','notes'];
        $clean=[]; foreach($fields as $f){ if(array_key_exists($f,$data)){ $clean[$f]=rentals_clean($data[$f]); } }
        return $clean;
    }

    public function add($data)
    {
        if (!rentals_check_license()) return false;
        $data=$this->prepare($data);
        if ($this->has_overlap($data['unit_id'], $data['start_date'], $data['end_date'] ?? null)) return false;
        $data['created_at']=rentals_now(); $data['created_by']=rentals_current_staff_id();
        $this->db->insert(db_prefix().'rentals',$data); return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!rentals_check_license()) return false;
        $old=$this->get($id); $data=$this->prepare($data);
        if ($this->has_overlap($data['unit_id'], $data['start_date'], $data['end_date'] ?? null, $id)) return false;
        $this->db->trans_start();
        $data['updated_at']=rentals_now(); $data['updated_by']=rentals_current_staff_id();
        $this->db->where('id',(int)$id)->update(db_prefix().'rentals',$data);
        if ($old && isset($data['monthly_price']) && (float)$old->monthly_price !== (float)$data['monthly_price']) {
            $this->db->insert(db_prefix().'rental_price_history',['rental_id'=>(int)$id,'old_price'=>$old->monthly_price,'new_price'=>$data['monthly_price'],'change_date'=>date('Y-m-d'),'reason'=>$data['change_reason'] ?? null,'created_at'=>rentals_now(),'created_by'=>rentals_current_staff_id()]);
        }
        $this->db->trans_complete(); return $this->db->trans_status();
    }

    public function delete($id, $force = false)
    {
        if (!rentals_check_license()) return false;
        if (!$force && (total_rows(db_prefix().'rental_payments',['rental_id'=>(int)$id]) || total_rows(db_prefix().'rental_deposits',['rental_id'=>(int)$id]))) return false;
        $this->db->where('id',(int)$id)->delete(db_prefix().'rentals'); return $this->db->affected_rows()>0;
    }

    public function price_history($id){ return $this->db->where('rental_id',(int)$id)->order_by('change_date','DESC')->get(db_prefix().'rental_price_history')->result_array(); }
}
