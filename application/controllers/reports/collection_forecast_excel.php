<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class collection_forecast_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		session_check();
		$this->load->model('receivables/check_warehousing_model');
	}
	
	public function index(){
		
		$from_date = date('d-M-y', strtotime($this->input->post('from_date')));
		$to_date = date('d-M-y', strtotime($this->input->post('to_date')));

		//~ echo $to_date;

		$rows = $this->check_warehousing_model->get_unit_check_details_excel($from_date,$to_date);
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		
		$this->load->library('excel');
			
		$writer = new XLSXWriter();
		
		$header = array(
						'Check_ID' => 'integer',
						'Check_Number' => 'integer',
						'Check_Bank' => 'string',
						'Check_Date' => 'MM/DD/YYYY',
						'Check_Amount' => '#,##0.00',
						'Date_Approved' => 'MM/DD/YYYY',
						'From_Check_Date' => 'MM/DD/YYYY',
						'To_Check_Date' => 'MM/DD/YYYY'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			array_push($row, date('Y-m-d', strtotime($this->input->post('from_date'))), date('Y-m-d', strtotime($this->input->post('to_date'))));
			$writer->writeSheetRow('Sheet1', $row);
		}
		
		$filename = "collection_forecast.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
