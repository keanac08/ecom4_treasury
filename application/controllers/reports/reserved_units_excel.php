<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reserved_units_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/reserved_units_model');
		session_check();
	}
	
	public function index(){
		
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
			
		$this->load->library('excel');
		$this->load->helper('date_helper');
		
		$rows = $this->reserved_units_model->get_tagged_per_customer($this->session->tre_portal_customer_id);

		$writer = new XLSXWriter();
		
		$header = array(
						'CS_NUMBER' => 'string',
						'SALES_MODEL' => 'string',
						'BODY_COLOR' => 'string',
						'ORDER_TYPE' => 'string',
						'PAYMENT_TERMS' => 'string',
						'RESERVED_DATE' => 'MM/DD/YYYY',
						'AGING' => 'integer',
						'AMOUNT' => '#,##0.00'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			
			if($row->CS_NUMBER != NULL){
				$data = array(
							$row->CS_NUMBER,
							$row->SALES_MODEL,
							$row->BODY_COLOR,
							$row->ORDER_TYPE,
							$row->PAYMENT_TERMS,
							excel_date($row->RESERVED_DATE),
							$row->AGING,
							$row->AMOUNT_DUE
						);
				$writer->writeSheetRow('Sheet1', $data);
			}
			
		}
		
		$filename = "reserved_units.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
