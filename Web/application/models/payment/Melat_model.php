<?php
class Melat_model extends CI_Model {
	var $namespace = 'http://interfaces.core.sw.bps.com/';
	var $terminalId="";
	var $userName="";
	var $userPassword="";

	public function __construct()
	{
		parent::__construct();
		$this->load->helper("nusoap/nusoap");
	}

	public function request_pay($total,$invoice_id)
	{	
		$total=(int)$total;
		$invoice_id=(int)$invoice_id;
		if(!$total || !$invoice_id)
		{
			return array("error"=>'خطای فاکتور و مبلغ');
		}

		$orderId=$invoice_id;		
		$amount=$total;
		$callBackUrl=get_pay_result_link("melat",$orderId);
		
		$parameters = array(
			'terminalId'     => $this->terminalId,
			'userName'       => $this->userName,
			'userPassword'   => $this->userPassword,
			'orderId'        => $orderId,
			'amount'         => $amount,
			'localDate'      => jdate('Ymd'),
			'localTime'      => jdate('His'),
			'additionalData' =>  '',
			'callBackUrl'    => $callBackUrl,
			'payerId'=>0
		);

		$client = new NuSOAP_Client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
		if ($err = $client->getError() || $client->fault)
		{
	    	$json = array();
			$json['error']= "متاسفانه سامانه بانک ملت در حال به روزرسانی است. ".$err;
			return $json;
		}

	
		$res=$client->call('bpPayRequest', $parameters, $this->namespace);
		if(!$res)
		{
			$json = array();
			$json['error']= "متاسفانه سامانه بانک ملت در حال به روزرسانی است. ";
			$json['error2']=$client->getError();
			return $json;
		}

		$res=explode(",",$res);
		if(sizeof($res)==1 || $res[0]!=0)
		{
			$json = array();
			$json['error']= "خطا شماره ".$res[0];
			return $json;
		}
		
		$json = array();
		$json['success']= 1;
		$json['RefId']=$res[1];

		$this->session->set_userdata(array("melat_refId"=>$res[1]));
	
		return $json;
	}

	public function verify_pay($invoice_id)
	{
		$RefId=$this->input->post("RefId");
		$ResCode=$this->input->post("ResCode");
		$SaleOrderId=$this->input->post("SaleOrderId");
		$orderId=$SaleOrderId;
		$SaleReferenceId=$this->input->post("SaleReferenceId");

		$saved_refId=$this->session->userdata("melat_refId");
		$this->session->unset_userdata("melat_refId");
		if( !$saved_refId || ($saved_refId !== $RefId) )
			return array('error'=>'خطای شناسه');
		
		$parameters = array(
			'terminalId'     => $this->terminalId,
			'userName'       => $this->userName,
			'userPassword'   => $this->userPassword,
			'orderId'        => $SaleOrderId,
			'saleOrderId'    => $SaleOrderId,
			'saleReferenceId'=> $SaleReferenceId
		);
		
		//if(0)
		{
		if($ResCode != 0)
			return array('error'=>"خطای پرداخت ".$ResCode);
		
		$client = new NuSOAP_Client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
		if ($err = $client->getError() || $client->fault)
			return array('error'=>"خطای اتصال ".$err." ".$client->fault);

		$res=$client->call('bpVerifyRequest', $parameters, $this->namespace);
		if($res!=0 && $res!=43)
			return array('error'=>"خطای بررسی صحت ".$res);
			
		$res=$client->call('bpSettleRequest', $parameters, $this->namespace);
		if($res!=0 && $res!=45)
			return  array('error'=>"خطای واریز ".$res);
		}		

		return array(
			'success'=>'پرداخت بانک ملت با موفقیت انجام شد. کدرهگیری: '.$SaleReferenceId
			,'refId'=>$SaleReferenceId
			,'bank'=>'melat'
		);
	}
}