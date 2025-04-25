<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Utiid;
use Carbon\Carbon;
use App\Model\Report;
use App\Model\Aepsreport;
use App\Model\Aepsfundrequest;
use App\Model\Upifundrequest;
use App\Model\Microatmreport;
use App\Model\Upireport;
use App\User;
use App\Model\Provider;
use App\Model\Mahaagent;
use App\Model\Api;

class CommonController extends Controller
{
     protected $xettleupi;
     public function __construct()
    {
        $this->xettleupi = Api::where('code', 'xettleupi')->first();
        $this->xettlepayout = Api::where('code', 'xettlepayout')->first();
    }
    
    public function fetchData(Request $request, $type, $id=0, $returntype="all")
	{
		$request['return'] = 'all';
		$request['returntype'] = $returntype;
		$parentData = \Myhelper::getParents(\Auth::id());
		switch ($type) {
			case 'permissions':
				$request['table']= '\App\Model\Permission';
				$request['searchdata'] = ['name', 'slug'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
			break;

			case 'roles':
				$request['table']= '\App\Model\Role';
				$request['searchdata'] = ['name', 'slug'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
			break;

			case 'whitelable':
			case 'md':
			case 'distributor':
			case 'retailer':
			case 'apiuser':
			case 'reseller':
			case 'other':
			case 'tr' :
			case 'kycpending':
			case 'kycsubmitted':
			case 'kycrejected':
			case 'employee':
				$request['table']= '\App\User';
				$request['searchdata'] = ['id','name', 'mobile','email'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if (\Myhelper::hasRole(['retailer', 'apiuser'])){
					$request['parentData'] = [\Auth::id()];
				}elseif(\Myhelper::hasRole(['admin','employee'])){
					$request['parentData'] = 'all';
				}else{
					$request['parentData'] = $parentData;
				}
				$request['whereIn'] = 'parent_id';
			break;

			case 'fundrequest':
				$request['table']= '\App\Model\Fundreport';
				$request['searchdata'] = ['amount','ref_no', 'remark','paymode', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;
				
			case 'vanaccountlist':
				$request['table']= '\App\Model\Vanaccount';
				$request['searchdata'] = ['vanAccount','user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
				$request['whereIn'] = 'user_id';
				break;
			case 'resourceconnectaccount':
				$request['table']= '\App\Model\P2p';
				$request['searchdata'] = ['midname','midid','bank','account','ifsc','accountholder','upiid','username','password','user_id',];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if (\Myhelper::hasRole(['retailer', 'apiuser'])){
					$request['parentData'] = [\Auth::id()];
				}elseif(\Myhelper::hasRole(['admin','employee'])){
					$request['parentData'] = 'all';
				}else{
					$request['parentData'] = $parentData;
				}
				$request['whereIn'] = 'user_id';
				break;	
			case 'idfcaccountstatement':
				$request['table']= '\App\Model\Idfcreport';
				$request['searchdata'] = ['txnid', 'user_id', 'credited_by', 'id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
			    if($id == 0){
					$request['parentData'] = [\Auth::id()];
				}else{
					if(in_array($id, $parentData)){
					    //dd("343434");
						$request['parentData'] = [$id];
					}else{
					    //dd("ABC");
						$request['parentData'] = [$id];
					}
				}
				$request['whereIn'] = 'user_id';
				
				break;	
			case 'idfcupistatement':
				$request['table']= '\App\Model\Idfcreport';
				$request['searchdata'] = ['aadhar', 'mobile','refno', 'txnid', 'payid', 'mytxnid', 'terminalid','id','aepstype','authcode'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;	
			
			case 'fundrequestview':
			case 'fundrequestviewall':
				$request['table']= '\App\Model\Fundreport';
				$request['searchdata'] = ['amount','ref_no', 'remark','paymode', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'credited_by';
				break;

			case 'fundstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['amount','number', 'mobile','credit_by', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;
			
			case 'aepsfundrequest':
				$request['table']= '\App\Model\Aepsfundrequest';
				$request['searchdata'] = ['amount','type', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;
			
			case 'upifundrequest':
				$request['table']= '\App\Model\Upifundrequest';
				$request['searchdata'] = ['amount','type', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;

			case 'aepsfundrequestview':
			case 'aepsfundrequestviewall':
			case 'aepspayoutrequestview':
			    $request['table']= '\App\Model\Aepsfundrequest';
				$request['searchdata'] = ['amount','type', 'user_id', 'id','payoutid', 'account'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				if(\Myhelper::hasNotRole(['admin'])){
					$request['parentData'] = \Myhelper::getParents(\Auth::id());
				}else{
					$request['parentData'] = 'all';
				}
				$request['whereIn'] = 'user_id';
				break;
			
			case 'upifundrequestview':
			case 'upifundrequestviewall':
			case 'upipayoutrequestview':
			    $request['table']= '\App\Model\Upifundrequest';
				$request['searchdata'] = ['amount','type', 'user_id', 'id','payoutid', 'account'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				if(\Myhelper::hasNotRole(['admin'])){
					$request['parentData'] = \Myhelper::getParents(\Auth::id());
				}else{
					$request['parentData'] = 'all';
				}
				$request['whereIn'] = 'user_id';
				break;
			
			case 'setupbank':
				$request['table']= '\App\Model\Fundbank';
				$request['searchdata'] = ['name','account', 'ifsc','branch'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;
			
			case 'setupapi':
				$request['table']= '\App\Model\Api';
				$request['searchdata'] = ['name','account', 'ifsc','branch'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
				$request['whereIn'] = 'user_id';
				break;
				
			case 'setupoperator':
				$request['table']= '\App\Model\Provider';
				$request['searchdata'] = ['name','recharge1', 'recharge2','type'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
				$request['whereIn'] = 'user_id';
				break;
			
			case 'setupcomplaintsub':
				$request['table']= '\App\Model\Complaintsubject';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
				$request['whereIn'] = 'user_id';
				break;

			case 'resourcescheme':
				$request['table']= '\App\Model\Scheme';
				$request['searchdata'] = ['name', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;
			
			case 'resourceupihandle':
				$request['table']= '\App\Model\Upiid';
				$request['searchdata'] = ['vpa', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;

			case 'resourcepackage':
				$request['table']= '\App\Model\Package';
				$request['searchdata'] = ['name', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;

			case 'resourcecompany':
				$request['table']= '\App\Model\Company';
				$request['searchdata'] = ['companyname'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
				$request['whereIn'] = 'user_id';
				break;

			case 'setuplinks':
				$request['table']= '\App\Model\Link';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = 'all';
				$request['whereIn'] = 'user_id';
				break;
			
			case 'accountstatement':
			case 'commissionstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['txnid', 'user_id', 'credited_by', 'id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
			    if($id == 0){
					$request['parentData'] = [\Auth::id()];
				}else{
					if(in_array($id, $parentData)){
					    //dd("343434");
						$request['parentData'] = [$id];
					}else{
					    //dd("ABC");
						$request['parentData'] = [$id];
					}
				}
				$request['whereIn'] = 'user_id';
				
				break;
			
			case 'upiaccountstatement':
				$request['table']= '\App\Model\Upireport';
				$request['searchdata'] = ['txnid', 'user_id', 'credited_by', 'id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
			    if($id == 0){
					$request['parentData'] = [\Auth::id()];
				}else{
					if(in_array($id, $parentData)){
					    //dd("343434");
						$request['parentData'] = [$id];
					}else{
					    //dd("ABC");
						$request['parentData'] = [$id];
					}
				}
				$request['whereIn'] = 'user_id';
				
				break;

			case 'awalletstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['mobile','aadhar', 'txnid', 'refno', 'payid', 'amount','mytxnid','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if($id == 0){
					$request['parentData'] = [\Auth::id()];
				}else{
					if(in_array($id, $parentData)){
						$request['parentData'] = [$id];
					}else{
						$request['parentData'] = [\Auth::id()];
					}
				}
				$request['whereIn'] = 'user_id';
				break;
			
			case 'utiidstatement':
				$request['table']= '\App\Model\Utiid';
				$request['searchdata'] = ['name','vleid', 'user_id', 'location', 'contact_person', 'pincode', 'email', 'id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
				
			case 'upiidstatement':
				$request['table']= '\App\Model\Upiid';
				$request['searchdata'] = ['panNo','mobile', 'user_id', 'vpa1', 'vpa2','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
				
			case 'contactstatement':
				$request['table']= '\App\Model\Contact';
				$request['searchdata'] = ['accountNumber','mobile', 'firstName'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'portaluti':
				$request['table']= '\App\Model\Utiid';
				$request['searchdata'] = ['name','vleid', 'user_id', 'location', 'contact_person', 'pincode', 'email','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					$request['parentData'] = [\Auth::id()];
					$request['whereIn'] = 'sender_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
			
			case 'utipancardstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['number', 'id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'rechargestatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['number', 'txnid', 'payid', 'remark', 'description', 'refno', 'id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'billpaystatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['number', 'txnid', 'payid', 'remark', 'description', 'refno','option1', 'option2', 'mobile','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'moneystatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['name', 'mobile', 'number', 'option1', 'option2', 'option3', 'option4', 'refno', 'payid', 'amount','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
			
			case 'aepsstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['aadhar', 'mobile', 'txnid', 'payid','refno', 'mytxnid', 'terminalid','id','aepstype'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
				
			case 'upistatement':
			case 'dispute':
				$request['table']= '\App\Model\Upireport';
				$request['searchdata'] = ['aadhar', 'mobile','refno', 'txnid', 'payid', 'mytxnid','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable','reseller'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
				
			case 'pendingupistatement':
				$request['table']= '\App\Model\Upirorder';
				$request['searchdata'] = ['aadhar', 'mobile','refno', 'txnid', 'payid', 'mytxnid', 'terminalid','id','aepstype','authcode'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
				
				
				
				case 'payoutstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['aadhar', 'mobile','refno', 'txnid', 'payid', 'mytxnid','apitxnid', 'terminalid','id','aepstype','authcode'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;
				
			case 'upioldstatement':
				$request['table']= '\App\Model\Aepsreport';
				$request['searchdata'] = ['aadhar', 'mobile','refno', 'txnid', 'payid', 'mytxnid', 'terminalid','id','aepstype'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;	
				
			case 'upicollectstatement':
				$request['table']= '\App\Model\Report';
				$request['searchdata'] = ['aadhar', 'mobile', 'txnid', 'payid', 'mytxnid', 'terminalid','id','aepstype'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;	

			case 'complaints':
				$request['table']= '\App\Model\Complaint';
				$request['searchdata'] = ['type', 'solution', 'description', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'apitoken':
				$request['table']= '\App\Model\Apitoken';
				$request['searchdata'] = ['ip'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if (\Myhelper::hasRole('admin')){
					$request['parentData'] = 'all';
				}else{
					$request['parentData'] = [\Auth::id()];
				}
				$request['whereIn'] = 'user_id';
				break;

			case 'aepsagentstatement':
				$request['table']= '\App\Model\Mahaagent';
				$request['searchdata'] = ['bc_f_name','bc_m_name', 'bc_id', 'phone1', 'phone2', 'emailid','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = [$id];
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'nsdlstatement':
				$request['table']= '\App\Model\Nsdlpan';
				$request['searchdata'] = ['lastname'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'matmfundrequest':
				$request['table']= '\App\Model\Microatmfundrequest';
				$request['searchdata'] = ['amount','type', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				$request['parentData'] = [\Auth::id()];
				$request['whereIn'] = 'user_id';
				break;

			case 'matmfundrequestview':
			case 'matmfundrequestviewall':
				$request['table']= '\App\Model\Microatmfundrequest';
				$request['searchdata'] = ['amount','type', 'user_id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if(\Myhelper::hasNotRole(['admin'])){
					$request['parentData'] = [\Auth::id()];
				}else{
					$request['parentData'] = 'all';
				}
				$request['whereIn'] = 'user_id';
				break;

			case 'matmstatement':
				$request['table']= '\App\Model\Microatmreport';
				$request['searchdata'] = ['aadhar', 'mobile', 'txnid', 'payid', 'mytxnid', 'terminalid','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				if ($id == 0 || $returntype == "all") {
					if($id == 0){
						if (\Myhelper::hasRole(['retailer', 'apiuser'])){
							$request['parentData'] = [\Auth::id()];
						}elseif(\Myhelper::hasRole(['md', 'distributor','whitelable'])){
							$request['parentData'] = $parentData;
						}else{
							$request['parentData'] = 'all';
						}
					}else{
						if(in_array($id, $parentData)){
							$request['parentData'] = \Myhelper::getParents($id);
						}else{
							$request['parentData'] = [\Auth::id()];
						}
					}
					$request['whereIn'] = 'user_id';
				}else{
					$request['parentData'] = [$id];
					$request['whereIn'] = 'id';
					$request['return'] = 'single';
				}
				break;

			case 'matmwalletstatement':
				$request['table']= '\App\Model\Microatmreport';
				$request['searchdata'] = ['mobile','aadhar', 'txnid', 'refno', 'payid', 'amount','mytxnid','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','desc'];
				if($id == 0){
					$request['parentData'] = [\Auth::id()];
				}else{
					if(in_array($id, $parentData)){
						$request['parentData'] = [$id];
					}else{
						$request['parentData'] = [\Auth::id()];
					}
				}
				$request['whereIn'] = 'user_id';
				break;
				
			case 'securedata':
				$request['table']= '\App\Model\Securedata';
				$request['searchdata'] = ['user_id','id'];
				$request['select'] = 'all';
				$request['order'] = ['id','DESC'];
				if (\Myhelper::hasRole('admin')){
					$request['parentData'] = 'all';
				}else{
					$request['parentData'] = [\Auth::id()];
				}
				$request['whereIn'] = 'user_id';
				break;	

			default:
				# code...
				break;
        }
        
		$request['where']=0;
		$request['type']= $type;
        
		try {
			$totalData = $this->getData($request, 'count');
		} catch (\Exception $e) {
			$totalData = 0;
		}

		if ((isset($request->searchtext) && !empty($request->searchtext)) ||
           	(isset($request->todate) && !empty($request->todate))       ||
           	(isset($request->product) && !empty($request->product))       ||
           	(isset($request->status) && $request->status != '')		  ||
           	(isset($request->agent) && !empty($request->agent))
         ){
	        $request['where'] = 1;
	    }

		try {
			$totalFiltered = $this->getData($request, 'count');
		} catch (\Exception $e) {
			$totalFiltered = 0;
		}
		//return $data = $this->getData($request, 'data');
		try {
			$data = $this->getData($request, 'data');
		} catch (\Exception $e) {
			$data = [];
		}
		
		//dd($data);
		if ($request->return == "all" || $returntype =="all") {
			$json_data = array(
				"draw"            => intval( $request['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			echo json_encode($json_data);
		}else{
			return response()->json($data);
		}
	}

	public function getData($request, $returntype)
	{ 
		$table = $request->table;
		$data = $table::query();
		$data->orderBy($request->order[0], $request->order[1]);

		if($request->parentData != 'all'){
			if(!is_array($request->whereIn)){
				$data->whereIn($request->whereIn, $request->parentData);
			}else{
				$data->where(function ($query) use($request){
					$query->where($request->whereIn[0] , $request->parentData)
					->orWhere($request->whereIn[1] , $request->parentData);
				});
			}
		}

		if( $request->type != "roles" &&
			$request->type != "permissions" &&
			$request->type != "fundrequestview" &&
			$request->type != "moneystatement" &&
			$request->type != "fundrequest" &&
			$request->type != "setupbank" &&
			$request->type != "dispute" &&
			$request->type != "setupapi" &&
			$request->type != "setuplinks" &&
			$request->type != "setupoperator" &&
			$request->type != "resourcescheme" &&
			$request->type != "resourceupihandle" &&
			$request->type != "resourcecompany" &&
			$request->type != "resourcepackage" &&
			$request->type != "resourceconnectaccount" &&
			$request->type != "aepsfundrequestview" &&
			$request->type != "fundrequestview" &&
			!in_array($request->type , ['whitelable', 'md', 'distributor', 'retailer', 'apiuser', 'other', 'tr','reseller'])&&
			$request->where != 1
        ){
            if(!empty($request->fromdate)){
                $data->whereDate('created_at', Carbon::parse($request->fromdate));
            }
	    }

        switch ($request->type) {
			case 'whitelable':
			case 'md':
			case 'distributor':
			case 'retailer':
			case 'apiuser':
			case 'reseller':
			case 'employee':
				$data->whereHas('role', function ($q) use($request){
					$q->where('slug', $request->type);
				})->where('kyc', 'verified');
			break;

			case 'other':
				$data->whereHas('role', function ($q) use($request){
					$q->whereNotIn('slug', ['whitelable', 'md', 'distributor', 'retailer', 'apiuser', 'admin']);
				});
			break;

			case 'tr':
				$data->whereHas('role', function ($q) use($request){
					$q->whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer', 'apiuser']);
				})->where('kyc', 'verified');
			break;

			case 'kycpending':
				$data->whereHas('role', function ($q) use($request){
					$q->whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer', 'apiuser']);
				})->whereIn('kyc', ['pending']);
			break;

			case 'kycsubmitted':
				$data->whereHas('role', function ($q) use($request){
					$q->whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer', 'apiuser']);
				})->whereIn('kyc', ['submitted']);
			break;
				
			case 'kycrejected':
				$data->whereHas('role', function ($q) use($request){
					$q->whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer', 'apiuser']);
				})->whereIn('kyc', ['rejected']);
			break;

			case 'fundrequest':
				$data->where('type', 'request');
				break;
				
			case 'matmstatement':
				$data->where('rtype', 'main')->where('aepstype', 'MATM');
				break;	

			case 'fundrequestview':
				$data->where('status', 'pending')->where('type', 'request');
				break;
				
			case 'awalletstatement':
				$data->where('status', '!=' ,'failed')->where('aepstype', '!=','BE');
				break;	
			
			case 'fundrequestviewall':
				$data->where('type', 'request');
				break;

			case 'aepsfundrequestview':
			    if(\Myhelper::hasRole('admin')){
			    $data->whereHas('user', function ($query){
                    $query->where('role_id', 8);
			    })->where('status', 'pending')->where('mode', 'NEFT');
                }else{
                    $data->where('status', 'pending')->where('mode', 'NEFT');  
                }
				break;
			
			case 'upifundrequestview':
			    if(\Myhelper::hasRole('admin')){
			    $data->whereHas('user', function ($query){
                    $query->where('role_id', 5);
			    })->where('mode', 'NEFT');
                }else{
                    $data->where('mode', 'NEFT');  
                }
				break;

			case 'aepspayoutrequestview':
				$data->where('status', 'pending')->where('payouttype', 'payout');
				break;
				
			case 'aepsfundrequestviewall':
				$data->where('aepstype', 'payout');
				break;	

			case 'rechargestatement':
				$data->where('product', 'recharge')->where('rtype', 'main');
				break;
			
			case 'billpaystatement':
				$data->where('product', 'billpay')->where('rtype', 'main');
				break;

			case 'aepsstatement':
				$data->where('rtype', 'main')->whereIn('aepstype', ['CW','AP']);
				break;
				
			case 'upistatement':
				$data->where('aepstype', 'UPI');
				//dd($t->toSql());
				//dd($data->where('aepstype', 'UPI')->toSql());
				break;
				
			case 'dispute':
				$data->where('status', 'dispute');
				//dd($t->toSql());
				//dd($data->where('aepstype', 'UPI')->toSql());
				break;
			case 'pendingupistatement':
			    //dd($data->toSql());
				$data->where('status', 'initiated');
				break;
			case 'payoutstatement':
				$data->where('product', 'payout');
				break;
				
			case 'upioldstatement':
				$data->where('aepstype', 'UPI');
				break;	
				
			case 'upicollectstatement':
				$data->where('aepstype', 'upicollect');
				break;		
			
			case 'utipancardstatement':
				$data->where('product', 'utipancard')->where('rtype', 'main');
				break;
			
			case 'fundstatement':
				$data->whereHas('provider', function ($q){
					$q->where('recharge1', 'fund');
				});
				break;

			case 'moneystatement':
				$data->where('product', 'dmt')->where('rtype', 'main');
				break;
		

			case 'commissionstatement':
				$data->where('rtype', 'commission');
				break;
        }
		if ($request->where) {
	        if((isset($request->fromdate) && !empty($request->fromdate)) 
	        	&& (isset($request->todate) && !empty($request->todate))){
	            if($request->fromdate == $request->todate){
	                $data->whereDate('created_at','=', Carbon::createFromFormat('Y-m-d', $request->fromdate)->format('Y-m-d'));
	            }else{
	                $data->whereBetween('created_at', [Carbon::createFromFormat('Y-m-d', $request->fromdate)->format('Y-m-d'), Carbon::createFromFormat('Y-m-d', $request->todate)->addDay(1)->format('Y-m-d')]);
	            }
	        }

	        if(isset($request->product) && !empty($request->product)){
	            switch ($request->type) {
					case 'billpaystatement':
					case 'rechargestatement':
	            		$data->where('provider_id', $request->product);
					break;

					case 'setupoperator':
	            		$data->where('type', $request->product);
					break;

					case 'complaints':
	            		$data->where('product', $request->product);
					break;

					case 'fundstatement':
					case 'aepsfundrequestview':
					case 'aepsfundrequestviewall':
	            		$data->where('aepstype', 'payout');
					break;
			        
			        case 'fundstatement':
					case 'upifundrequestview':
					case 'upifundrequestviewall':
	            		$data->where('aepstype', 'payout');
					break;
					
				}
			}
			
	        if(isset($request->status) && $request->status != '' && $request->status != null){
	        	switch ($request->type) {	
					case 'kycpending':
					case 'kycsubmitted':
					case 'kycrejected':
						$data->where('kyc', $request->status);
					break;

					default:
	            		$data->where('status', $request->status);
					break;
				}
			}
			
				if(isset($request->agent) && !empty($request->agent)){
	        	switch ($request->type) {					
					case 'whitelable':
					case 'md':
					case 'distributor':
					case 'retailer':
					case 'apiuser':
					case 'reseller':
					case 'other':
					case 'tr' :
					case 'kycpending':
					case 'kycsubmitted':
					case 'kycrejected':
					case 'employee':
						$data->whereIn('id', $this->agentFilter($request));
					break;

					default:
						$data->whereIn('user_id', $this->agentFilter($request));
					break;
				}
	        }

	        if(!empty($request->searchtext)){
	            $data->where( function($q) use($request){
	            	foreach ($request->searchdata as $value) {
	            		$q->orWhere($value, 'like',$request->searchtext.'%');
                  		$q->orWhere($value,'like','%'.$request->searchtext.'%');
                  		$q->orWhere($value, 'like','%'.$request->searchtext);
	            	}
				});
	        } 
      	}
		
		if ($request->return == "all" || $request->returntype == "all") {
			if($returntype == "count"){
				return $data->count();
			}else{
				if($request['length'] != -1){
					$data->skip($request['start'])->take($request['length']);
				}

				if($request->select == "all"){
					return $data->get();
				}else{
					return $data->select($request->select)->get();
				}
			}
		}else{
			if($request->select == "all"){
				return $data->first();
			}else{
				return $data->select($request->select)->first();
			}
		}
	}

	public function agentFilter($post)
	{
		if (\Myhelper::hasRole('admin') || in_array($post->agent, session('parentData'))) {
			return \Myhelper::getParents($post->agent);
		}else{
			return [];
		}
	}

	public function update(Request $post)
    {
        switch ($post->actiontype) {
            case 'utiid':
                $permission = "Utiid_statement_edit";
				break;

			case 'aepsid':
                $permission = "aepsid_statement_edit";
				break;
				
			case 'utipancard':
                $permission = "utipancard_statement_edit";
				break;
				
			case 'recharge':
                $permission = "recharge_statement_edit";
				break;
				
			case 'billpay':
                $permission = "billpay_statement_edit";
				break;
			
			case 'money':
                $permission = "money_statement_edit";
                break;

			case 'aeps':
                $permission = "aeps_statement_edit";
				break;
			
			case 'payout':
                $permission = "payout_statement_edit";
                break;
            
            case 'upiid':
                $permission = "upiid_statement_edit";
				break;
        }

        if (isset($permission) && !\Myhelper::can($permission)) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        switch ($post->actiontype) {
            case 'utiid':
                $rules = array(
					'id'    => 'required',
                    'status'    => 'required',
                    'vleid'    => 'required|unique:utiids,vleid'.($post->id != "new" ? ",".$post->id : ''),
                    'vlepassword'    => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = Utiid::where('id', $post->id)->update($post->except(['id', '_token', 'actiontype', 'actiontype']));
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
				
			case 'upiid':
                $rules = array(
					'id'    => 'required',
                    'requestUrl'    => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = \App\Model\Upiid::where('id', $post->id)->update(['requestUrl' => $post->requestUrl]);
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;

			case 'aepsid':
                $rules = array(
					'id'    => 'required',
                    'bbps_agent_id' => 'required',
                    'bbps_id'   => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = Mahaagent::where('id', $post->id)->update($post->except(['id', '_token', 'actiontype']));
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
				
			case 'utipancard':
                $rules = array(
					'id'    => 'required',
                    'status'    => 'required',
                    'number'    => 'required',
                    'remark'    => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
				}
				
				$report = Report::where('id', $post->id)->first();
				if(!$report || !in_array($report->status , ['pending', 'success'])){
					return response()->json(['status' => "Utipancard Editing Not Allowed"], 400);
				}

                $action = Report::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
					if($post->status == "reversed"){
						\Myhelper::transactionRefund($post->id);
					}

					if($report->user->role->slug == "apiuser" && $report->status == "pending" && $post->status != "pending"){
						\Myhelper::callback($report, 'utipancard');
					}
					
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
				
			case 'recharge':
                $rules = array(
					'id'    => 'required',
                    'status'    => 'required',
                    'txnid'    => 'required',
					'refno'    => 'required',
                    'payid'    => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
				}

				$report = Report::where('id', $post->id)->first();
				if(!$report || !in_array($report->status , ['pending', 'success'])){
					return response()->json(['status' => "Recharge Editing Not Allowed"], 400);
				}

                $action = Report::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
					if($post->status == "reversed"){
						\Myhelper::transactionRefund($post->id);
					}

					if($report->user->role->slug == "apiuser" && $report->status != "reversed" && $post->status != "pending"){
						\Myhelper::callback($report, 'recharge');
					}

                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
				
			case 'billpay':
                $rules = array(
					'id'    => 'required',
                    'status'    => 'required',
                    'txnid'    => 'required',
					'refno'    => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
				}

				$report = Report::where('id', $post->id)->first();
				if(!$report || !in_array($report->status , ['pending', 'success'])){
					return response()->json(['status' => "Recharge Editing Not Allowed"], 400);
				}

                $action = Report::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
					if($post->status == "reversed"){
						\Myhelper::transactionRefund($post->id);
					}
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
				
			case 'money':
                $rules = array(
					'id'    => 'required',
                    'status'=> 'required',
                    'txnid' => 'required',
					'refno' => 'required',
                    'payid' => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
				}

				$report = Report::where('id', $post->id)->first();
				if(!$report || !in_array($report->status , ['pending', 'success'])){
					return response()->json(['status' => "Money Transfer Editing Not Allowed"], 400);
				}

                $action = Report::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
					if($post->status == "reversed"){
						\Myhelper::transactionRefund($post->id);
					}
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;

			case 'aeps':
                $rules = array(
					'id'    => 'required',
                    'status'=> 'required',
                    'txnid' => 'required',
					'refno' => 'required',
                    'payid' => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
				}

				$report = Report::where('id', $post->id)->first();
				if(!$report || !in_array($report->status , ['pending'])){
					return response()->json(['status' => "Money Transfer Editing Not Allowed"], 400);
				}
				if($post->status == "success"){
					$post['status'] = "complete";
				}
                $action = Report::updateOrCreate(['id'=> $post->id], $update);
                if ($action) {
					if($report->status == "pending" && $post->status == "complete"){
					    $user = User::where('id', $report->user_id)->first();
					    $insert = [
                            "mobile" => $report->mobile,
                            "aadhar" => $report->aadhar,
                            "api_id" => $report->api_id,
                            "txnid"  => $report->txnid,
                            "refno"  => "Txnid - ".$report->id. " Cleared",
                            "amount" => $report->amount,
                            "bank"   => $report->bank,
                            "user_id"=> $report->user_id,
                            "balance" => $user->mainwallet,
                            'aepstype'=> $report->aepstype,
                            'status'  => 'success',
                            'authcode'=> $report->authcode,
                            'payid'=> $report->payid,
                            'mytxnid'=> $report->mytxnid,
                            'terminalid'=> $report->terminalid,
                            'TxnMedium'=> $report->TxnMedium,
                            'credited_by' => $report->credited_by,
                            'type' => 'credit'
                        ];
                        if($report->amount >= 100 && $report->amount <= 3000){
                            $provider = Provider::where('recharge1', 'aeps1')->first();
                        }elseif($report->amount>3000 && $report->amount<=10000){
                            $provider = Provider::where('recharge1', 'aeps2')->first();
                        }
                        $post['provider_id'] = $provider->id;
                        $post['service'] = $provider->type;
            
                        if($report->aepstype == "CW"){
                            if($report->amount >= 100){
                                $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id,$user->role->slug);
                            }else{
                                $usercommission = 0;
                            }
                        }else{
                            $usercommission = 0;
                        }
                        
                        $insert['charge'] = $usercommission;
                        $action = User::where('id', $report->user_id)->increment('mainwallet', $report->amount+$usercommission);
                        if($action){
                            $aeps = Report::create($insert);
                            $post['reportid'] = $aeps->id;
                            $post['precommission'] = $usercommission;
                            if($report->amount > 500){
                                \Myhelper::commission($aeps);
                            }
                        }
					}
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
				
			case 'payout':
                $rules = array(
					'id'    => 'required',
                    'status'=> 'required',
					//'payoutref' => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
				}

				$fundreport = Report::where('id', $post->id)->first();
			/*	if(!$fundreport || !in_array($fundreport->status , ['pending', 'approved'])){
					return response()->json(['status' => "Transaction Editing Not Allowed"], 400);
				}*/

                $action = Report::where('id', $post->id)->update($post->except(['id', '_token', 'actiontype']));
                if ($action) {
					if($post->status == "reversed"){
						//$report = Report::where('txnid', $fundreport->payoutid)->update(['status' => "reversed"]);
					   \Myhelper::transactionRefund($post->id);
                    //	User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
					}
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
				break;
        }
	}
	
	public function status(Request $post)
    {
		if (!\Myhelper::can($post->type."_status")) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
		}
		
		switch ($post->type) {
			case 'recharge':
			case 'billpayment':
			case 'utipancard':
			case 'money':
				$report = Report::where('id', $post->id)->first();
				break;

			case 'utiid':
				$report = Utiid::where('id', $post->id)->first();
				break;
				
			case 'aeps':
				$report = Report::where('id', $post->id)->first();
				break;
				
			case 'payout':
				$report = Aepsfundrequest::where('id', $post->id)->first();
				break;	
				
			case 'upi':
				$report = Report::where('id', $post->id)->first();
				break;	

			case 'matm':
				$report = Microatmreport::where('id', $post->id)->first();
				break;
				
			case 'bcstatus':
				$report = Mahaagent::where('id', $post->id)->first();
				break;

			default:
				return response()->json(['status' => "Status Not Allowed"], 400);
				break;
		}

		if(!$report || !in_array($report->status , ['pending', 'success', 'approved'])){
			return response()->json(['status' => "Recharge Status Not Allowed"], 400);
		}

		if($post->type == "aeps" && (!$report || !in_array($report->status , ['pending']))){
			return response()->json(['status' => "Aeps Status Not Allowed"], 400);
		}

		switch ($post->type) {
			case 'recharge':
				switch ($report->api->code) {
					case 'recharge1':
						$url = $report->api->url.'/status?token='.$report->api->username.'&apitxnid='.$report->txnid;
						break;

					case 'recharge2':
						$url = $report->api->url.'rechargestatus.aspx?memberid='.$report->api->username."&pin=".$report->api->password.'&transid='.$report->txnid.'&format=json';
						break;
					
					default:
						return response()->json(['status' => "Recharge Status Not Allowed"], 400);
						break;
				}
				
				$method = "GET";
				$parameter = "";
				$header = [];
				break;

			case 'billpayment':
				$url = $report->api->url.'/status?token='.$report->api->username.'&apitxnid='.$report->txnid;
				$method = "GET";
				$parameter = "";
				$header = [];
				break;

				case 'utipancard':
				$url = $report->api->url.'/UATUTICouponRequestStatus';
				$method = "POST";
				$parameter['securityKey'] = $report->api->password;
                $parameter['createdby']   = $report->api->username;
                $parameter['requestid']   = $report->payid;
					$header = array(
							"Accept: application/json",
							"Cache-Control: no-cache",
							"Content-Type: application/json"
						);
				break;
			
			case 'utiid':
				$url = $report->api->url.'UATUTIAgentRequestStatus';
				$method = "POST";
				$parameter['securityKey'] = $report->api->password;
                $parameter['createdby']   = $report->api->username;
                $parameter['requestid']   = $report->payid;
				$header = array(
							"Accept: application/json",
							"Cache-Control: no-cache",
							"Content-Type: application/json"
						);
				break;

			case 'money':
				$url = $report->api->url."Common/CheckAndUpdateStatus";
				$method = "POST";
				$parameter = json_encode(array(
					'Secretkey' => $report->api->password,
					'Saltkey' => $report->api->username,
					'Mhid' => $report->payid,
					'FsessionId' => $report->remark,
				));

				$header = array(
					"Accept: application/json",
					"Cache-Control: no-cache",
					"Content-Type: application/json"
				);
				break;
			
			case 'aeps':
				$url = $report->api->url."Common/CheckAePSTxnStatus";
				$method = "POST";
				$txnid = explode("|", $report->txnid);
				$parameter = json_encode(array(
					'Secretkey' => $report->api->password,
					'Saltkey' => $report->api->username,
					'stanno' => $txnid[0]
				));

				$header = array(
					"Accept: application/json",
					"Cache-Control: no-cache",
					"Content-Type: application/json"
				);
				break;
				
			case 'payout':
				$url = "https://api.xettle.io/v1/service/payout/orders/".$report->apitxnid;
				$method = "GET";
				$string=("/v1/service/payout/orders/".$report->apitxnid.$this->xettlepayout->username."####".$this->xettlepayout->optional1);
                $signature=  hash("sha256",$string);
                $parameter="";
                //dd($string,$signature);
                $header = array(
                    "authorization: Basic ".base64_encode($this->xettlepayout->username.":".$this->xettlepayout->password),
                    "cache-control: no-cache",
                    "content-type: application/json",
                    "Signature: ".$signature
                );
				break;	
				
			case 'upi':
				$url = $report->api->url."upi/status/".$report->mytxnid;
				$method = "GET";
				

				$header = array(
                    "authorization: Basic ".base64_encode($this->xettleupi->username.":".$this->xettleupi->password),
                    "cache-control: no-cache",
                    "content-type: application/json"
                );
				break;	

			case 'matm':
				$url = "http://uat.dhansewa.com/MICROATM/GetMATMtxnStatus";
				$method = "POST";
				$parameter = json_encode(array(
					'secretekey' => $report->api->password,
					'saltkey' => $report->api->username,
					'referenceid' => $report->txnid
				));

				$header = array(
					"Accept: application/json",
					"Cache-Control: no-cache",
					"Content-Type: application/json"
				);
				break;
				
			case 'bcstatus':
			    $api  = Api::where('code', 'aeps')->first();
				$url  = "http://uat.mahagram.in/AEPS/APIBCStatus";
				$method = "POST";
				$parameter = json_encode(array(
					'Secretkey' => $api->password,
					'Saltkey' => $api->username,
					'bc_id' => $report->bc_id
				));

				$header = array(
					"Accept: application/json",
					"Cache-Control: no-cache",
					"Content-Type: application/json"
				);
				break;
			
			default:
				# code...
				break;
		}

		$result = \Myhelper::curl($url, $method, $parameter, $header);
		dd($result,$url,$header);
		if($result['response'] != ''){
			switch ($post->type) {
				case 'recharge':
					switch ($report->api->code) {
						case 'recharge1':
							$doc = json_decode($result['response']);
							if($doc->statuscode == "TXN" && ($doc->trans_status =="success" || $doc->trans_status =="pending")){
								$update['refno'] = $doc->refno;
								$update['status'] = "success";
							}elseif($doc->statuscode == "TXN" && $doc->trans_status =="reversed"){
								$update['status'] = "reversed";
								$update['refno'] = $doc->refno;
							}else{
								$update['status'] = "Unknown";
								$update['refno'] = $doc->message;
							}
							break;

						case 'recharge2':
							$doc = json_decode($result['response']);
							if(strtolower($doc->Status) == "success" || strtolower($doc->Status) == "pending"){
								$update['refno'] = $doc->OperatorRef;
								$update['status'] = "success";
							}elseif(strtolower($doc->Status) == "failed" || strtolower($doc->Status) == "failure" || strtolower($doc->Status) == "refund" || strtolower($doc->Status) == "refunded"){
								$update['status'] = "reversed";
								$update['refno'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "failed";
							}else{
								$update['status'] = "Unknown";
								$update['refno'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "Unknown";
							}
							break;
					}
					$product = "recharge";
					break;

				case 'billpayment':
					$doc = json_decode($result['response']);
					if(isset($doc->statuscode)){
						if(($doc->statuscode == "TXN" && $doc->data->status =="success") || ($doc->statuscode == "TXN" && $doc->data->status =="pending")){
							$update['refno'] = $doc->data->ref_no;
							$update['status'] = "success";
						}elseif($doc->statuscode == "TXN" && $doc->data->status =="reversed"){
							$update['status'] = "reversed";
						}else{
							$update['status'] = "Unknown";
						}
					}else{
						$update['status'] = "Unknown";
					}
					$product = "billpay";
					break;

				case 'utipancard':
					$doc = json_decode($result['response']);
					if(isset($doc[0]->StatusCode) && $doc[0]->StatusCode == "000"){
						$update['status'] = "success";
					}else{
						$update['status'] = "Unknown";
					}
					$product = "utipancard";
					break;

				case 'money':
					$doc = json_decode($result['response']);
					//dd($doc);
					if(isset($doc->statuscode) && $doc->statuscode == "000"){
					    if(isset($doc->Data[0]) && isset($doc->Data[0]->status)){
					       if(strtolower($doc->Data[0]->status) == "success"){
    						    $update['status'] = "success";
    						    $update['refno'] = $doc->Data[0]->opt_rrn;
					       }elseif(strtolower($doc->Data[0]->status) == "failure"){
					            $update['status'] = "reversed";
					            $update['refno'] = isset($doc->Data[0]->opt_rrn) ? $doc->Data[0]->opt_rrn : "Failed";
					       }elseif(strtolower($doc->Data[0]->status) == "pending"){
					            $update['status'] = "pending";
					       }else{
    						    $update['status'] = "Unknown";
        				   }
					    }else{
    						$update['status'] = "Unknown";
    					}
					}elseif(isset($doc->statuscode) && $doc->statuscode == "001"){
					    $update['status'] = "reversed";
					    $update['refno'] = isset($doc->Data[0]->opt_rrn) ? $doc->Data[0]->opt_rrn : "Failed";
					}else{
						$update['status'] = "Unknown";
					}
					$product = "aeps";
					break;

				case 'utiid':
					$doc = json_decode($result['response']);
					//dd($doc);
					if(isset($doc->statuscode) && $doc->statuscode == "TXN"){
						$update['status'] = "success";
						$update['remark'] = $doc->message;
					}elseif(isset($doc->statuscode) && $doc->statuscode == "TXF"){
						$update['status'] = "reversed";
						$update['remark'] = $doc->message;
					}elseif(isset($doc->statuscode) && $doc->statuscode == "TUP"){
						$update['status'] = "pending";
						$update['remark'] = $doc->message;
					}else{
						$update['status'] = "Unknown";
					}
					$product = "utiid";
					break;
					
				case 'upi':
					$doc = json_decode($result['response']);
					//dd($doc);
					if(isset($doc->code)){
					    
					       if($doc->code == "0x0200"){
    						    $update['status'] = "complete";
    						    $update['refno'] = $doc->data->customerRefId;
    						    $update['payid'] = $doc->data->bankTrxnId;
    						    $update['remark'] = isset($doc->message) ? $doc->message : "Success";
					       }else{
					            $update['status'] = "failed";
					            $update['refno'] = isset($doc->message) ? $doc->message : "Failed";
    						    $update['remark'] = isset($doc->message) ? $doc->message : "Failed";
					       }
					    
					}else{
						$update['status'] = "Unknown";
					}
					$product = "aeps";
					break;	
					
				case 'aeps':
					$doc = json_decode($result['response']);
					//dd($doc);
					if(isset($doc->statuscode) && $doc->statuscode == "000"){
					    if(isset($doc->Data[0]) && isset($doc->Data[0]->status)){
					       if($doc->Data[0]->status == "SUCCESS"){
    						    $update['status'] = "complete";
    						    $update['refno'] = $doc->Data[0]->rrn;
    						    $update['remark'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "Success";
					       }elseif($doc->Data[0]->status == "FAILURE"){
					            $update['status'] = "failed";
					            $update['refno'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "Failed";
    						    $update['remark'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "Failed";
					       }elseif($doc->Data[0]->status == "PENDING"){
					            $update['status'] = "pending";
    						    $update['remark'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "pending";
					       }else{
    						    $update['status'] = "Unknown";
        				   }
					    }else{
    						$update['status'] = "Unknown";
    					}
					}else{
						$update['status'] = "Unknown";
					}
					$product = "aeps";
					break;
					
				case 'payout':
					$doc = json_decode($result['response']);
					//dd($doc);
					if(isset($doc->code) && $doc->code == "0x0200"){
					    if(isset($doc->data->status) && isset($doc->data->status)){
					       if($doc->data->status == "success"){
    						    $update['status'] = "approved";
    						    $update['payoutref'] = $doc->data->utr;
    						    
					       }elseif($doc->data->status == "failed"){
					            $update['status'] = "reversed";
    						    $update['remark'] = isset($doc->data->remark) ? $doc->data->remark : "Failed";
					       }elseif($doc->data->status == "pening"){
					            $update['status'] = "pending";
    						    $update['remark'] = isset($doc->data->remark) ? $doc->data->remark : "pending";
					       }else{
    						    $update['status'] = "Unknown";
        				   }
					    }else{
    						$update['status'] = "Unknown";
    					}
					}else{
						$update['status'] = "Unknown";
					}
					$product = "payout";
					break;	

				case 'matm':
					$doc = json_decode($result['response']);
					//dd($doc);
					if(isset($doc->statuscode) && $doc->statuscode == "000"){
					    if(isset($doc->Data[0]) && isset($doc->Data[0]->status)){
					       if(strtolower($doc->Data[0]->status) == "success"){
    						    $update['status'] = "complete";
    						    $update['amount'] = $doc->Data[0]->amount;
    						    $update['refno']  = $doc->Data[0]->rrn;
    						    $update['aadhar'] = $doc->Data[0]->cardno;
    						    $update['payid']  = isset($doc->Data[0]->stanno) ? $doc->Data[0]->stanno : "Failed";
    						    $update['remark'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "Success";
					       }elseif(strtolower($doc->Data[0]->status) == "failed"){
					            $update['status'] = "failed";
					            $update['amount'] = $doc->Data[0]->amount;
					            $update['refno']  = isset($doc->Data[0]->rrn) ? $doc->Data[0]->rrn : "Failed";
					            $update['payid']  = isset($doc->Data[0]->stanno) ? $doc->Data[0]->stanno : "Failed";
    						    $update['aadhar'] = $doc->Data[0]->cardno;
    						    $update['remark'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "Failed";
					       }elseif(strtolower($doc->Data[0]->status) == "pending"){
					            $update['status'] = "pending";
					            $update['amount'] = $doc->Data[0]->amount;
					            $update['payid']  = isset($doc->Data[0]->stanno) ? $doc->Data[0]->stanno : "Failed";
					            $update['refno']  = isset($doc->Data[0]->rrn) ? $doc->Data[0]->rrn : "Failed";
    						    $update['remark'] = isset($doc->Data[0]->bankmessage) ? $doc->Data[0]->bankmessage : "pending";
					       }else{
    						    $update['status'] = "Unknown";
        				   }
					    }else{
    						$update['status'] = "Unknown";
    					}
					}elseif($doc->statuscode == "002"){
					            $update['status'] = "failed";
					       }
					else{
						$update['status'] = "Unknown";
					}
					$product = "matm";
					break;	
					
				case 'bcstatus':
    			    $doc = json_decode($result['response']);
					//dd($doc);
					
					if(isset($doc[0]->status) && $doc[0]->status == "Active"){
					    $update['status'] = "success";
					}elseif(isset($doc[0]->status) && $doc[0]->status == "Rejected"){
					    $update['status'] = "rejected";
					    $update['remark'] = isset($doc[0]->remarks) ? $doc[0]->remarks : "Failed";
					}else{
						$update['status'] = "Unknown";
					}
    				break;
			}

			if ($update['status'] != "Unknown") {
				switch ($post->type) {
					case 'recharge':
					case 'billpayment':
					case 'utipancard':
					case 'money':
				
						$reportupdate = Report::updateOrCreate(['id'=> $post->id], $update);
						if ($reportupdate && $update['status'] == "reversed") {
							\Myhelper::transactionRefund($post->id);
						}
						break;
                    
                    case 'bcstatus':
						$reportupdate = Mahaagent::where('id', $post->id)->update($update);
						break;
						
                    case 'aeps':
						$reportupdate = Report::updateOrCreate(['id'=> $post->id], $update);
						
						if($report->status == "pending" && in_array($update['status'], ["complete","success"]) ){
						    $user = User::where('id', $report->user_id)->first();
						    $insert = [
                                "mobile" => $report->mobile,
                                "aadhar" => $report->aadhar,
                                "api_id" => $report->api_id,
                                "txnid"  => $report->txnid,
                                "refno"  => "Txnid - ".$report->id. " Cleared",
                                "amount" => $report->amount,
                                "bank"   => $report->bank,
                                "user_id"=> $report->user_id,
                                "balance" => $user->mainwallet,
                                'aepstype'=> $report->aepstype,
                                'status'  => 'success',
                                'authcode'=> $report->authcode,
                                'payid'=> $report->payid,
                                'mytxnid'=> $report->mytxnid,
                                'terminalid'=> $report->terminalid,
                                'TxnMedium'=> $report->TxnMedium,
                                'credited_by' => $report->credited_by,
                                'type' => 'credit'
                            ];
                            if($report->aepstype == "CW"){
                                if($report->amount > 99 && $report->amount <= 499){
                                    $provider = Provider::where('recharge1', 'aeps1')->first();
                                }elseif($report->amount>499 && $report->amount<=1000){
                                    $provider = Provider::where('recharge1', 'aeps2')->first();
                                }elseif($report->amount>1000 && $report->amount<=1500){
                                    $provider = Provider::where('recharge1', 'aeps3')->first();
                                }elseif($report->amount>1500 && $report->amount<=2000){
                                    $provider = Provider::where('recharge1', 'aeps4')->first();
                                }elseif($report->amount>2000 && $report->amount<=2500){
                                    $provider = Provider::where('recharge1', 'aeps5')->first();
                                }elseif($report->amount>2500 && $report->amount<=3000){
                                    $provider = Provider::where('recharge1', 'aeps6')->first();
                                }elseif($report->amount>3000 && $report->amount<=4000){
                                    $provider = Provider::where('recharge1', 'aeps7')->first();
                                }elseif($report->amount>4000 && $report->amount<=5000){
                                    $provider = Provider::where('recharge1', 'aeps8')->first();
                                }elseif($report->amount>5000 && $report->amount<=7000){
                                    $provider = Provider::where('recharge1', 'aeps9')->first();
                                }elseif($report->amount>7000 && $report->amount<=10000){
                                    $provider = Provider::where('recharge1', 'aeps10')->first();
                                }
                            }else{
                                $provider = Provider::where('recharge1', 'aadharpay')->first();
                            }
                    
                            $post['provider_id'] = $provider->id;
                            $post['service']     = $provider->type;
                
                            if($report->aepstype == "CW"){
                                if($report->amount > 500){
                                    $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id,$user->role->slug);
                                }else{
                                    $usercommission = 0;
                                }
                            }elseif($report->aepstype == "AP"){
                                $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id,$user->role->slug);
                            }else{
                                $usercommission = 0;
                            }
                            
                            $insert['charge'] = $usercommission;
                            if($report->aepstype == "CW"){
                                $action = User::where('id', $report->user_id)->increment('mainwallet', $report->amount+$usercommission);
                            }else{
                                $action = User::where('id', $report->user_id)->increment('mainwallet', $report->amount-$usercommission);
                            }
                            
                            if($action){
                                $aeps = Report::create($insert);
                                if($report->amount > 500){
                                    \Myhelper::commission(Report::find($aeps->id));
                                }
                            }
						}
						break;
						
					case 'payout':
					            $myaepsreport=  Aepsfundrequest::where('payoutid', $doc->data->clientRefId)->first();
					            
					            $reportupdate = Aepsfundrequest::updateOrCreate(['id'=> $post->id], $update); 
					            if($report->status == "pending" && in_array($update['status'], ["complete","success"]) ){
					           
					               
					               
                                   $aepsreports=Report::where('id', $myaepsreport->id)->first();
                                   Report::where('payid', $myaepsreport->id)->update(['status' => "success", "refno" => isset($doc->data->utr) ? $decode->data->utr : $doc->data->utr]); 
                                
                                
					            }
					            else{
					                  $aepsreports=Report::where('id', $myaepsreport->id)->first();
					                  User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount']+$aepsreports['charge']);
                                     Report::where('payid', $myaepsreport->id)->update(['status' => "failed", "refno" => $doc->data->remark]);
					                
					            }
					    
        						
           
						break;		
						
					case 'upi':
						$reportupdate = Report::updateOrCreate(['id'=> $post->id], $update);
						
						if($report->status == "pending" && in_array($update['status'], ["complete","success"]) ){
						    $user = User::where('id', $report->user_id)->first();
						    $insert = [
                                "mobile" => $report->mobile,
                                "aadhar" => $report->aadhar,
                                "api_id" => $report->api_id,
                                "txnid"  => $report->txnid,
                                "refno"  => "Txnid - ".$report->id. " Cleared",
                                "amount" => $report->amount,
                                "bank"   => $report->bank,
                                "user_id"=> $report->user_id,
                                "balance" => $user->mainwallet,
                                'aepstype'=> $report->aepstype,
                                'status'  => 'success',
                                'authcode'=> $report->authcode,
                                'payid'=> $report->payid,
                                'mytxnid'=> $report->mytxnid,
                                'terminalid'=> $report->terminalid,
                                'TxnMedium'=> $report->TxnMedium,
                                'credited_by' => $report->credited_by,
                                'type' => 'credit'
                            ];
                            
                                if($report->amount > 99 && $report->amount <= 499){
                                    $provider = Provider::where('recharge1', 'aeps1')->first();
                                }elseif($report->amount>499 && $report->amount<=1000){
                                    $provider = Provider::where('recharge1', 'aeps2')->first();
                                }elseif($report->amount>1000 && $report->amount<=1500){
                                    $provider = Provider::where('recharge1', 'aeps3')->first();
                                }elseif($report->amount>1500 && $report->amount<=2000){
                                    $provider = Provider::where('recharge1', 'aeps4')->first();
                                }elseif($report->amount>2000 && $report->amount<=2500){
                                    $provider = Provider::where('recharge1', 'aeps5')->first();
                                }elseif($report->amount>2500 && $report->amount<=3000){
                                    $provider = Provider::where('recharge1', 'aeps6')->first();
                                }elseif($report->amount>3000 && $report->amount<=4000){
                                    $provider = Provider::where('recharge1', 'aeps7')->first();
                                }elseif($report->amount>4000 && $report->amount<=5000){
                                    $provider = Provider::where('recharge1', 'aeps8')->first();
                                }elseif($report->amount>5000 && $report->amount<=7000){
                                    $provider = Provider::where('recharge1', 'aeps9')->first();
                                }elseif($report->amount>7000 && $report->amount<=10000){
                                    $provider = Provider::where('recharge1', 'aeps10')->first();
                                }
                            
                    
                            $post['provider_id'] = $provider->id;
                            $post['service']     = $provider->type;
                
                            
                                if($report->amount > 500){
                                    $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id,$user->role->slug);
                                }else{
                                    $usercommission = 0;
                                }
                            
                            
                            $insert['charge'] = $usercommission;
                           
                                $action = User::where('id', $report->user_id)->increment('mainwallet', $report->amount+$usercommission);
                            
                            if($action){
                                $aeps = Report::create($insert);
                                if($report->amount > 500){
                                    \Myhelper::commission(Aepsreport::find($aeps->id));
                                }
                            }
						}
						break;	

					case 'matm':
						$reportupdate = Microatmreport::where('id', $post->id)->update($update);
						
						if($report->status == "pending" && $update['status'] == "complete"){
						    $user     = User::where('id', $report->user_id)->first();
						    $myreport = Microatmreport::where('id', $post->id)->first();

						    $insert = [
                                "mobile"  => $myreport->mobile,
                                "aadhar"  => $myreport->aadhar,
                                "api_id"  => $myreport->api_id,
                                "txnid"   => $myreport->txnid,
                                "refno"   => "Txnid - ".$myreport->id. " Cleared",
                                "amount"  => $myreport->amount,
                                "bank"    => $myreport->bank,
                                "user_id" => $myreport->user_id,
                                "balance" => $user->mainwallet,
                                'aepstype'=> $myreport->aepstype,
                                'status'  => 'success',
                                'authcode'=> $myreport->authcode,
                                'payid'	  => $myreport->payid,
                                'mytxnid' => $myreport->mytxnid,
                                'terminalid' => $myreport->terminalid,
                                'TxnMedium'  => $myreport->TxnMedium,
                                'credited_by'=> $myreport->credited_by,
                                'type' 	  => 'credit'
                            ];

                            if($myreport->amount > 0){
	                            if($myreport->amount >= 100 && $myreport->amount <= 500){
	                                $provider = Provider::where('recharge1', 'matm1')->first();
	                            }elseif($myreport->amount > 500 && $myreport->amount <= 1000){
	                                $provider = Provider::where('recharge1', 'matm2')->first();
	                            }elseif($myreport->amount > 1000 && $myreport->amount <= 1500){
	                                $provider = Provider::where('recharge1', 'matm3')->first();
	                            }elseif($myreport->amount > 1500 && $myreport->amount <= 2000){
	                                $provider = Provider::where('recharge1', 'matm4')->first();
	                            }elseif($myreport->amount > 2000 && $myreport->amount <= 2500){
	                                $provider = Provider::where('recharge1', 'matm5')->first();
	                            }elseif($myreport->amount > 2500 && $myreport->amount <= 3000){
	                                $provider = Provider::where('recharge1', 'matm6')->first();
	                            }elseif($myreport->amount > 3000 && $myreport->amount <= 4000){
	                                $provider = Provider::where('recharge1', 'matm7')->first();
	                            }elseif($myreport->amount > 4000 && $myreport->amount <= 5000){
	                                $provider = Provider::where('recharge1', 'matm8')->first();
	                            }elseif($myreport->amount > 5000 && $myreport->amount <= 7000){
	                                $provider = Provider::where('recharge1', 'matm9')->first();
	                            }elseif($myreport->amount > 7000 && $myreport->amount <= 10000){
	                                $provider = Provider::where('recharge1', 'matm10')->first();
	                            }
	                            
	                            $insert['provider_id'] = $provider->id;
                                if($myreport->amount > 500){
                                    $insert['charge'] = \Myhelper::getCommission($myreport->amount, $user->scheme_id, $insert['provider_id'], $user->role->slug);
                                }else{
                                	$insert['charge'] = 0;
                                }
	                        }else{
	                        	$insert['provider_id'] = 0;
	                        	$insert['charge'] = 0;
	                        }
                            
                            $action = User::where('id', $report->user_id)->increment('mainwallet',$myreport->amount + $insert['charge']);
                            if($action){
                                 $matm = Report::create($insert);

                                if($report->amount > 500){
                                    \Myhelper::commission(Report::find($matm->id));
                                }
                            }
						}
						break;
						
					case 'utiid':
						$reportupdate = Utiid::updateOrCreate(['id'=> $post->id], $update);
						break;
				}
			}
			return response()->json($update, 200);
		}else{
			return response()->json(['status' => "Status Not Fetched , Try Again."], 400);
		}
	}
	public function resendCallback(Request $post)
	{
	     $rules = array(
					'id'    => 'required',
                    'type'    => 'required'
                );
                
        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
		}
		
		$upireport = Upireport::where('id',$post->id)->first();
		if(!$upireport){
		   	return response()->json(['status' => "Somethig is wrong"], 400); 
		}
		$user = User::where('id',$upireport->user_id)->first(['role_id','callbackurl']);
		//dd($user->role->slug);
		 if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
            
                 
                        
                $output['status'] = $upireport->status;
                $output['clientid']  = $upireport->mytxnid;
                $output['txnid']     = $upireport->txnid;
                $output['vpaadress']   = $upireport->payer_vpa;
                $output['npciTxnId']   = $upireport->refno;
                $output['payId']   = $upireport->payid;
                $output['amount']   = $upireport->amount;
                $output['bankTxnId']   = $upireport->option1;
                $output['payerVpa']  = $upireport->payer_vpa;
                $output['payerAccName']= $upireport->payerAccName;
                $output['orderAmount']= $upireport->orderAmount;
                
                \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $upireport->mytxnid);
                return response()->json(["status"=>"success"],200);
            }
		
	}
	public function chargeBackUpdate(Request $post)
	{
	   $rules = array(
					'orderId' => 'required'
                );
                
        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
		}
		
		$upiReport = \DB::table('upireports')->where('id',$post->orderId)->where('status','success')->first(['id','user_id','amount']);
		if(!$upiReport){
		    return response()->json(['status'=>"error","message"=>"Something went wrong"]);
		}
		
		$updateUpi=\DB::table('upireports')
                  ->where('id', $post->orderId)
                  ->update(['status' => 'dispute']);
                  
		$incrementDisWallet = User::where('id', $upiReport->user_id)->increment('disputewallet', $upiReport->amount);
		
		return response()->json(['status'=>"success","message"=>"Chargeback raised Successfully"]);
	}
	
	
	public function disputeRaise(Request $post)
    {
        $rules = array(
            'orderId' => 'required|array',
        );
    
        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        foreach ($post->orderId as $orderId) {
            
            $upiReport = \DB::table('upireports')->where('refno', $orderId)->where('status', 'success')->first(['id', 'user_id', 'amount']);
            //dd($upiReport);
            if (!$upiReport) {
                return response()->json(['status' => "error", "message" => "Something went wrong for order ID: $orderId"]);
            }
            
    
            $updateUpi = \DB::table('upireports')
                ->where('refno', $orderId)
                ->update(['status' => 'dispute']);
    
            $incrementDisWallet = User::where('id', $upiReport->user_id)->increment('disputewallet', $upiReport->amount);
        }
    
        return response()->json(['status' => "success", "message" => "Chargeback raised Successfully"]);
    }

}