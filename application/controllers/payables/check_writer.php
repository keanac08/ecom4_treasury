<?php 
class Check_Writer extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('file');
		$this->load->model('payables/check_writer_model');
		session_check();
	}
	
	public function generate(){
		
		$this->load->helper('form');
		
		$data['content'] = 'payables/check_writer_generate_view';
		$data['head_title'] = 'Treasury | Check Writer';
		$data['title'] = 'Check Writer';
		$data['subtitle'] = '';
		
		$this->load->view('include/template',$data);
	}
	
	public function ajax_export_payables(){
		
		$this->load->helper('date_helper');
		
		$from_date = oracle_date($this->input->post('from_date'));
		$to_date = oracle_date($this->input->post('to_date'));
		$check_bank = $this->input->post('check_bank');
		
		$params = array($from_date, $to_date, $check_bank);
		$row = $this->check_writer_model->get_payables_cnt($params);
		
		if($row->CNT > 0){
			
			//~ echo $this->session->userdata('fullname');
			
			$header_data = array(
								$check_bank,
								$from_date,
								$to_date,
								$this->session->userdata('fullname')
							);
			
			$this->check_writer_model->insert_payables_header($header_data);
			$header = $this->check_writer_model->get_last_header_id();
			$header_id = $header->LAST_HEADER_ID;
			
			if($check_bank == 'UBP-ATM'){
				$rows = $this->check_writer_model->get_payables($params);
			}	
			else{
				$rows = $this->check_writer_model->get_payables($params);
			}
			
			//~ echo '<pre>';
			//~ print_r($rows);
			//~ echo '</pre>';
			
			//~ die();
			
			foreach($rows as $row){
				
				$line_data = array(
								$header_id,
								$row->IFS_PAYEE_CODE,
								$row->PAYEE_CODE,
								$row->PAYEE_NAME,
								$row->ACCOUNT_NUMBER,
								$row->REFERENCE_NO,
								oracle_date($row->INVOICE_DATE),
								$row->INVOICE_NO,
								$row->PAID_AMOUNT,
								$row->INVOICE_AMOUNT,
								$row->VAT_AMOUNT,
								$row->INVOICE_WOUT_VAT,
								$row->WHT_AMOUNT,
								$row->VAT_CODE,
								$row->WHT_CODE,
								$row->WHT_RATE,
								$row->CHECK_AMOUNT,
								$row->CHECK_NUMBER
							);
							
					
			//~ print_r($line_data);
			
				$this->check_writer_model->insert_payables_lines($line_data);
			}
			//~ die();
			echo $header_id;
		}
		else{
			echo 'false';
		}

	}
	
	public function export(){
		
		$header_id = $this->uri->segment(4);
		$data['head_title'] = 'Treasury | Check Writer';
		$data['result'] = $this->check_writer_model->get_payables_summary($header_id);
		$data['content'] = 'payables/check_writer_export_view';
		$data['title'] = 'Check Writer';
		$this->load->view('include/template',$data);
	}
	
	public function ajax_export(){
		
		$this->load->helper('date_helper');
		//~ ini_set('display_errors', 1); // show errors
		//~ error_reporting(-1);
		
		if($this->input->post('bank') != 'RCBC-CW'){
			$rows = $this->check_writer_model->get_payables_per_line($this->input->post('id'));
			$rows_xls = $rows;
			$text = '';
			$new_line = "\r\n";
			$cnt = 0;
			$total = 0;
			foreach($rows as $row){
				$row = (object)$row;
				
				//~ echo '"'.$this->encrypt($row->IFS_PAYEE_CODE).'","'.$this->encrypt($row->PAYEE_NAME).'",';
				$text .= '"'.$this->encrypt($row->IFS_PAYEE_CODE).'","'.$this->encrypt($row->PAYEE_NAME).'",';
				
				if($row->CHECK_BANK == 'UBP-ATM'){
					//~ echo '"'.$this->encrypt($row->ACCOUNT_NUMBER).'",';
					$text .= '"'.$this->encrypt($row->ACCOUNT_NUMBER).'",';
				}
				
				//~ echo '"'.$this->encrypt($row->REFERENCE_NO).'","'.$this->encrypt($row->INVOICE_DATE).'","'.$this->encrypt($row->INVOICE_NO).'","'.$this->encrypt($row->PAID_AMOUNT).'","'.$this->encrypt($row->CHECK_NO).'","'.$this->encrypt($row->CHECK_AMOUNT).'"'.$new_line.'<br />';
				$text .= '"'.$this->encrypt($row->REFERENCE_NO).'","'.$this->encrypt($row->INVOICE_DATE).'","'.$this->encrypt($row->INVOICE_NO).'","'.$this->encrypt($row->PAID_AMOUNT).'","'.$this->encrypt($row->CHECK_NO).'","'.$this->encrypt($row->CHECK_AMOUNT).'"'.$new_line;
				
				$total = $total + $row->PAID_AMOUNT;
				
				if($cnt == 0){
					$bank = $row->CHECK_BANK;
				}
				
				$cnt++;
			}
			//~ echo '"'.$this->encrypt((string)$cnt).'","'.$this->encrypt((string)$total).'",,,,,,<br />';
			$text .= '"'.$this->encrypt((string)$cnt).'","'.$this->encrypt((string)$total).'",,,,,,';
		}
		else{
			
			$params = array(
							$this->input->post('from'),
							$this->input->post('to'),
							$this->input->post('bank')
							);
			$rows = $this->check_writer_model->get_payables_rcbc($params);
			
			$rows_xls = $rows;
			$text = '';
			$new_line = "\r\n";
			$cnt = 0;
			$total = 0;
			foreach($rows as $row){
				$row = (object)$row;
				
				$text .= $row->PAYEE_CODE.',';
				$text .= $row->CHECK_DATE.',';
				$text .= $row->OTHER1.',';
				$text .= $row->AMOUNT.',';
				$text .= $row->OTHER2.',';
				$text .= $row->REMARKS.',';
				$text .= $row->OTHER3.',';
				$text .= $row->OTHER4.',';
				$text .= $row->OTHER5.',';
				$text .= $row->OTHER6.',';
				$text .= $row->EWT.',';
				$text .= $row->DELIVERY_ADDRESS.',';
				$text .= $row->ZIP_CODE.',';
				$text .= $row->ALPHA_NUMERIC_CODE.',';
				$text .= $row->FIRST_MONTH_OF_THE_QTR.',';
				$text .= $row->SECOND_MONTH_OF_THE_QTR.',';
				$text .= $row->THIRD_MONTH_OF_THE_QTR.',';
				$text .= $row->TOTAL_AMOUNT.',';
				$text .= $row->TAX_WITHELD.',';
				$text .= $row->PICKUP_OR_DELIVERY.',';
				$text .= $row->PICKUP_BRANCH.',';
				$text .= $row->PURPOSE_CODE.',';
				$text .= $row->PAYEE_NAME2.',';
				$text .= $row->PARTICULARS.',';
				$text .= $row->AUTHORIZED_COLLECTOR.',';
				$text .= $row->PAYEE_OR_COLLECTOR . $new_line;
				
				$cnt++;
			}
		}

		$bank = $this->input->post('bank');
		
		if($bank == 'BPI-CW'){
			$filename = '\\\ipcsvs001\database$\Treasury\bpi\Text\ORABPICW'.date('mdy');
			$filename_xls = '\\\ipcsvs001\database$\Treasury\bpi\ORACLE\ORABPICW'.date('mdy');
		}
		else if($bank == 'RCBC-CW'){
			$filename = '\\\ipcsvs001\database$\Treasury\rcbc\Text\ORARCBCCW'.date('mdy');
			$filename_xls = '\\\ipcsvs001\database$\Treasury\rcbc\ORACLE\ORARCBCCW'.date('mdy');
		}
		else if($bank == 'UBP-CW'){
			$filename = '\\\ipcsvs001\database$\Treasury\unionbank\Text\ORAUBPCW'.date('mdy');
			$filename_xls = '\\\ipcsvs001\database$\Treasury\unionbank\ORACLE\ORAUBPCW'.date('mdy');
		}
		else if($bank == 'UBP-ATM'){
			$filename = '\\\ipcsvs001\database$\Treasury\unionbank\SE PAYOUT\ORA TXT\ORAUBPATM'.date('mdy');
			$filename_xls = '\\\ipcsvs001\database$\Treasury\unionbank\SE PAYOUT\ORA DATA\ORAUBPATM'.date('mdy');
		}
		
		if(file_exists($filename.'.txt')){
			$ctr = 1;
			while(file_exists($filename.'-'.$ctr.'.txt')){
				$ctr++;
			}
			
			//~ write text file
			$filename = $filename.'-'.$ctr.'.txt';
			$myfile = fopen($filename, 'w');
			fwrite($myfile, $text);
			
			//~ write excel file
			$filename_xls = $filename_xls.'-'.$ctr.'.xlsx';
			$this->create_excel($rows_xls, $filename_xls, $bank);
			
			$data['filename'] = $filename;
			$data['filename_xls'] = $filename_xls;
			echo $this->load->view('ajax/cw_success',$data,true);
		}
		else{
			//~ write text file
			$filename = $filename.'.txt';
			$myfile = fopen($filename, 'w');
			fwrite($myfile, $text);
			
			//~ write excel file
			$filename_xls = $filename_xls.'.xlsx';
			$this->create_excel($rows_xls, $filename_xls, $bank);
			
			$data['filename'] = $filename;
			$data['filename_xls'] = $filename_xls;
			echo $this->load->view('ajax/cw_success',$data,true);
		}
	}
	
	public function create_excel($data, $filename, $bank){
		
		$this->load->library('phpexcel');

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

      	$row = count($data) + 1;
      	
      	if($bank != 'RCBC-CW'){
			
			$objPHPExcel = PHPExcel_IOFactory::load('././resources/report_template/checkwriter-template.xlsx');
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'T1')->applyFromArray($styleArray_header);
			$objPHPExcel->getActiveSheet()->getStyle('A2:'.'T'.$row)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('H2:L'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('P2:P'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		}
		else{
			
			$objPHPExcel = PHPExcel_IOFactory::load('././resources/report_template/checkwriter-template-rcbc.xlsx');
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->fromArray($data,null, 'A2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.'Z1')->applyFromArray($styleArray_header);
			$objPHPExcel->getActiveSheet()->getStyle('Z2:'.'Z'.$row)->applyFromArray($styleArray);
			//~ $objPHPExcel->getActiveSheet()->getStyle('AA2:'.'AA'.$row)->applyFromArray($styleArray);
		}
		
      	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      	$objWriter->save($filename);
	}
	
	public function encrypt($string){
	
		$encrypted_data = '';
		$str_length = strlen($string);
		$ctr = 0;
		
		if($str_length != 0){
			while($ctr < $str_length){
				$conv = ord($string[$ctr]) + 4;
				$encrypted_data = $encrypted_data . strlen($conv) . $conv;
				$ctr++;
			}
			return $encrypted_data; 
		}
		else {
			return NULL;
		}
	}
}
