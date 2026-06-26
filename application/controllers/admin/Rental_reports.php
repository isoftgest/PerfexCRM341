<?php

defined('BASEPATH') or exit('No direct script access allowed');

/** Controlador puente para /admin/rental_reports. */
class Rental_reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('rentals/rentals');
        $this->load->model('rentals/Rental_reports_model');
        if (!rentals_check_license()) { redirect(admin_url('rentals_license')); }
        if (!rentals_is_admin_or_can('rentals_reports', 'view')) { access_denied('rentals_reports'); }
    }

    public function index()
    {
        redirect(admin_url('rental_reports/income_annual'));
    }

    public function income_annual()
    {
        $year = $this->input->get('year') ?: date('Y');
        $data = ['title' => _l('annual_income_report'), 'items' => $this->Rental_reports_model->income_annual($year), 'year' => $year];
        $this->load->view('rentals/reports/income_annual', $data);
    }

    public function expenses_annual()
    {
        $year = $this->input->get('year') ?: date('Y');
        $data = ['title' => _l('annual_expenses_report'), 'items' => $this->Rental_reports_model->expenses_annual($year), 'year' => $year];
        $this->load->view('rentals/reports/expenses_annual', $data);
    }
}
