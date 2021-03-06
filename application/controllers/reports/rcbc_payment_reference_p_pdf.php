<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rcbc_payment_reference_p_pdf extends CI_Controller {
	
	var $pdf = NULL;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/payment_model');
		session_check();
	}
	
	public function index(){
		
		
		//~ print_r($this->input->post());
		//~ die();
		
		$invoice_numbers = "'".implode("','",$this->input->post('invoice_numbers'))."'";
		//~ echo $invoice_numbers;
		$orientation = 'P';
		
		$this->pdf($orientation);
		$this->load->helper('date_helper');
		$this->load->helper('number_helper');
		
		$rows = $this->payment_model->get_parts_invoiced($this->session->tre_portal_customer_id, $invoice_numbers);

		//~ echo '<pre>';
		//~ print_r($rows);
		//~ echo '</pre>';
		//~ die();
		

		$this->pdf->AddPage($orientation);
		
		$html = '<table style="font-size: 12px;padding: 3px;">
					<tr>
						<td align="center" colspan="2" style="font-size: 15px;">Bills Payment Reference Form (RCBC)</td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 15px;">&nbsp;</td>
					</tr>
					<tr>
						<td align="left" >Dealer Name : '.$rows[0]->PARTY_NAME.'</td>
						<td align="right">Date: '.date('m/d/Y').'</td>
					</tr>
					<tr>
						<td align="left" >Branch Name : '.$rows[0]->ACCOUNT_NAME.' '.$rows[0]->CUSTOMER_ID.'</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr>
						<td align="left" >Sales Type : Parts</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr>
						<td align="left" >Payment Reference : '.$rows[0]->CUSTOMER_ID.'-'.date('mdyHis').'</td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$data = '';
		$count = 1;
		$total = 0;
		foreach($rows as $row){
			$data .= '<tr>
						<td width="30px">'.$count.'</td>
						<td width="120px">'.$row->INVOICE_NUMBER.'</td>
						<td width="120px">'.$row->PAYMENT_TERM.'</td>
						<td width="90px">'.short_date($row->DUE_DATE).'</td>
						<td width="130px" align="right">'.amount($row->INVOICE_AMOUNT).'</td>
						<td width="130px" align="right">'.amount($row->AMOUNT_DUE).'</td>
					</tr>';
			$count++;
			$total += $row->AMOUNT_DUE;
		}
		
		$html = '<table border="1" style="font-size: 10px;padding: 3px;">
					<thead>
						<tr style="background-color: #ccc;">
							<th width="30px">#</th>
							<th width="120px">Invoice Number</th>
							<th width="120px">Payment Terms</th>
							<th width="90px">Due Date</th>
							<th width="130px" align="right">Invoice Amount</th>
							<th width="130px" align="right">Amount Due</th>
						</tr>
					</thead>
					<tbody>
						'.$data.'
					</tbody>
				</table>
				
				<table border="0" style="font-size: 11px;padding: 3px;">
					<tbody>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td width="620px" align="right">Total Amount Due : '.amount($total).'</td>
						</tr>
					</tbody>
				</table>
				
				';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$this->pdf->Output('payment_reference_rcbc.pdf', 'I');
		
	}
	
	public function pdf($orientation){
		
		if($orientation == 'P'){
			// generate pdf content
			$this->load->library('Pdf_P');
			// create new PDF document
			$this->pdf = new PDF_P(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		}
		else{
			// generate pdf content
			$this->load->library('Pdf_L');
			// create new PDF document
			$this->pdf = new PDF_L(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		}
		// set document information
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor('Isuzu');
		$this->pdf->SetTitle('IPC Treasury Portal');
		$this->pdf->SetSubject('IPC Treasury Portal');
		$this->pdf->SetKeywords('IPC Treasury Portal');
		// set default header data
		$this->pdf->SetheaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$this->pdf->setFooterData(array(0,0,0), array(0,0,0));
		// set header and footer fonts
		$this->pdf->setheaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT - 5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT - 5);
		$this->pdf->SetheaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$this->pdf->setLanguageArray($l);
		}
		// set default font subsetting mode
		$this->pdf->setFontSubsetting(true);
	}
}
