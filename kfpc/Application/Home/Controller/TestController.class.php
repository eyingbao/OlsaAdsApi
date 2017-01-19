<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
	
	
	
	
	public function index(){
		
		cookie('abc','abc',3600 * 24 * 30 * 6);
	}
   
	public function pdf(){
		require_once  THINK_PATH.'Library/Vendor/fpdf/fpdf.php';
		$pdf= new \FPDF();
		$pdf->AddPage();  
		$fp = file_get_contents("http://test.qdetong.com/system/month/index.html"); 
 
		$strContent = fread($fp, strlen($fp));  
		fclose($fp);  
		$pdf->WriteHTML($strContent);  
		$pdf->Output("sample.pdf");  
		echo "PDF file is generated successfully!";  
	}
  
}