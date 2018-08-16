<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_miscellaneous_pdf extends CI_Controller {
	
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
		//~ $lines = $this->invoice_model->get_parts_invoice_lines_print($invoice_id);
		$lines = $this->invoice_model->get_others_invoice_lines_print($invoice_id);
		
		$this->pdf->AddPage($orientation);
		
		$html = '<table border="0" style="padding: 1px 2px;font-size: 11px;">
					<tr>
						<td colspan="5" align="right" style="font-size: 14px;"><strong>SYSTEM COPY MISCELLANEOUS INVOICE</strong></td>
					</tr>
					<tr>
						<td colspan="5" align="right" style="font-size: 14px;"><span style="font-size: 12px;">Original Invoice No.</span><strong> '.$header->TRX_NUMBER.' </strong></td>
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
						<td colspan="2" width="240px" align="right">Date : '. date('F j, Y', strtotime($header->TRX_DATE)) .'</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td><strong>'. $header->PARTY_NAME .'</strong></td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td></td>
						<td><strong></strong></td>
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
						<td width="140px"></td>
						<td width="115px"></td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>Business Style : '. $header->CLASS_CODE . ' - ' . $header->BUSINESS_STYLE .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td></td>
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
		$net = 0;
		$vat = 0;
		$total = 0;
		foreach($lines as $line){
			//~ $data .= '<tr>
						//~ <td width="50px" align="center" style="borders-right:1px solid #000;">'.$ctr.'</td>
						//~ <td width="80px" align="center" style="borders-right:1px solid #000;">'.$line->PART_NUMBER.'</td>
						//~ <td width="226px" align="left" style="borders-right:1px solid #000;">'.$line->PART_DESCRIPTION.'</td>
						//~ <td width="40px" align="center" style="borders-right:1px solid #000;">'.$line->QTY.'</td>
						//~ <td width="80px" align="right" style="borders-right:1px solid #000;">'.amount($line->UNIT_PRICE).'</td>
						//~ <td width="197px" align="right">'.amount($line->AMOUNT).'</td>
					//~ </tr>';
			//~ $ctr++;
			$data .= '<tr>
						<td width="50px" align="center" style="borders-right:1px solid #000;">'.$ctr.'</td>
						<td width="80px" align="center" style="borders-right:1px solid #000;">&nbsp;</td>
						<td width="226px" align="left" style="borders-right:1px solid #000;">'.$line->DESCRIPTION.'</td>
						<td width="40px" align="center" style="borders-right:1px solid #000;">'.$line->QTY.'</td>
						<td width="80px" align="right" style="borders-right:1px solid #000;">'.amount($line->UNIT_SELLING_PRICE).'</td>
						<td width="197px" align="right">'.amount($line->TOTAL).'</td>
					</tr>';
			$ctr++;
			
			$vatable_sales += $line->VAT > 0 ? $line->UNIT_SELLING_PRICE : 0;
			$exempt += $line->VAT > 0 ? 0 : $line->UNIT_SELLING_PRICE;
			$net = $vatable_sales + $exempt;
			$vat += $line->VAT;
			$total = $net + $vat;
		}
					
		while($ctr <= 10){
			$data .= '<tr>
						<td style="border-right:1px solid #ffffff;">&nbsp;</td>
						<td style="border-right:1px solid #ffffff;">&nbsp;</td>
						<td style="border-right:1px solid #ffffff;">&nbsp;</td>
						<td style="border-right:1px solid #ffffff;">&nbsp;</td>
						<td style="border-right:1px solid #000000;">&nbsp;</td>
						<td style="border-right:1px solid #ffffff;">&nbsp;</td>
					</tr>';
			$ctr++;
		}
					
		
		
		$html = '<table border="1" cellpadding="0" style="padding: 2px;font-size: 11px;">
					<tr>
						<td colspan="2">
							<table border="1" style="padding:3px;">
								<tr style="font-weight: bold;">
									<td width="50px" align="center">#</td>
									<td width="80px" align="center">ITEM CODE</td>
									<td width="226px" align="center">DESCRIPTION</td>
									<td width="40px" align="center">QTY</td>
									<td width="80px" align="center">UNIT PRICE</td>
									<td width="197px" align="center">AMOUNT</td>
								</tr>
								'.$data.'
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" >
							<table style="padding:3px;">
								
							</table>
						</td>
					</tr>
					<tr>
						<td width="476px;">
							<table style="padding:10px;font-size: 11px;">
								<tr>
									<td>Reference : '.$header->ORDER_NUMBER.'</td>
								</tr>
								<tr>
									<td>'.$header->COMMENTS.'</td>
								</tr>
							</table>
						</td>
						<td width="197px">
							<table border="0" style="padding:3px;font-size: 11px;">
								<tr>
									<td style="text-align: left;width: 102px;"><span>Vatables Sales</span></td>
									<td style="text-align: right;width: 95px;">'. number_format($vatable_sales,2) .'</td>
								</tr>
								<tr>
									<td style="text-align: left;"><span>Exempted Sales</span></td>
									<td style="text-align: right;">'. number_format($exempt,2) .'</td>
								</tr>
								<tr>
									<td style="text-align: left;"><span>Zero Rated Sales</span></td>
									<td style="text-align: right;">0.00</td>
								</tr>
								<tr>
									<td style="text-align: left;"><span>Discount</span></td>
									<td style="text-align: right;">0.00</td>
								</tr>
								<tr>
									<td style="text-align: left;"><span>Amt. Net of Vat</span></td>
									<td style="text-align: right;">'. number_format($net,2) .'</td>
								</tr>
								<tr>
									<td style="text-align: left;"><span>VAT Amount</span></td>
									<td style="text-align: right;border-bottom: 1px solid #000;">'. number_format($vat,2) .'</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td style="text-align: left;font-size: 12px;"><strong>Total Sales '.$header->CURRENCY.' </strong></td>
									<td style="text-align: right;font-size: 12px;"><strong>'. number_format($total,2) .'</strong></td>
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
		
		$this->pdf->Output('vehicle_invoice.pdf', 'I');
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
