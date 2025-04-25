<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;


class ChargebackController extends Controller
{
    public function index($type)
    {
        if(\Myhelper::hasNotRole('admin')){
            abort(403);
        }
        switch($type){
            
            case 'uploadchargeback':
                $data['outletNames'] = '';
                
                break;    
            default:
                $data['outletNames'] = [];
                break;
            
        }
        $data['type'] = $type;

        return view("chargeback.".$type)->with($data);
        
    }
    
    public function uploadchargebackupdate(Request $request)
    {
        $request->validate([
            'uploadChargeback' => 'required|mimes:xls,xlsx',
        ]);

        $file = $request->file('uploadChargeback');
        $path = $file->getRealPath();
        $data = Excel::toArray([], $path);
        dd($data);
        if ($file) {
            
            $path = $file->getRealPath();

            $spreadsheet = IOFactory::load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                dd($data);
                $utrno = strval($data[0]); 
                $amount = (float)$data[2];

                if (is_numeric($amount) && is_string($post['txnid'])) {
                    
                    \DB::table('reports')
                        ->where('refno', $utrno)
                        ->update([
                            'chargebackamt' => (float)$amount,
                            'chargebackOrdId' => (string)$post['txnid'],
                        ]);
                    $dataReport =  \DB::table('reports')
                        ->where('refno', $utrno)->first();
                   
                    $chargebackUpdate = \App\Model\Chargeback::insert([
                            'user_id'=>'',
                            'chargebackDate'=>'',
                            'chargebackPercent'=>'0',
                            'collectionAmount'=>'0',
                            'noOfTransactions'=>'0',
                            'chargeBackAmount'=> (float)$amount,
                            'txnid'=>(string)$post['txnid'],
                            'chargebackvia'=>'excelupload'
                            
                            ]);    
                } else {
                    //dd("sdsdsd");
                    
                    \Log::error("Invalid data in row: " . json_encode($data));
                }
            }

            return response()->json(['statuscode' => 'TXN', 'message' => 'Chargeback upload successfully']);
        }
    }

}