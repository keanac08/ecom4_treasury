<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Collection_receipt_pdf extends CI_Controller {
	
	var $pdf = NULL;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/receipt_model');
		session_check();
	}
	
	public function index(){
		
		$receipt_id = $this->uri->segment(4);
		
		//~ $n = 214324231432.43;
		//~ $whole = floor($n);      // 1
		//~ $fraction = number_format($n - $whole,2); // .25
		
		//~ echo $f;
		
		//~ $f = new NumberFormatter("En", NumberFormatter::SPELLOUT);
		//~ echo UCWORDS($f->format($whole));
		//~ echo '<br />';
		//~ echo UCWORDS($f->format($fraction));
		//~ die();
		
		$orientation = 'P';
		
		$this->pdf($orientation);
		$this->load->helper('profile_class_helper');
		$this->load->helper('number_helper');
		$this->load->helper('date_helper');
		$this->load->helper('string_helper');
		
		$header = $this->receipt_model->get_collection_receipts_header($receipt_id);
		$header = $header[0];
		$lines  = $this->receipt_model->get_collection_receipts_lines($receipt_id);
		
		$n = $header->RECEIPT_AMOUNT;
		$receipt_amount = amount($header->RECEIPT_AMOUNT);
		$whole = floor($n);      // 1
		$fraction = number_format($n - $whole,2); // .25
		$fraction = $fraction * 100;
		
		if($fraction > 0){
			$fraction = ' and ' . $fraction . '/100';
		}
		else{
			$fraction = '';
		}
		
		$f = new NumberFormatter("En", NumberFormatter::SPELLOUT);
		$receipt_amount_words =  UCWORDS($f->format($whole));
		
		$this->pdf->AddPage($orientation);
		
		//~ HEADER ---------------------------------------------------------------------------------------------------
		$html = '<table border="0" style="padding: 2px">
					<tr>
						<td colspan="2" align="right" style="font-size: 17px;"><strong>COLLECTION RECEIPT</strong></td>
					</tr>
					<tr>
						<td align="right" style="font-size: 13px;" width="550px">No. :</td>
						<td align="right" style="font-size: 15px;" width="123px">'.$header->RECEIPT_NUMBER.'</td>
					</tr>
					<tr>
						<td align="right" style="font-size: 13px;" width="550px">Date :</td>
						<td align="right" style="font-size: 12px;" width="123px"><div style="font-size:1px;">&nbsp;</div>'.$header->DATE1.'</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$html = '<table border="0" style="font-size: 12px;padding: 2px 4px;">
					<tr>
						<td width="123px">Received From</td>
						<td width="550px" style="border-bottom: solid 1px #000;"><strong>'.$header->PARTY_NAME.'</strong></td>
					</tr>
					<tr>
						<td width="123px">Address</td>
						<td width="550px" style="border-bottom: solid 1px #000;">'.$header->ADDRESS_ALL.'</td>
					</tr>
					<tr>
						<td width="123px">TIN</td>
						<td width="550px" style="border-bottom: solid 1px #000;">'.$header->TAX_R_ALL.'</td>
					</tr>
					<tr>
						<td width="123px">Business Style</td>
						<td width="550px" style="border-bottom: solid 1px #000;">'.$header->BUSINESS_STYLE.'</td>
					</tr>
					<tr>
						<td width="123px">Pesos</td>
						<td width="550px" style="border-bottom: solid 1px #000;">'.$receipt_amount_words.$fraction.' (PHP '.$receipt_amount.')</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$html = '<table border="0" style="font-size: 12px;padding: 2px 4px;">
					<tr>
						<td>Received payment for : </td>
					</tr>
					<tr>
						<td>INVOICES</td>
					</tr>
				 </table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		//~ LINES ----------------------------------------------------------------------------------------------------
		$column = 0;
		$total_wht = 0;
		$total_balance = 0;
		$total_amount_applied = 0;
		$total_amount_applied_2 = 0;
		$negative_amount_applied = 0;
		$data = '<tr>
					<td align="center" width="110px">Invoice Number</td>
					<td align="center" width="110px">Amount</td>
					<td align="center" width="110px">Invoice Number</td>
					<td align="center" width="110px">Amount</td>
				</tr>';
		foreach($lines as $line){
			if($line->AMOUNT_APPLIED > 0){
				if($column == 0){
					$data .= '<tr>';
				}
				$column++;
				$data .= '<td align="center">'.$line->TRX_NUMBER.'</td>
						  <td align="right">'.amount($line->AMOUNT_APPLIED).'</td>';
				
				if($column == 2){
					$data .= '</tr>';
					$column = 0;
				}
				//~ $total_wht += $line->WHT;
				$total_balance += $line->BALANCE_PAYABLE;
				$total_amount_applied += $line->AMOUNT_APPLIED;
			}
			else{
				$negative_amount_applied += $line->AMOUNT_APPLIED;
			}
			$total_amount_applied_2 += $line->AMOUNT_APPLIED;
		}
		if($column == 1){
			$data .= '<td>&nbsp;</td>
						  <td>&nbsp;</td>
					  </tr>';
		}
		
		$html = '<table border="1" style="font-size: 10px;padding: 2px 6px;">
					'.$data.'
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		//~ AMOUNT ----------------------------------------------------------------------------------------------------
		$html = '<table border="0" style="font-size: 11px;" cellpadding="0">
					<tr>
						<td width="300px">&nbsp;</td>
						<td width="50px">&nbsp;</td>
						<td width="320px">Form of Payment</td>
					</tr>
					<tr>
						<td width="300px">
							<table border="1" style="padding: 2px 4px;">
								<tr>
									<td align="left">Total Amount Paid</td>
									<td align="right">'.amount($total_amount_applied).'</td>
								</tr>
								<tr>
									<td align="left">Balance Unpaid Invoice</td>
									<td align="right">'.amount($total_balance).'</td>
								</tr>
								<tr>
									<td align="left">Withholding Tax</td>
									<td align="right">'.amount($negative_amount_applied * -1).'</td>
								</tr>
								<tr>
									<td align="left">Advances</td>
									<td align="right">'.amount($header->RECEIPT_AMOUNT - $total_amount_applied_2).'</td>
								</tr>
							</table>
						</td>
						<td width="50px">&nbsp;</td>
						<td width="320px">
							<table border="1" style="padding: 2px 4px;">
								<tr>
									<td width="120px">Currency</td>
									<td width="80px">PHP</td>
									<td width="120px" align="right">'.$receipt_amount.'</td>
								</tr>
								<tr>
									<td width="120px">Check</td>
									<td width="80px">'.$header->CHECK_BANK.' ' .$header->CHECK_NUMBER. '</td>
									<td width="120px" align="right">&nbsp;</td>
								</tr>
								<tr>
									<td width="120px"><strong>Total Payment</strong></td>
									<td width="80px">&nbsp;</td>
									<td width="120px" align="right"><strong>'.$receipt_amount.'</strong></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		//~ NOTES ----------------------------------------------------------------------------------------------------
		$html = '<table border="0" cellpadding="0" style="font-size: 9px;padding: 2px 4px;">
					<tr>
						<td colspan="2" width="470px" style="font-size: 12px;">NOTE : Checks paid shall not be considered as payments until honored by the bank.</td>
						<td width="200px">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" width="470px">&nbsp;</td>
						<td align="center" width="200px" style="font-size: 12px;">This is a system generated receipt.</td>
					</tr>
					<tr>
						<td colspan="2" width="470px">&nbsp;</td>
						<td align="center" width="200px" style="font-size: 12px;">No Signature Required</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td align="center" colspan="3" style="font-size: 10px;"><strong>THIS COLLECTION RECEIPT SHALL BE VALID FOR FIVE (5) YEARS FROM THE DATE OF THE PERMIT TO USE</strong></td>
					</tr>
					<tr>
						<td align="center" colspan="3" style="font-size: 10px;"><strong>“THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX”</strong></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="370px">&nbsp;</td>
						<td width="150px">BIR PERMIT TO USE NO.:</td>
						<td width="150px">1701_0124_PTU_CAS_000056</td>
					</tr>
					<tr>
						<td width="370px">&nbsp;</td>
						<td width="150px">Date issued:</td>
						<td width="150px">January 3, 2017</td>
					</tr>
					<tr>
						<td width="370px">&nbsp;</td>
						<td width="150px">Valid until:</td>
						<td width="150px">December 31, 2021</td>
					</tr>
					<tr>
						<td width="370px">&nbsp;</td>
						<td width="150px">Series No.:</td>
						<td width="150px">70100000001- 70199999999</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		
		$this->pdf->Output('collection_receipt.pdf', 'I');
		
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
