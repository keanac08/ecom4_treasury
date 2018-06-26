<?php 
class Invoice extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		session_check();
	}
	
	public function vehicle_by_due_date_modal(){

		$data[] = '';
		echo $this->load->view('reports/vehicle_invoice_by_due_date_view',$data, true);
	}
	
	public function invoice_modal(){

		$data[] = '';
		echo $this->load->view('reports/invoices_view',$data, true);
	}
	
	public function vehicle_by_due_date_form(){
		
		$data['content'] = 'reports/vehicle_invoice_by_due_date_view';
		$data['head_title'] = 'Treasury | Reports';
		$data['title'] = 'Vehicle Invoice Report';
		$data['subtitle'] = 'By Due Date Range';
		
		$this->load->view('include/template',$data);
	}
	
	public function by_date_range(){
		
		$this->load->model('receivables/invoice_model');
		
		$from_date = $this->input->post('from_date') == NULL ? date('d-M-y'): date('d-M-y', strtotime($this->input->post('from_date')));
		$to_date = $this->input->post('to_date')  == NULL ? date('d-M-y'): date('d-M-y', strtotime($this->input->post('to_date')));
		$customer_id = $this->session->tre_portal_customer_id;
		
		$data['from_date'] = $from_date;
		$data['to_date'] = $to_date;
		$data['customer_id'] = $customer_id;
		
		$data['content'] = 'receivables/invoiced_date_range_view';
		$data['title'] = 'Invoices';
		$data['head_title'] = 'Treasury | Invoices';
		
		$data['result'] = $this->invoice_model->get_invoices($from_date, $to_date, 0, $customer_id);
		
		$this->load->view('include/template',$data);
	}
}
