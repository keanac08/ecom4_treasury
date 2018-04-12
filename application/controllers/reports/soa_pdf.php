<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soa_pdf extends CI_Controller {
	
	var $pdf = NULL;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/soa_model');
		session_check();
	}
	
	public function index(){
		
		$sales_type = $this->uri->segment(4);
		
		if(in_array($sales_type, array('fleet'))){
			$orientation = 'L';
		}
		else{
			$orientation = 'P';
		}
		
		$this->pdf($orientation);
		$this->load->helper('number_helper');
		$this->load->helper('date_helper');
	
		$customer_id = $this->uri->segment(6) / 101403;
		$as_of_date = DateTime::createFromFormat('mdY', $this->uri->segment(7));
		$as_of_date =  $as_of_date->format('m/d/Y');
		
		$profile_class_ids = get_profile_class_ids($sales_type);
		
		$rows = $this->soa_model->get_soa_per_customer($customer_id, $profile_class_ids, date('d-M-y', strtotime($as_of_date)));
		$summary = $this->soa_model->get_soa_per_customer_summary($customer_id, $profile_class_ids, date('d-M-y', strtotime($as_of_date)));
		$summary = $summary[0];
		
		$customer = $this->soa_model->get_customer_details($customer_id, $profile_class_ids);
		
		$this->pdf->AddPage($orientation);
		
		//~ $th = '';
		$cs_number = 0;
		$pdc_number = 0;
		$fleet_name = 0;
		$cust_po_number = 0;
		$width = '625';
		$th = '';
		
		if(in_array($sales_type, array('vehicle', 'fleet'))){ 
			$cs_number = 1;
			$pdc_number = 1;
			$th = '<td width="70" align="center">PDC<br />Number</td><td width="1"></td>';
			$width = '675';
			
			if(in_array($sales_type, array('fleet'))){ 
				$width = '870';
				$fleet_name = 1;
				$th = '<td width="60" align="center">PDC<br />Number</td><td width="195" align="center">Fleet<br />Name</td>';
			}
		}
		else if(in_array($sales_type, array('parts'))){ 
			$width = '645';
			$cust_po_number = 1;
			$th = '<td width="90" align="center">Cust Po<br />Number</td><td width="1"></td>';
		}

		$html = '<table border="0" style="font-size: 8px;padding: 1px">
					<tbody>
						<tr>
							<td width="'.($width-330).'" style="font-size: 10px;">Statement of Account (' . camelcase($sales_type) . ')</td>
							<td rowspan="5" width="320" align="right">
								<table border="1" style="font-size: 8px;padding: 3px">
									<tr>
										<td width="320" colspan="2" align="center">Transaction Summary as of '.$as_of_date.'</td>
									</tr>
									<tr>
										<td width="200" align="left">Total Past Due Receivables</td>
										<td width="120" align="right">'.amount($summary->PASTDUE_RECEIVABLES).'</td>
									</tr>
									<tr>
										<td width="200" align="left">Total Current Receivables</td>
										<td width="120" align="right">'.amount($summary->CURRENT_RECEIVABLES).'</td>
									</tr>
									<tr>
										<td width="200" align="left">Total Contingent Receivables</td>
										<td width="120" align="right">'.amount($summary->CONTINGENT_RECEIVABLES).'</td>
									</tr>
									<tr>
										<td width="200" align="left" style="background-color: #f1f1f1;"><strong>Total Receivables</strong></td>
										<td width="120" align="right" style="background-color: #f1f1f1;"><strong>'.amount($summary->TOTAL_RECEIVABLES).'</strong></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td align="left" style="font-size: 9px;">' . $customer->CUSTOMER_ID . ' - ' . $customer->CUSTOMER_NAME . ' </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<table border="0" style="font-size: 8px;padding: 2px">
					<tbody>
						<tr style="font-weight: bold;">
							<td width="' . ($cs_number == 1 ? 60:1) . '" align="center">' . ($cs_number == 1 ? 'CS<br />Number':'') . '</td>
							<td width="70" align="center">Invoice<br />Number</td>
							<td width="70" align="center">Invoice<br />Date</td>
							<td width="70" align="center">' . ($cs_number == 1 ? 'Pullout':'Delivery') . '<br />Date</td>
							<td width="60" align="center">Payment<br />Terms</td>
							<td width="75" align="right">Invoice<br />Amount</td>
							<td width="55" align="right">WHT<br />Amount</td>
							<td width="75" align="right">&nbsp;<br />Balance</td>
							<td width="70" align="center">Days<br />Overdue</td>
							'.$th.'
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #333;" width="'.$width.'" align="center"></td>
						</tr>
					</tbody>
				</table>
				';
					
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$ctr = 0;
	
		foreach($rows as $row){
			if($row->INVOICE_ID != NULL){
				if($ctr == 0){
					$html = '<table border="0" style="font-size: 8px;padding: 2px">
								<tbody>';
					
				}
				$html .= '<tr>
							<td width="' . ($cs_number == 1 ? 60:1) . '" align="center">' . ($cs_number == 1 ? $row->CS_NUMBER:'') . '</td>
							<td width="70" align="center">'.$row->INVOICE_NO.'</td>
							<td width="70" align="center">'.short_date($row->INVOICE_DATE).'</td>
							<td width="70" align="center">'.short_date($row->DELIVERY_DATE).'</td>
							<td width="60" align="center">'.$row->PAYMENT_TERM.'</td>
							<td width="75" align="right">'.amount($row->TRANSACTION_AMOUNT).'</td>
							<td width="55" align="right">'.amount($row->WHT_AMOUNT).'</td>
							<td width="75" align="right">'.amount($row->BALANCE).'</td>
							<td width="70" align="center">'.$row->DAYS_OVERDUE.'</td>
							<td width="' . ($pdc_number == 1 ? 70:1) . '" align="center">' . ($pdc_number == 1 ? $row->CHECK_NO:'') . '</td>
							<td width="' . ($cust_po_number == 1 ? 90:1) . '" align="center">' . ($cust_po_number == 1 ? $row->CUST_PO_NUMBER:'') . '</td>
							<td width="' . ($fleet_name == 1 ? 195:1) . '" align="left">' . ($fleet_name == 1 ? $row->FLEET_NAME:'') . '</td>
						</tr>
					';
				$ctr++;
			}
			else if(($row->INVOICE_ID == NULL AND $row->DUE_DATE != NULL) OR($row->INVOICE_ID == NULL AND $row->DUE_DATE == NULL AND $row->DELIVERY_DATE == NULL)){
				
				$subtotal = ($row->DELIVERY_DATE == NULL ? 'Subtotal' : ($row->DAYS_OVERDUE > 0 ? 'Subtotal Past Due':'Subtotal Due'));
				$html .= '<tr style="font-weight: bold;background-color: #DDDDDD;">
							<td colspan="5" width="' . ($cs_number == 1 ? 330:271) . '"  align="center">'.$subtotal.' - '.short_date($row->DUE_DATE).'</td>
							<td width="75" align="right">'.amount($row->TRANSACTION_AMOUNT).'</td>
							<td width="55" align="right">'.amount($row->WHT_AMOUNT).'</td>
							<td width="75" align="right">'.amount($row->BALANCE).'</td>
							<td colspan="4" width="' . ($cs_number == 1 ? $width-535:$width-482) . '">&nbsp;</td>
						</tr>
					</tbody>
				</table>';
				$this->pdf->writeHTML($html, true, false, true, false, '');
				$ctr = 0;
			}
		}
		
		$this->pdf->Output('soa.pdf', 'I');
		
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
