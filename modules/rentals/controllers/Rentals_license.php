<?php defined('BASEPATH') or exit('No direct script access allowed');
class Rentals_license extends AdminController { public function __construct(){ parent::__construct(); $this->load->helper('rentals/rentals'); $this->load->model('rentals/Rentals_license_model'); if(!is_admin() && !has_permission('rentals_license','','manage')) access_denied('rentals_license'); }
 public function index(){ if($this->input->post()){ $key=$this->input->post('license_key',true); $ok=$this->Rentals_license_model->activate($key); set_alert($ok?'success':'danger',$ok?_l('rentals_license_valid'):_l('rentals_license_invalid')); redirect(admin_url('rentals_license')); } $data=['title'=>_l('rentals_license_activation'),'license'=>rentals_get_license_row()]; $this->load->view('rentals/license/activate',$data); }
}
