<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class unapplied_receipt_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		session_check();
		$this->load->model('receivables/receipt_model');
	}
	
	public function index(){
		
		$from_date = date('d-M-y', strtotime($this->input->post('from_date')));
		$to_date = date('d-M-y', strtotime($this->input->post('to_date')));

		//~ echo $to_date;

		$rows = $this->receipt_model->get_unapplied_receipts($from_date,$to_date);
		
		$this->load->library('excel');
			
		$writer = new XLSXWriter();
		
		$header = array(
						'Receipt_Number' => 'integer',
						'Receipt_Doc_Number' => 'integer',
						'Customer_Name' => 'string',
						'Account_Number' => 'integer',
						'Account_Name' => 'string',
						'Site_Name' => 'string',
						'Bank_Name' => 'string',
						'Bank_Account_Name' => 'string',
						'Currency_Code' => 'string',
						'Unidentified_Amount' => '#,##0.00',
						'Unapplied_Amount' => '#,##0.00',
						'Applied_Amount' => '#,##0.00',
						'Receipt_Amount' => '#,##0.00',
						'Receipt_Date' => 'MM/DD/YYYY',
						'Status' => 'string'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			$writer->writeSheetRow('Sheet1', $row);
		}
		
		$filename = "unapplied_receipts.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
