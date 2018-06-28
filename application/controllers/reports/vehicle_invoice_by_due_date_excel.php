<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class vehicle_invoice_by_due_date_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		session_check();
		$this->load->model('receivables/invoice_model');
	}
	
	public function index(){

		$from_date = date('d-M-y', strtotime($this->input->post('from_date')));
		$to_date = date('d-M-y', strtotime($this->input->post('to_date')));
		//~ $from_date = '01-FEB-18';
		//~ $to_date = '05-FEB-18';

		$rows = $this->invoice_model->get_vehicle_invoice_by_duedate($from_date,$to_date);
		
		$this->load->library('excel');
			
		$writer = new XLSXWriter();
		
		$header = array(
						'Customer_ID' => 'integer',
						'Customer_Name' => 'string',
						'Account_Name' => 'string',
						'Fleet_Name' => 'string',
						'Profile_Class' => 'string',
						'Invoice_ID' => 'integer',
						'Invoice_Number' => 'integer',
						'Invoice_Date' => 'MM/DD/YYYY',
						'CS_Number' => 'string',
						'Sales_Model' => 'string',
						'Body_Color' => 'string',
						'Payment_Term' => 'string',
						'Delivery_Date' => 'MM/DD/YYYY',
						'Due_Date' => 'MM/DD/YYYY',
						'Days_Overdue' => 'integer',
						'Invoice_Amount' => '#,##0.00',
						'WHT_Amount' => '#,##0.00',
						'Balance' => '#,##0.00',
						'Check_Number' => 'string',
						'Check_Bank' => 'string',
						'Check_Date' => 'MM/DD/YYYY',
						'Check_Amount' => '#,##0.00',
						'Due_Date_From' => 'MM/DD/YYYY',
						'Due_Date_To' => 'MM/DD/YYYY'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			$writer->writeSheetRow('Sheet1', $row);
		}
		
		$filename = "invoiced_vehicle_by_due_date.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
