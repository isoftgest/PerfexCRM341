<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rental_expenses extends AdminController
{
    protected $permission = 'rentals_expenses';
    protected $model_name = 'Rental_expenses_model';
    protected $view_folder = 'expenses';
    protected $lang_key = 'rental_expenses';

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
            redirect(admin_url('rental_expenses'));
        }
        $data['item'] = $id === '' ? null : $this->{$this->model_name}->get($id);
        $data['title'] = $id === '' ? _l('new_record') : _l('edit');
        $this->load->view('rentals/' . $this->view_folder . '/expense', $data);
    }

    public function delete($id)
    {
        if (!rentals_is_admin_or_can($this->permission, 'delete')) { access_denied($this->permission); }
        $ok = $this->{$this->model_name}->delete($id, (bool)$this->input->get('force'));
        set_alert($ok ? 'success' : 'danger', $ok ? _l('deleted') : _l('rentals_delete_failed'));
        redirect(admin_url('rental_expenses'));
    }
}
