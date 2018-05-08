<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_vehicle_pdf extends CI_Controller {
	
	var $pdf = NULL;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/invoice_model');
		session_check();
	}
	
	public function index(){
		
		$orientation = 'P';
		
		$this->pdf($orientation);
		//~ $this->load->helper('profile_class_helper');
		//~ $this->load->helper('date_helper');
		//~ $this->load->helper('number_helper');
		
		$invoice_id = $this->uri->segment(4);
		$row = $this->invoice_model->get_vehicle_invoice_print($invoice_id);
		$row = $row[0];
		
		$this->pdf->AddPage($orientation);
		
		$html = '<table border="0" style="padding: 1px 2px;font-size: 12px;">
					<tr>
						<td colspan="5" align="right" style="font-size: 14px;"><strong>SYSTEM COPY SALES INVOICE (VEHICLE)</strong></td>
					</tr>
					<tr>
						<td colspan="5" align="right" style="font-size: 14px;"><span style="font-size: 12px;">Original Sales Invoice No.</span><strong> '.$row->TRX_NUMBER.' </strong></td>
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
						<td width="90px">Date</td>
						<td width="165px"><strong>'. date('F j, Y', strtotime($row->TRX_DATE)) .'</strong></td>
					</tr>
					<tr>
						<td width="10px" style="border-left: 1px solid #000;">&nbsp;</td>
						<td width="387px" style="font-size:10px;">SOLD TO</td>
						<td width="35px" style="border-left: 1px solid #000;">&nbsp;</td>
						<td width="90px">PO/SO Ref</td>
						<td width="165px"><strong>'. $row->SO_NUMBER .'</strong></td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td><strong>'. $row->PARTY_NAME .'</strong></td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>DR No.</td>
						<td><strong>'. $row->DR_NUMBER .'</strong></td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>'. $row->ADDRESS .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>TIN# : '. $row->TAX_REFERENCE .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td width="140px">Terms of Payment</td>
						<td width="115px">'.$row->PAYMENT_TERMS.'</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>Business Style : '. $row->CLASS_CODE . ' - ' . $row->BUSINESS_STYLE .'</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>(&nbsp;&nbsp;) Cash &nbsp; (&nbsp;&nbsp;) Check</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="border-left: 1px solid #000;border-bottom: 1px solid #000;">&nbsp;</td>
						<td>&nbsp;</td>
						<td style="border-left: 1px solid #000;">&nbsp;</td>
						<td>With Orig copy of CSR</td>
						<td>'.$row->CSR_NUMBER.'</td>
					</tr>
					<tr>
						<td width="387px">&nbsp;</td>
						<td width="10px" style="border-top: 1px solid #000;">&nbsp;</td>
						<td>&nbsp;</td>
						<td>CSR (OR)</td>
						<td>'.$row->CSR_OR_NUMBER.'</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		if($row->FLEET_NAME != NULL){
			$fleet_name = '<tr>
								<td colspan="2">
									<table border="0" style="padding: 1px 3px;font-size: 12px;">
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td style="font-size: 10px;"><strong>Dealers Fleet Account :</strong></td>
										</tr>
										<tr>
											<td><strong>'. $row->FLEET_NAME .'</strong></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>';
		}
		else{
			$fleet_name = '<tr>
								<td colspan="2">
									<table>
										<tr>
											<td>&nbsp;</td>
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
									</table>
								</td>
							</tr>';
		}
		
		$html = '<table border="1" cellpadding="0" style="padding: 2px;font-size: 12px;">
					<tr>
						<td colspan="2">
							<table border="0" style="font-size: 11px;padding:2px;">
								<tr style="font-weight: bold;">
									<td width="60px" align="center">Item No</td>
									<td width="90px" align="center">Reference</td>
									<td width="290px" align="center" colspan="2">Description</td>
									<td width="50px" align="center">Qty</td>
									<td width="90px" align="right">Unit Price</td>
									<td width="90px" align="right">Amount</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table border="0"  style="font-size: 12px;padding: 1px;">
								<tr>
									<td colspan="7" style="font-size: 5px;">&nbsp;</td>
								</tr>
								<tr>
									<td width="60px" style="text-align: center;"><strong>1</strong></td>
									<td width="90px" style="text-align: left;"><strong>CS NO.:</strong></td>
									<td width="95px"style="text-align: left;"><strong>Model</strong></td>
									<td width="195px"style="text-align: left;"><strong>'.$row->SALES_MODEL.'</strong></td>
									<td width="50px"style="text-align: center;"><strong>1</strong></td>
									<td width="90px"style="text-align: right;"><strong>'. number_format($row->VATABLE_SALES,2) .'</strong></td>
									<td width="90px"style="text-align: right;"><strong>'. number_format($row->VATABLE_SALES,2) .'</strong></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td align="left" rowspan="2" style="font-size: 20px;"><strong>'.$row->CS_NUMBER.'</strong></td>
									<td align="left"><strong>Lot No</strong></td>
									<td align="left"><strong>'.$row->LOT_NUMBER.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td align="left"><strong>Serial No</strong></td>
									<td align="left"><strong>'.$row->CHASSIS_NUMBER.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td align="left"><strong>WB No.:</strong></td>
									<td align="left"><strong>Engine</strong></td>
									<td align="left"><strong>'.$row->ENGINE_TYPE.'-'.$row->ENGINE_NO.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td align="left"><strong>'.$row->WB_NUMBER.'</strong></td>
									<td align="left"><strong>Color</strong></td>
									<td align="left"><strong>'.$row->BODY_COLOR.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td align="left"><strong>GVW</strong></td>
									<td align="left"><strong>'.$row->GVW.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td align="center" colspan="2">&nbsp;</td>
									<td align="left"><strong>Fuel</strong></td>
									<td align="left"><strong>'.$row->FUEL.'</strong></td>
									<td colspan="3" style="text-align: center;">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td align="left"><strong>Key No</strong></td>
									<td align="left"><strong>'.$row->KEY_NO.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td align="left"><strong>Tire Specs</strong></td>
									<td align="left"><strong>'.$row->TIRE_SPECS.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td align="left"><strong>Battery</strong></td>
									<td align="left"><strong>'.$row->BATTERY.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td align="left"><strong>Displacement</strong></td>
									<td align="left"><strong>'.$row->DISPLACEMENT.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td align="left"><strong>Year Model</strong></td>
									<td align="left"><strong>'.$row->YEAR_MODEL.'</strong></td>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="7">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="7">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="455px;">
							<table border="0" style="padding: 3px;font-size: 9px;">
								<tr>
									<td width="245px">'.nl2br($row->ITEMS1).'</td>
									<td width="215px">'.nl2br($row->ITEMS2).'</td>
								</tr>
							</table>
						</td>
						<td width="218px">
							<table border="0" style="padding: 1px 3px;font-size: 12px;">
								<tr>
									<td width="125px"><strong>Vatables Sales</strong></td>
									<td align="right" width="93px">'. number_format($row->VATABLE_SALES,2) .'</td>
								</tr>
								<tr>
									<td><strong>Exempted Sales</strong></td>
									<td align="right">'. number_format($row->EXEMPT,2) .'</td>
								</tr>
								<tr>
									<td><strong>Zero Rated Sales</strong></td>
									<td align="right">0.00</td>
								</tr>
								<tr>
									<td><strong>Discount</strong></td>
									<td align="right">'. number_format($row->DISCOUNT,2) .'</td>
								</tr>
								<tr>
									<td><strong>Amt. Net of Vat</strong></td>
									<td align="right">'. number_format($row->AMT_NET_OF_VAT,2) .'</td>
								</tr>
								<tr>
									<td><strong>VAT Amount</strong></td>
									<td align="right" style="border-bottom: 1px solid #000;">'. number_format($row->VAT_AMOUNT,2) .'</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td style="font-size: 13px;"><strong>Total Sales '.$row->CURRENCY.'</strong></td>
									<td align="right" style="font-size: 13px;"><strong>'. number_format($row->TOTAL_SALES,2) .'</strong></td>
								</tr>
							</table>
						</td>
					</tr>
					'.$fleet_name.'
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
