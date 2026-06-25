<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rentals extends AdminController
{
    protected $permission = 'rentals';
    protected $model_name = 'Rentals_model';
    protected $view_folder = 'rentals';
    protected $lang_key = 'rentals';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('rentals/rentals');
        $this->load->model('rentals/' . $this->model_name);
        if (!rentals_check_license()) { redirect(admin_url('rentals_license')); }
        if (!is_admin() && !has_permission($this->permission, '', 'view') && !($this->permission === 'rentals' && has_permission('rentals', '', 'view_own'))) { access_denied($this->permission); }
    }

    public function index()
    {
        $data['title'] = _l($this->lang_key);
        $own = $this->permission === 'rentals' && !is_admin() && !has_permission('rentals', '', 'view') && has_permission('rentals', '', 'view_own');
        $data['items'] = $this->{$this->model_name}->get('', $own);
        $this->load->view('rentals/' . $this->view_folder . '/manage', $data);
    }

    public function form($id = '')
    {
        if ($id === '' && !rentals_is_admin_or_can($this->permission, 'create')) { access_denied($this->permission); }
        if ($id !== '' && !rentals_is_admin_or_can($this->permission, 'edit')) { access_denied($this->permission); }
        if ($this->input->post()) {
            $post = $this->input->post(null, true);
            $ok = $id === '' ? $this->{$this->model_name}->add($post) : $this->{$this->model_name}->update($id, $post);
            set_alert($ok ? 'success' : 'danger', $ok ? _l('rentals_saved_successfully') : _l('rentals_save_failed'));
            redirect(admin_url('rentals'));
        }
        $data['item'] = $id === '' ? null : $this->{$this->model_name}->get($id);
        $data['title'] = $id === '' ? _l('new_record') : _l('edit');
        $this->load->view('rentals/' . $this->view_folder . '/rental', $data);
    }


    public function view($id)
    {
        $own = !is_admin() && !has_permission('rentals', '', 'view') && has_permission('rentals', '', 'view_own');
        $data['rental'] = $this->Rentals_model->get($id, $own);
        if (!$data['rental']) { show_404(); }
        $data['history'] = $this->Rentals_model->price_history($id);
        $data['title'] = _l('view_rental');
        $this->load->view('rentals/rentals/view', $data);
    }

    public function generate_month($id)
    {
        if (!rentals_is_admin_or_can('rentals_payments', 'create')) { access_denied('rentals_payments'); }
        $this->load->model('rentals/Rental_payments_model');
        $rental = $this->Rentals_model->get($id);
        if (!$rental) { show_404(); }
        $month = $this->input->get('month') ?: date('Y-m');
        $dueDay = max(1, min(28, (int)(get_option('rentals_default_due_day') ?: 1)));
        $ok = $this->Rental_payments_model->add(['rental_id'=>$rental->id,'clientid'=>$rental->clientid,'payment_month'=>$month,'due_date'=>$month . '-' . str_pad($dueDay, 2, '0', STR_PAD_LEFT),'amount'=>$rental->monthly_price,'amount_paid'=>0,'status'=>'pending']);
        set_alert($ok ? 'success' : 'danger', $ok ? _l('rentals_saved_successfully') : _l('rentals_save_failed'));
        redirect(admin_url('rentals/view/' . $id));
    }

    public function delete($id)
    {
        if (!rentals_is_admin_or_can($this->permission, 'delete')) { access_denied($this->permission); }
        $ok = $this->{$this->model_name}->delete($id, (bool)$this->input->get('force'));
        set_alert($ok ? 'success' : 'danger', $ok ? _l('deleted') : _l('rentals_delete_failed'));
        redirect(admin_url('rentals'));
    }
}

