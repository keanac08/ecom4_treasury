<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Check_warehousing extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/check_warehousing_model');
		session_check();
	}
	
	public function pdc(){
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
		
		$data['content'] = 'receivables/check_warehousing_pdc_view';
		$data['title'] = 'Check Warehousing';
		$data['head_title'] = 'Treasury | Check Warehousing';
		
		$data['result'] = $this->check_warehousing_model->get_approved_pdc($data['from_date'], $data['to_date']);
		
		$this->load->view('include/template',$data);
	}
	
	public function customer_check_list(){
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
		
		$data['content'] = 'receivables/check_warehousing_pdc_customer_view';
		$data['title'] = 'Requests for Invoice';
		$data['head_title'] = 'Treasury | Tagged Units';
		
		$data['result'] = $this->check_warehousing_model->get_customer_pdc($data['from_date'], $data['to_date'], $this->session->tre_portal_customer_id);
		
		$this->load->view('include/template',$data);
	}
	
	public function credit_hold_releasing(){

		$data['content']    = 'receivables/credit_hold_releasing_view';
		$data['head_title'] = 'Treasury | Check Warehousing';
		$data['title']      = 'Vehicle Credit Hold Releasing';
		
		if($this->input->post('batch_id') != NULL OR $this->input->post('check_id') != NULL){
			$batch_id = $this->input->post('batch_id');
			$check_id = $this->input->post('check_id');
			$data['batch_id'] = $batch_id;
			$data['check_id'] = $check_id;
			
			$data['result'] = $this->check_warehousing_model->get_check_details_for_releasing($batch_id, $check_id);
		}
		
		$this->load->view('include/template',$data);
	}
	
	
	public function credit_hold_releasing_ajax(){

		$line_ids = $this->input->post('line_ids');
		$check_ids = $this->input->post('check_ids');
		$cs_numbers = $this->input->post('cs_numbers');
		
		$i = 0;
		while($i < count($line_ids)){
			$this->check_warehousing_model->new_approved_pdc($check_ids[$i], $line_ids[$i], $cs_numbers[$i]);
			$i++;
		}
		
		$line_ids = $this->input->post('line_ids');
		$line_ids = implode(',', $line_ids);
		$data = $this->check_warehousing_model->new_release_lines($line_ids);

	}
	
	public function entry(){
		
		$data['content'] = 'receivables/check_warehousing_entry_view';
		$data['title'] = 'Check Warehousing <small>New Entry</small>';
		$data['head_title'] = 'Treasury | Check Warehousing';
		
		$data['cs_numbers'] = NULL;
		
		$this->load->view('include/template',$data);
	}
	
	public function customer_entry(){
		
		//~ echo $this->session->tre_portal_customer_id;
		
		$type = $this->uri->segment(4);
		
		$data['content'] = 'receivables/check_warehousing_customer_entry_view';
		$data['title'] = 'Tagged Units';
		$data['head_title'] = 'Treasury | Check Warehousing';
		$data['type'] = $type;
		
		if($type == 'vehicle'){
			$data['subtitle'] = 'Vehicle';
		}
		else if($type == 'vehicle_terms'){
			$data['subtitle'] = 'Vehicle w/ Terms';
		}
		else if($type == 'fleet'){
			$data['subtitle'] = 'Fleet';
		}
		
		
		$data['results'] = $this->check_warehousing_model->get_tagged_per_customer($this->session->tre_portal_customer_id);
		
		$this->load->view('include/template',$data);
	}
	
	public function customer_entry_2(){
		
		$data['content'] = 'receivables/check_warehousing_customer_entry2_view';
		$data['title'] = 'Tagged Units';
		$data['head_title'] = 'Treasury | Check Warehousing';
		
		$cs_numbers = '\''.implode('\',\'', str_replace(' ', '', $this->input->post('cs_numbers'))).'\'';
		$cs_numbers = STRTOUPPER($cs_numbers);
		
		$data['result'] = $this->check_warehousing_model->get_tagged_units($cs_numbers);
		$data['cs_numbers'] = $cs_numbers;
		
		$this->load->view('include/template',$data);
		//~ echo '<pre>';
		//~ print_r($data['result']);
		//~ echo '</pre>';
	}
	
	public function collection_forecast_modal(){

		$data[] = '';
		echo $this->load->view('reports/collection_forecast_view',$data, true);
	}
	
	public function search(){
		
		$data['content'] = 'receivables/check_warehousing_search_view';
		$data['title'] = 'Check Warehousing <small> Search</small>';
		$data['head_title'] = 'Treasury | Check Warehousing';
		
		if($this->input->post('q') != NULL){
			$data['q'] = $this->input->post('q');
			$data['results'] = $this->check_warehousing_model->get_unit_check_details($this->input->post('q'));
			//~ print_r($data['results']);
		}
		else{
			$data['q'] = NULL;
		}
		
		$this->load->view('include/template',$data);
	}
	
	public function ajax_search_cs_number(){
		
		$cs_numbers = explode(',', $this->input->post('cs_numbers'));
		$cs_numbers = '\''.implode('\',\'', str_replace(' ', '', $cs_numbers)).'\'';
		$cs_numbers = STRTOUPPER($cs_numbers);
		
		$data['result'] = $this->check_warehousing_model->get_tagged_units($cs_numbers);
		$data['cs_numbers'] = $cs_numbers;
		
		echo $this->load->view('ajax/check_warehousing_selected_units',$data,true);	
	}
	
	public function save_entry(){
		
		$this->load->helper('date_helper');
		
		$check_details = array(
					$this->input->post('check_number'),
					$this->input->post('check_bank'),
					oracle_date($this->input->post('check_date')),
					$this->input->post('check_amount'),
					$this->session->tre_portal_customer_id
				);
				
		//~ insert check header
		$this->check_warehousing_model->new_pdc_header($check_details);
		//~ get last header id
		$header_id = $this->check_warehousing_model->get_last_pdc_header_id();
		$header_id = $header_id[0];
		
		$cs_numbers = explode(',', $this->input->post('cs_numbers'));
		$cs_numbers = '\''.implode('\',\'', str_replace(' ', '', $cs_numbers)).'\'';
		$cs_numbers = STRTOUPPER($cs_numbers);
		
		if($this->session->tre_portal_customer_id == NULL){
			$cs_numbers = explode(',', $this->input->post('cs_numbers'));
			$cs_numbers = '\''.implode('\',\'', str_replace(' ', '', $cs_numbers)).'\'';
			$cs_numbers = STRTOUPPER($cs_numbers);
		}
		else{
			$cs_numbers = $this->input->post('cs_numbers');
		}
		
		//~ insert check tagged units
		$this->check_warehousing_model->new_pdc_units_header($header_id->LAST_ID, $cs_numbers);
		
		//~ echo $header_id->LAST_ID . ' ' . $cs_numbers;
		
		echo $header_id->LAST_ID;
	}
	
	public function approved_check_unit_details_ajax(){
		
		$data['result'] =  $this->check_warehousing_model->get_approved_check_unit_details($this->input->post('check_id'));
		$data['check_number'] = $this->input->post('check_number');
		$data['check_bank'] = $this->input->post('check_bank');
		echo $this->load->view('ajax/approved_check_unit_details_view',$data,true);
		
	}
	
	public function customer_check_unit_details_ajax(){
		
		$data['result'] =  $this->check_warehousing_model->get_approved_check_unit_details($this->input->post('check_id'));
		$data['check_number'] = $this->input->post('check_number');
		$data['check_bank'] = $this->input->post('check_bank');
		echo $this->load->view('ajax/customer_check_unit_details_view',$data,true);
		
	}
	
	public function deposit_date_entry_ajax(){
		
		
		$this->load->helper('date_helper');
		
		$check_id = $this->input->post('check_id');
		$deposit_date = $this->input->post('deposit_date');
		$this->check_warehousing_model->update_check_deposit_date($check_id, oracle_date($deposit_date));
	}
	
	public function pdc_details_pdf(){
		
		$this->load->helper('number_helper');
		$this->load->helper('date_helper');
		$rows = $this->check_warehousing_model->get_pdc_details($this->uri->segment(4));
		
		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		//~ die();
		
		$data = "";
		$check_id = 0;
		$ctr = 1;
		$total_amount_due = 0;
		foreach($rows as $row){
			
			if($check_id == 0){
				$check_number = $row->CHECK_NUMBER;
				$check_bank = $row->CHECK_BANK;
			}
			
			if($check_id == 0 OR $check_id != $row->CHECK_ID){
				
				if($check_id != 0){
					
					$data .= '<tr>
								<td colspan="5" style="text-align: right;border-top: 1px solid #444;">&nbsp;</td>
								<td colspan="1" style="text-align: right;border-top: 1px solid #444;">'.amount($total_amount_due).'</td>
								<td colspan="1" style="text-align: right;border-top: 1px solid #444;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="7">&nbsp;</td>
							</tr>';
				}
				
				$data .= '<tr>
							<td width="170px;" colspan="2">Check ID</td>
							<td colspan="5">'.$row->CHECK_ID.'</td>
						</tr>
						<tr>
							<td width="170px;" colspan="2">Check Number</td>
							<td colspan="5">'.$row->CHECK_NUMBER.'</td>
						</tr>
						<tr>
							<td colspan="2">Check Bank</td>
							<td colspan="5">'.$row->CHECK_BANK.'</td>
						</tr>
						<tr>
							<td colspan="2">Check Date</td>
							<td colspan="5">'.short_date($row->CHECK_DATE).'</td>
						</tr>
						<tr>
							<td colspan="2">Check Amount</td>
							<td colspan="5">'.amount($row->CHECK_AMOUNT).'</td>
						</tr>
						<tr>
							<td colspan="7">&nbsp;</td>
						</tr>
						<tr style="background-color: #ccc;">
							<th width="20px;" style="text-align: center;">#</th>
							<th width="70px;" style="text-align: center;">CS Number</th>
							<th width="180px;" style="text-align: left;">Sales Model</th>
							<th width="100px;" style="text-align: left;">Account Name</th>
							
							<th width="80px;" style="text-align: center;">Due Date</th>
							<th width="120px;" style="text-align: center;">Status</th>
							<th width="100px;" style="text-align: right;">Amount Due</th>
						</tr>
						';
				$total_amount_due = 0;
				$ctr = 1;
				$check_id = $row->CHECK_ID;
			}
				$amount_due = $row->AMOUNT_DUE != NULL ?  $row->AMOUNT_DUE : $row->INVOICE_AMOUNT_DUE;
				$data .= '<tr>
							<td style="text-align: center;">'. $ctr .'</td>
							<td style="text-align: center;">'.$row->CS_NUMBER.'</td>
							<td style="text-align: left;">'.$row->SALES_MODEL.'</td>
							<td style="text-align: left;">'.$row->ACCOUNT_NAME.'</td>
						
							<td style="text-align: center;">'.short_date($row->DUE_DATE).'</td>
							<td style="text-align: center;">'.$row->STATUS.'</td>
							<td style="text-align: right;">'.amount($amount_due).'</td>
						</tr>';
				
				$total_amount_due += $amount_due;
				if($ctr == 1){
					$nearest_due_date = $row->DUE_DATE;
					$order_type = strtok($row->ORDER_TYPE, ' ');
				}
				$ctr++;
		}
		
		$data .= '<tr>
					<td colspan="5" style="text-align: right;border-top: 1px solid #444;">&nbsp;</td>
					<td colspan="1" style="text-align: right;border-top: 1px solid #444;">&nbsp;</td>
					<td colspan="1" style="text-align: right;border-top: 1px solid #444;">'.amount($total_amount_due).'</td>
					
				</tr>
				<tr>
					<td colspan="7">&nbsp;</td>
				</tr>';
		
		$html = '<table border="0" style="padding: 3px;">
					<tr>
						<td colspan="7">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="7" style="font-size: 15px;text-align: center;">Check Details</td>
					</tr>
					'.$data.'
				</table>';
		
		//~ echo $html;

        $this->load->library('Pdf_P');

        $pdf = new Pdf_P('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Isuzu');
        $pdf->SetTitle('PDC Report');
        $pdf->SetSubject('PDC Report');
        $pdf->SetKeywords('PDC Report, isuzu');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->SetMargins(PDF_MARGIN_LEFT - 5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT - 5);
        $pdf->SetheaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 7, '', true);

        $pdf->AddPage('P', 'A4');
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        $pdf->Output(strtoupper($check_bank . ' ' . $check_number).".pdf",'I');
		
	}
}
