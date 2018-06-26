<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagged_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('sales_order/vehicle_model');
		session_check();
	}
	
	public function index(){
			
		$this->load->library('excel');
					
		$this->load->helper('date_helper');

		$customer_id = $this->session->tre_portal_customer_id;
		$rows = $this->vehicle_model->get_tagged($customer_id);

		$writer = new XLSXWriter();
		
		$header = array(
						'Account_Name' => 'string',
						'CS_Number' => 'string',
						'Sales_Model' => 'string',
						'Body_Color' => 'string',
						'Chassis_Number' => 'string',
						'Engine_Number' => 'string',
						'Aircon_Number' => 'string',
						'Stereo_Number' => 'string',
						'Key_Number' => 'string',
						'Tagged_Date' => 'MM/DD/YYYY',
						'Aging' => 'integer',
						'Amount_Due' => '#,##0.00'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			
			$line = array(
						$row->ACCOUNT_NAME,
						$row->CS_NUMBER,
						$row->SALES_MODEL,
						$row->BODY_COLOR,
						$row->CHASSIS_NUMBER,
						$row->ENGINE,
						$row->AIRCON,
						$row->STEREO,
						$row->KEY_NO,
						excel_date($row->TAGGED_DATE),
						$row->AGING,
						$row->AMOUNT_DUE
					);
			$writer->writeSheetRow('Sheet1', $line);
		}
		
		//~ $writer->writeToFile('.xlsx');
		$filename = "tagged_units.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
