<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/TCPDF/tcpdf.php";
 
class Pdf_inv_watermark extends TCPDF {

	public function __construct() {
		parent::__construct();
	}

	//Page header
    public function Header() {
        //~ Logo
        $this->SetY(0,true,true);
		$image_file = base_url() . 'resources/images/isuzu_logo.jpg';
		$this->Image($image_file, 10, 5, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//~ watermark
		$img_file = base_url() . 'resources/images/watermark.png';
		// Render the image
        $this->Image($img_file, 30, 70, 0, 0, '', '', '', false, 300, '', false, false, 0);
		
		//~ IPC
		$this->SetFont('helvetica', 'B', 12);
		$html = "Philippines Corporation";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 41, $y = 5, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		$this->SetFont('helvetica', 'N', 9);
		$html = "114 Technology Avenue, Laguna Technopark Phase II, Biñan, Laguna 4024 Philippines";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 11, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		$html = "Tel. No. (049) 541-0224 to 26	|	Fax No. (+632) 842-0202";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		$html = "Treasury Portal";
		$this->writeHTMLCell($w = 0, $h = 0, $x = 178, $y = 15, $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
		
		//~ line
		//~ $style = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

		$this->Line(10, 21, 200, 21, $style);

    }
    
    public function Header2() {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        //~ $img_file = './your-watermark.jpg';
        $img_file = base_url() . 'resources/images/watermark.png';

        // Render the image
        $this->Image($img_file, 30, 70, 0, 0, '', '', '', false, 300, '', false, false, 0);

        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }

    // Page footer
   public function Footer() {

		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell('', '', 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), '', false, 'C', 0, '', 0, false, 'T', 'C');  
    }

}
