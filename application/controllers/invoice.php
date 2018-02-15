<?php 
class Invoice extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		session_check();
	}
	
	public function vehicle_by_due_date_form(){
		
		$data['content'] = 'report_forms/vehicle_invoice_by_due_date_view';
		$data['head_title'] = 'Treasury | Reports';
		$data['title'] = 'Vehicle Invoice Report';
		$data['subtitle'] = 'By Due Date Range';
		
		$this->load->view('include/template',$data);
	}
	
	public function vehicle_by_due_date_excel(){

		//~ echo $_SERVER['REQUEST_URI'];
		$this->load->model('invoice_model');

		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);

		//~ $customer_id = $this->uri->segment(3) / 101403;
		//~ $profile_class_id = $this->uri->segment(4);
		//~ $as_of_date_orig = DateTime::createFromFormat('mdY', $this->uri->segment(5));
		//~ $as_of_date =  $as_of_date_orig->format('d-M-y');

		$from_date = date('d-M-y', strtotime($this->input->post('from_date')));
		$to_date = date('d-M-y', strtotime($this->input->post('to_date')));

		//~ echo $from_date . ' - ' . $to_date;

		$data = $this->invoice_model->get_vehicle_invoice_by_duedate($from_date,$to_date);

		 //~ echo "<pre>";
		 //~ print_r($data);
		 //~ echo "</pre>";
		 //~ exit();
		$this->load->library('excel');

      	$styleArray = array(
        	'borders' => array(
	            'allborders' => array(
	                'style' => PHPExcel_Style_Border::BORDER_THIN
	            )
	        ),

	        'font'  => array(
	              'bold'  => false,
	              'size'  => 8,
	              'name'  => 'Calibri'
	        ),

	        'alignment' => array(
	          	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	      	)

    	);

    	$styleArray_header = array(
        	'borders' => array(
	            'allborders' => array(
	                'style' => PHPExcel_Style_Border::BORDER_THIN
	            )
	        ),

	        'font'  => array(
	              'bold'  => true,
	              'size'  => 10,
	              'name'  => 'Calibri'
	        ),

	        'alignment' => array(
	          	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	      	)

    	);

    	//~ print_r($data);die();

      	$row = count($data) + 1;
      	$objPHPExcel = PHPExcel_IOFactory::load('././resources/report_template/vehicle-invoice-by-duedate.xlsx');
      	$objPHPExcel->setActiveSheetIndex(0);

      	$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
      	$objPHPExcel->getActiveSheet()->getStyle('A1:'.'S1')->applyFromArray($styleArray_header);
      	$objPHPExcel->getActiveSheet()->getStyle('A2:'.'S'.$row)->applyFromArray($styleArray);
      	//~ $objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('00000000000000');
      	//~ $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$ctr, $row->part_no,PHPExcel_Cell_DataType::TYPE_STRING);
      	//~ $objPHPExcel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('0000');

      	//~ $objPHPExcel->getActiveSheet()->getStyle('S2:S'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      	//~ $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	//~ $objWriter->save('././resources/report_template/soa_temp.xls');

      	//~ $filename = $sales_type . '-soa-'.$data[0]['account_number'].'.xls'; //save our workbook as this file name
      	$filename = 'vehicle-invoice-by-duedate.xls'; //save our workbook as this file name

      	header('Content-Type: application/vnd.ms-excel'); //mime type

      	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name

      	header('Cache-Control: max-age=0'); //no cache

      	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      	$objWriter->save('php://output');
	}
}
