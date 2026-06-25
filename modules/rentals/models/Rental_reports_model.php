<?php defined('BASEPATH') or exit('No direct script access allowed');
class Rental_reports_model extends App_Model {
 public function income_annual($year){ if(!rentals_check_license()) return []; return $this->db->select('payment_month, clientid, amount, amount_paid, (amount-amount_paid) as pending, status')->where('payment_month >=',$year.'-01')->where('payment_month <=',$year.'-12')->get(db_prefix().'rental_payments')->result_array(); }
 public function expenses_annual($year){ if(!rentals_check_license()) return []; return $this->db->select('e.*, p.name as property_name')->from(db_prefix().'rental_expenses e')->join(db_prefix().'rental_properties p','p.id=e.property_id','left')->where('YEAR(e.expense_date)',(int)$year)->get()->result_array(); }
}
