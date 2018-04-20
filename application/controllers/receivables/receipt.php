<?php 
class Receipt extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/receipt_model');
		session_check();
	}
	
	public function unapplied_receipt_modal(){

		$data[] = '';
		echo $this->load->view('reports/unapplied_receipt_view',$data, true);
	}
	
	public function search(){

		$data['content'] = 'receivables/receipt_search_view';
		$data['head_title'] = 'Treasury | Receipt';
		$data['title'] = 'Receipt';
		$data['subtitle'] = '';
				
		$this->load->view('include/template',$data);
	}
	
	public function collection(){

		$data['content'] = 'receivables/receipt_collection_view';
		$data['head_title'] = 'Treasury | Receipt';
		$data['title'] = 'Receipt';
		$data['subtitle'] = '';
		
		$receipt_id = $this->uri->segment('4');
		$data['receipt_id'] = $receipt_id;
		$data['header'] = $this->receipt_model->get_collection_receipts_header($receipt_id);
		$data['header'] = $data['header'][0];
		//~ print_r($data['header']);
		$data['lines'] = $this->receipt_model->get_collection_receipts_lines($receipt_id);
		
		$this->load->view('include/template',$data);
	}
	
	public function ajax_find_receipt_id(){
		
		$receipt_id = $this->input->post('search_receipt');
		$data['result'] = $this->receipt_model->get_receipt_id($receipt_id);
		
		echo $this->load->view('ajax/receipt_result_view',$data, true);

		
	}
}
