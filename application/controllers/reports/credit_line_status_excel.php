<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_line_status_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/credit_line_model');
		session_check();
	}
	
	public function index(){
		
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
		
		$this->load->library('excel');
		
		if($this->uri->segment(4) == 'parts'){ 
			$rows = $this->credit_line_model->get_parts_credit_line_status();
		}
		$writer = new XLSXWriter();
		
		$header = array(
						'CUSTOMER_NAME' => 'string',
						'PROFILE_CLASS' => 'string',
						'TERM' => 'string',
						'CREDIT_LIMIT' => '#,##0.00',
						'EXPOSURE_AR_BALANCE' => '#,##0.00',
						'EXPOSURE_OPEN_SO' => '#,##0.00',
						'TOTAL_EXPOSURE' => '#,##0.00',
						'AVAILABLE_CREDIT_LIMIT' => '#,##0.00',
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			
			$data = array(
						$row->CUSTOMER_NAME,
						$row->PROFILE_CLASS,
						$row->TERM,
						$row->CREDIT_LIMIT,
						$row->EXPOSURE_AR_BALANCE_TOTAL,
						$row->EXPOSURE_OPEN_SO,
						$row->TOTAL_EXPOSURE,
						$row->AVAILABLE_CREDIT_LIMIT
					);
			$writer->writeSheetRow('Sheet1', $data);
		}
		
		$filename = "credit_line_status.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
