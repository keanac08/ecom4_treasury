<?php 
class Security_bank extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('file');
		$this->load->model('order_management/security_bank_model');
		//~ session_check();
	}
	
	public function export_tagged(){
		
		$this->load->library('excel');
		$this->load->helper('date_helper');
		
		$writer = new XLSXWriter();
		
		$header = array(
						'CUSTOMER_ID' => 'string',
						'INVOICE_NUMBER' => 'string',
						'INVOICE_DATE' => 'MM/DD/YYYY',
						'INVOICE_DUE_DATE' => 'MM/DD/YYYY',
						'INVOICE_AMOUNT' => '0.00',
						'DOCUMENT_TYPE' => 'string',
						'COLLECTION_ACCT_NO' => 'string',
						'MODEL' => 'string',
						'SERIAL_NUMBER' => 'string',
						'ENGINE_NUMBER' => 'string',
						'CS_NUMBER' => 'string',
						'YEAR_MODEL' => 'string',
						'BODY_COLOR' => 'string',
						'EXCEMPT' => '0.00',
						'ZERO_RATED' => '0.00',
						'DISCOUNT' => '0.00',
						'NET_AMOUNT' => '0.00',
						'PO_REF_NUMBER' => 'string',
						'WB_NUMBER' => 'string',
						'KEY_NUMBER' => 'string',
						'VAT' => '0.00',
						'ADDRESS1' => 'string',
						'ADDRESS2' => 'string',
						'ADDRESS3' => 'string',
						'LOT_NUMBER' => 'string',
						'WHT' => '0.00'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		$text = 'BuyerCode~~InvoiceNumber~~InvoiceDate~~InvoiceDueDate~~InvoiceAmount~~DocumentType~~CollectionAcctNo~~Model~~SerialNo~~EngineNo~~Con_Sticker~~YearModel~~Color~~Excempt~~Zero Rated~~Discount~~NetAmount~~PORefNo~~WBNo~~KeyNo~~VAT~~DeliveryAddr1~~DeliveryAddr2~~DeliveryAddr3~~LotNumber~~WHoldTax~~dtl_fld1~!';
		
		$rows = $this->security_bank_model->get_tagged();
		
		$cnt = 0;
		$amount = 0;
		
		foreach($rows as $row){
			
			$cnt++;
			$amount += $row->INVOICE_AMOUNT;
			
			$text .= '~'.$row->CUSTOMER_ID . '~~' . 
				 $row->INVOICE_NUMBER . '~~' . 
				 $row->INVOICE_DATE . '~~' . 
				 $row->INVOICE_DUE_DATE . '~~' . 
				 $row->INVOICE_AMOUNT . '~~' . 
				 $row->DOCUMENT_TYPE . '~~' . 
				 $row->COLLECTION_ACCT_NO . '~~' . 
				 $row->MODEL . '~~' . 
				 $row->SERIAL_NUMBER . '~~' . 
				 $row->ENGINE_NUMBER . '~~' . 
				 $row->CS_NUMBER . '~~' . 
				 $row->YEAR_MODEL . '~~' . 
				 $row->BODY_COLOR . '~~' . 
				 $row->EXCEMPT . '~~' . 
				 $row->ZERO_RATED . '~~' . 
				 $row->DISCOUNT . '~~' . 
				 $row->NET_AMOUNT . '~~' . 
				 $row->PO_REF_NO . '~~' . 
				 $row->WB_NUMBER . '~~' . 
				 $row->KEY_NUMBER . '~~' . 
				 $row->VAT . '~~' . 
				 $row->ADDRESS1 . '~~' . 
				 $row->ADDRESS2 . '~~' . 
				 $row->ADDRESS3 . '~~' . 
				 $row->LOT_NUMBER . '~~' . 
				 $row->WHT . '~~~!';
				 
			$line = array(
						$row->CUSTOMER_ID, 
						$row->INVOICE_NUMBER, 
						excel_date($row->INVOICE_DATE), 
						excel_date($row->INVOICE_DUE_DATE), 
						$row->INVOICE_AMOUNT, 
						$row->DOCUMENT_TYPE, 
						$row->COLLECTION_ACCT_NO, 
						$row->MODEL, 
						$row->SERIAL_NUMBER, 
						$row->ENGINE_NUMBER, 
						$row->CS_NUMBER, 
						$row->YEAR_MODEL, 
						$row->BODY_COLOR, 
						$row->EXCEMPT, 
						$row->ZERO_RATED, 
						$row->DISCOUNT, 
						$row->NET_AMOUNT, 
						$row->PO_REF_NO, 
						$row->WB_NUMBER, 
						$row->KEY_NUMBER, 
						$row->VAT, 
						$row->ADDRESS1, 
						$row->ADDRESS2, 
						$row->ADDRESS3, 
						$row->LOT_NUMBER, 
						$row->WHT
					);
			$writer->writeSheetRow('Sheet1', $line);
			$this->security_bank_model->insert_sent_tagged($row->CS_NUMBER);
		}

		if($cnt > 0){
			$filename = 'securitybank';
			
			//~ write excel file
			$writer->writeToFile($filename.'.xlsx');
			
			//~ write text file
			$myfile = fopen($filename.'.txt', 'w');
			fwrite($myfile, $text);
			fclose($myfile);
			
			$this->load->library('emailerphp');
			$mail = new EmailerPHP;
			
			
			$mail->addAddress('rhyme-javier@isuzuphil.com');
			$mail->addCC('eric-alcones@isuzuphil.com');
			$mail->addCC('nathalie-baladad@isuzuphil.com');
			$mail->addCC('zandra-dela-pena@isuzuphil.com');
			$mail->addBCC('christopher-desiderio@isuzuphil.com');
			
			$mail->Subject = 'Security Bank - Tagged Units for Uploading';
			$mail->AddAttachment($filename.'.xlsx');
			$mail->AddAttachment($filename.'.txt');
			$mail->Body = '<p>Hi Ms. Rhyme,</p>';
			$mail->Body .= '<p>Good Day!</p>';
			$mail->Body .= '<p>Please see attached newly tagged units for uploading.</p>';
			$mail->Body .= '<p>Batch Number : '.date('mdyHis').'</p>';
			$mail->Body .= '<p>Total Amount : '.$amount.'</p>';
			$mail->Body .= '<p>Total Lines : '.$cnt.'</p>';
			$mail->Body .= '<p>&nbsp;</p>';
			$mail->Body .= '<p>This is an automated message. Please do not respond to this e-mail.</p>';
		
			$mail->send();
			
			$mail = NULL;
			
			unlink($filename.'.xlsx');
			unlink($filename.'.txt');
		}
	}
}
