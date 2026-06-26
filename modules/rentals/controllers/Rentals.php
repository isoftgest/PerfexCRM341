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
    }

    public function index()
    {
        if (!is_admin() && !has_permission('rentals', '', 'view') && !has_permission('rentals', '', 'view_own')) { access_denied('rentals'); }
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


    /**
     * Renderiza secciones secundarias bajo /admin/rentals/* para evitar 404 en instalaciones
     * donde el router no resuelve controladores adicionales del módulo.
     */
    private function section_config($section)
    {
        $sections = [
            'properties' => ['rentals_properties', 'Rental_properties_model', 'properties', 'rental_properties', 'property', 'rentals/properties'],
            'units'      => ['rentals_units', 'Rental_units_model', 'units', 'rental_units', 'unit', 'rentals/units'],
            'payments'   => ['rentals_payments', 'Rental_payments_model', 'payments', 'rental_payments', 'payment', 'rentals/payments'],
            'deposits'   => ['rentals_deposits', 'Rental_deposits_model', 'deposits', 'rental_deposits', 'deposit', 'rentals/deposits'],
            'expenses'   => ['rentals_expenses', 'Rental_expenses_model', 'expenses', 'rental_expenses', 'expense', 'rentals/expenses'],
        ];

        return $sections[$section] ?? null;
    }

    private function section_index($section)
    {
        $config = $this->section_config($section);
        if (!$config) { show_404(); }
        [$permission, $model, $folder, $langKey, , $route] = $config;
        if (!rentals_is_admin_or_can($permission, 'view')) { access_denied($permission); }
        $this->load->model('rentals/' . $model);
        $data['title'] = _l($langKey);
        $data['items'] = $this->{$model}->get();
        $data['route'] = $route;
        $data['form_route'] = $route . '_form';
        $this->load->view('rentals/' . $folder . '/manage', $data);
    }

    private function section_form($section, $id = '')
    {
        $config = $this->section_config($section);
        if (!$config) { show_404(); }
        [$permission, $model, $folder, , $formView, $route] = $config;
        if ($id === '' && !rentals_is_admin_or_can($permission, 'create')) { access_denied($permission); }
        if ($id !== '' && !rentals_is_admin_or_can($permission, 'edit')) { access_denied($permission); }
        $this->load->model('rentals/' . $model);
        if ($this->input->post()) {
            $post = $this->input->post(null, true);
            $ok = $id === '' ? $this->{$model}->add($post) : $this->{$model}->update($id, $post);
            set_alert($ok ? 'success' : 'danger', $ok ? _l('rentals_saved_successfully') : _l('rentals_save_failed'));
            redirect(admin_url($route));
        }
        $data['item'] = $id === '' ? null : $this->{$model}->get($id);
        $data['title'] = $id === '' ? _l('new_record') : _l('edit');
        $this->load->view('rentals/' . $folder . '/' . $formView, $data);
    }

    private function section_delete($section, $id)
    {
        $config = $this->section_config($section);
        if (!$config) { show_404(); }
        [$permission, $model, , , , $route] = $config;
        if (!rentals_is_admin_or_can($permission, 'delete')) { access_denied($permission); }
        $this->load->model('rentals/' . $model);
        $ok = $this->{$model}->delete($id, (bool) $this->input->get('force'));
        set_alert($ok ? 'success' : 'danger', $ok ? _l('deleted') : _l('rentals_delete_failed'));
        redirect(admin_url($route));
    }

    public function properties() { $this->section_index('properties'); }
    public function properties_form($id = '') { $this->section_form('properties', $id); }
    public function properties_delete($id) { $this->section_delete('properties', $id); }

    public function units() { $this->section_index('units'); }
    public function units_form($id = '') { $this->section_form('units', $id); }
    public function units_delete($id) { $this->section_delete('units', $id); }

    public function payments() { $this->section_index('payments'); }
    public function payments_form($id = '') { $this->section_form('payments', $id); }
    public function payments_delete($id) { $this->section_delete('payments', $id); }

    public function deposits() { $this->section_index('deposits'); }
    public function deposits_form($id = '') { $this->section_form('deposits', $id); }
    public function deposits_delete($id) { $this->section_delete('deposits', $id); }

    public function expenses() { $this->section_index('expenses'); }
    public function expenses_form($id = '') { $this->section_form('expenses', $id); }
    public function expenses_delete($id) { $this->section_delete('expenses', $id); }

    public function reports()
    {
        redirect(admin_url('rentals/reports_income_annual'));
    }

    public function reports_income_annual()
    {
        if (!rentals_is_admin_or_can('rentals_reports', 'view')) { access_denied('rentals_reports'); }
        $this->load->model('rentals/Rental_reports_model');
        $year = $this->input->get('year') ?: date('Y');
        $data = ['title' => _l('annual_income_report'), 'items' => $this->Rental_reports_model->income_annual($year), 'year' => $year];
        $this->load->view('rentals/reports/income_annual', $data);
    }

    public function reports_expenses_annual()
    {
        if (!rentals_is_admin_or_can('rentals_reports', 'view')) { access_denied('rentals_reports'); }
        $this->load->model('rentals/Rental_reports_model');
        $year = $this->input->get('year') ?: date('Y');
        $data = ['title' => _l('annual_expenses_report'), 'items' => $this->Rental_reports_model->expenses_annual($year), 'year' => $year];
        $this->load->view('rentals/reports/expenses_annual', $data);
    }

    public function license()
    {
        if (!is_admin() && !has_permission('rentals_license', '', 'manage')) { access_denied('rentals_license'); }
        $this->load->model('rentals/Rentals_license_model');
        if ($this->input->post()) {
            $key = $this->input->post('license_key', true);
            $ok = $this->Rentals_license_model->activate($key);
            set_alert($ok ? 'success' : 'danger', $ok ? _l('rentals_license_valid') : _l('rentals_license_invalid'));
            redirect(admin_url('rentals/license'));
        }
        $data = ['title' => _l('rentals_license_activation'), 'license' => rentals_get_license_row()];
        $this->load->view('rentals/license/activate', $data);
    }

    public function delete($id)
    {
        if (!rentals_is_admin_or_can($this->permission, 'delete')) { access_denied($this->permission); }
        $ok = $this->{$this->model_name}->delete($id, (bool)$this->input->get('force'));
        set_alert($ok ? 'success' : 'danger', $ok ? _l('deleted') : _l('rentals_delete_failed'));
        redirect(admin_url('rentals'));
    }
}

