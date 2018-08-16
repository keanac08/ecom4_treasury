<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_parts_pdf extends CI_Controller {
	
	var $pdf = NULL;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/invoice_model');
		session_check();
	}
	
	public function index(){
		
		$orientation = 'P';
		
		$this->pdf($orientation);
		$this->load->helper('number_helper');
		
		$invoice_id = $this->uri->segment(4);
		$header = $this->invoice_model->get_parts_invoice_header_print($invoice_id);
		
		//~ $header->TRX_NUMBER;die();
		$lines = $this->invoice_model->get_parts_invoice_lines_print($invoice_id);
		
		$this->pdf->AddPage($orientation);
		
		$html = '<table border="0" style="padding: 1px 2px;font-size: 12px;">
					<tr>
						<td colspan="5" align="right" style="font-size: 14px;"><strong>SYSTEM COPY SALES INVOICE (PARTS)</strong></td>
					</tr>
					<tr>
						<td colspan="5" align="right" style="font-size: 14px;"><span style="font-size: 12px;">Original Sales Invoice No.</span><strong> '.$header->TRX_NUMBER.' </strong></td>
					</tr>
					<tr>
						<td colspan="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="10px">&nbsp;</td>
						<td width="377px">&nbsp;</td>
						<td width="10px" style="border-bottom: 1px solid #000;">&nbsp;</td>
						<td width="35px">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="10px" style="border-top: 1px solid #000;border-left: 1px solid #000;">&nbsp;</td>
						<td width="387px" style="border-right: 1px solid #000;">&nbsp;</td>
						<td width="35px"></td>
						<td width="90px"></td>
						<td width="165px"></td>
					</tr>
					<tr>
						<td width="10px" style="border-left: 1px solid #000;">&nbsp;</td>
						<td width="387px" style="font-size:10px;">SOLD TO</td>
						<td width="35px" style="border-left: 1px solid #000;">&nbsp;</td>
						<td width="90px">Date</td>
						<td width="165px"><strong>'. date('F j, Y', strtotime($header->TRX_DATE)) .'</strong></td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td><strong>'. $header->PARTY_NAME .'</strong></td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>PO Ref</td>
						<td><strong>'. $header->CUST_PO_NUMBER .'</strong></td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>'. $header->ADDRESS .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>TIN# : '. $header->TAX_REFERENCE .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td width="140px">Terms of Payment</td>
						<td width="115px">'.$header->PAYMENT_TERMS.'</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>Business Style : '. $header->CLASS_CODE . ' - ' . $header->BUSINESS_STYLE .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>(&nbsp;&nbsp;) Cash &nbsp; (&nbsp;&nbsp;) Check</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;border-bottom: 1px solid #000;">&nbsp;</td>
						<td>&nbsp;</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td width="387px">&nbsp;</td>
						<td width="10px" style="border-top: 1px solid #000;">&nbsp;</td>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$data = '';
		$ctr = 1;
		$total_qty = 0;
		$dr_number = '';
		$picklist_number = '';
		$vatable_sales = 0;
		$exempt = 0;
		$zero_rated = 0;
		$discount = 0;
		$net = 0;
		$vat = 0;
		$total = 0;
		foreach($lines as $line){
			$data .= '<tr>
						<td width="60px" align="center">'.$ctr.'</td>
						<td width="90px">'.$line->PART_NUMBER.'</td>
						<td width="290px">'.$line->PART_DESCRIPTION.'</td>
						<td width="50px" align="center">'.$line->QTY.'</td>
						<td width="90px" align="right">'.amount($line->UNIT_PRICE).'</td>
						<td width="90px" align="right">'.amount($line->AMOUNT).'</td>
					</tr>';
			$ctr++;
			$total_qty += $line->QTY;
			$dr_number = $line->DR_NUMBER;
			$picklist_number = $line->PICKLIST_NUMBER;
			
			$vatable_sales += $line->VAT > 0 ? $line->AMOUNT:0;
			$exempt += $line->VAT == 0 ? $line->AMOUNT:0;
			$discount += $line->DISCOUNT;
			$net = $vatable_sales + $discount;
			$vat += $line->VAT;
			$total = $net + $vat;
		}

		$data .= '<tr>
					<td colspan="3">&nbsp;</td>
					<td align="center" style="border-top: 1px solid #000;">'.$total_qty.'</td>
					<td colspan="2">&nbsp;</td>
				</tr>';
					
		while($ctr <= 19){
			$data .= '<tr>
						<td colspan="6">&nbsp;</td>
					</tr>';
			$ctr++;
		}
					
		
		
		$html = '<table border="1" cellpadding="0" style="padding: 2px;font-size: 12px;">
					<tr>
						<td colspan="2">
							<table border="0" style="font-size: 11px;padding:2px;">
								<tr style="font-weight: bold;">
									<td width="60px" align="center">Item No</td>
									<td width="90px" align="left">Reference</td>
									<td width="290px" align="left">Description</td>
									<td width="50px" align="center">Qty</td>
									<td width="90px" align="right">Unit Price</td>
									<td width="90px" align="right">Amount</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" >
							<table style="font-size: 11px;padding:2px;">
								'.$data.'
							</table>
						</td>
					</tr>
					<tr>
						<td width="445px;">
							<table border="0" style="padding: 0 2px;font-size: 11px;">
								<tr>
									<td colspan="2">Shipping Instructions</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td width="115px"><strong>Remarks :</strong></td>
									<td>'.$header->REMARKS.'</td>
								</tr>
								<tr>
									<td><strong>Additional Remarks :</strong></td>
									<td>'.$header->ADDL_REMARKS.'</td>
								</tr>
								<tr>
									<td><strong>Order Number :</strong></td>
									<td>'.$header->ORDER_NUMBER.'</td>
								</tr>
								<tr>
									<td><strong>DR Number :</strong></td>
									<td>'.$dr_number.'</td>
								</tr>
								<tr>
									<td><strong>DR Reference No. :</strong></td>
									<td>'.$header->DR_REFERENCE.'</td>
								</tr>
								<tr>
									<td><strong>Picklist Number :</strong></td>
									<td>'.$picklist_number.'</td>
								</tr>
								
							</table>
						</td>
						<td width="228px">
							<table border="0" style="padding: 1px 3px;font-size: 12px;">
								<tr>
									<td style="text-align: left;width: 120px;"><strong>Vatables Sales</strong></td>
									<td style="text-align: right;width: 108px;">'. number_format($vatable_sales,2) .'</td>
								</tr>
								<tr>
									<td style="text-align: left;"><strong>Exempted Sales</strong></td>
									<td style="text-align: right;">0.00</td>
								</tr>
								<tr>
									<td style="text-align: left;"><strong>Zero Rated Sales</strong></td>
									<td style="text-align: right;">0.00</td>
								</tr>
								<tr>
									<td style="text-align: left;"><strong>Discount</strong></td>
									<td style="text-align: right;">'. number_format($discount,2) .'</td>
								</tr>
								<tr>
									<td style="text-align: left;"><strong>Amt. Net of Vat</strong></td>
									<td style="text-align: right;">'. number_format($net,2) .'</td>
								</tr>
								<tr>
									<td style="text-align: left;"><strong>VAT Amount</strong></td>
									<td style="text-align: right;border-bottom: 1px solid #000;">'. number_format($vat,2) .'</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td style="text-align: left;font-size: 14px;"><strong>Total Sales '.$header->CURRENCY.' </strong></td>
									<td style="text-align: right;font-size: 14px;"><strong>'. number_format($total,2) .'</strong></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br />
				<br />
				<br />
				<br />
				<span align="center" style="font-size: 12px;"><b>System Generated Document</b></span><br />
				<span align="center" style="font-size: 12px;"><i>Not valid for any tax claim. Original copy to be sent to your office.</i></span><br />
				<span align="center" style="font-size: 12px;"><i>This is for reference purposes only.</i></span>
				';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$this->pdf->Output('parts_invoice.pdf', 'I');
	}
	
	public function pdf($orientation){
		
		if($orientation == 'P'){
			// generate pdf content
			$this->load->library('Pdf_inv_watermark');
			// create new PDF document
			$this->pdf = new PDF_INV_WATERMARK(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
