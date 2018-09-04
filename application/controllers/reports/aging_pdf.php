<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aging_pdf extends CI_Controller {
	
	var $pdf = NULL;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/aging_model');
		session_check();
	}
	
	public function index(){
		
		$orientation = 'P';
		
		$as_of_date = DateTime::createFromFormat('mdY', $this->uri->segment(4));
		$as_of_date =  $as_of_date->format('m/d/Y');
		//~ $profile_class_id = NULL;
		
		$this->pdf($orientation);
		$this->load->helper('profile_class_helper');
		$this->load->helper('date_helper');
		$this->load->helper('number_helper');
		
		$rows = $this->aging_model->get_receivables_aging(oracle_date($as_of_date));

		$this->pdf->AddPage($orientation);
		
		$data = '';
		foreach($rows as $row){
			$data .= '<tr>
						<td align="left">'.$row->PROFILE_CLASS.'</td>
						<td align="right">'.amount($row->CONTINGENT_RECEIVABLES).'</td>
						<td align="right">'.amount($row->CURRENT_RECEIVABLES).'</td>
						<td align="right">'.amount($row->PAST_DUE).'</td>
						<td align="right">'.amount($row->TOTAL).'</td>
					</tr>';
		}
		
		$html = '<table border="0" style="font-size: 12px;padding: 3px;">
					<tr>
						<td colspan="5" align="left">Profile Accounts Receivable</td>
					</tr>
					<tr>
						<td colspan="5" align="left">As of : '.$as_of_date.'</td>
					</tr>
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$html = '<table border="1" style="font-size: 11px;padding: 3px;">
					
					<tr style="background-color: #ccc;">
						<td align="left">Profile Class</td>
						<td align="right">Unpulledout Receivables</td>
						<td align="right">Current Receivables</td>
						<td align="right">Past Due Receivables</td>
						<td align="right">Total Receivables</td>
					</tr>
					'.$data.'
				</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$this->pdf->Output('areceivables_aging.pdf', 'I');
		
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
