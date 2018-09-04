<?php 
class Payment extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		session_check();
		$this->load->model('receivables/payment_model');
	}
	
	
	
	public function parts(){
		
		$data['content'] = 'receivables/payment_parts_view';
		$data['head_title'] = 'Treasury | Payments';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'Parts';
		$data['results'] = $this->payment_model->get_parts_invoiced($this->session->tre_portal_customer_id);
		
		$this->load->view('include/template',$data);
	}
	
	public function dated(){
		
		//~ echo $this->session->tre_portal_customer_id;
		
		//~ $type = $this->uri->segment(4);
		
		$data['content'] = 'receivables/payments_vehicle_dated_view';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'w/ Terms';
		$data['head_title'] = 'Treasury | Payments';
		
		$data['results'] = $this->payment_model->get_tagged_units_per_customer($this->session->tre_portal_customer_id, 'dated');
		
		$this->load->view('include/template',$data);
	}
	
	public function advance_payment(){
		
		//~ echo $this->session->tre_portal_customer_id;
		
		//~ $type = $this->uri->segment(4);
		
		$data['content'] = 'receivables/payments_vehicle_adv_pay_view';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'w/o Terms (Advance Payment)';
		$data['head_title'] = 'Treasury | Payments';
		
		$data['results'] = $this->payment_model->get_tagged_units_per_customer($this->session->tre_portal_customer_id, 'pdc');
		
		$this->load->view('include/template',$data);
	}
	
	public function regular_pdc(){
		
		//~ echo $this->session->tre_portal_customer_id;
		
		//~ $type = $this->uri->segment(4);
		
		$data['content'] = 'receivables/payments_vehicle_reg_pdc_view';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'w/o Terms (PDC)';
		$data['head_title'] = 'Treasury | Payments';
		
		$data['results'] = $this->payment_model->get_vehicle_tagged_w_terms($this->session->tre_portal_customer_id, $this->uri->segment(4));
		
		$this->load->view('include/template',$data);
	}
	
	public function check_details(){
		
		$data['content'] = 'receivables/payments_vehicle_dated_check_view';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'Check';
		$data['head_title'] = 'Treasury | Payments';
		
		$cs_numbers = '\''.implode('\',\'', str_replace(' ', '', $this->input->post('cs_numbers'))).'\'';
		$cs_numbers = STRTOUPPER($cs_numbers);
		
		$data['result'] = $this->payment_model->get_tagged_for_check_payments($cs_numbers);
		$data['cs_numbers'] = $cs_numbers;
		
		$this->load->view('include/template',$data);
		//~ echo '<pre>';
		//~ print_r($data['result']);
		//~ echo '</pre>';
	}
}
