<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;
use Picqer\Barcode\BarcodeGeneratorPNG; 

class InvoiceController extends Controller
{
    public function download($id)
    {

        $transaction = Transaction::with(['transactionItems.product.category', 'member'])->findOrFail($id);

        $generator = new BarcodeGeneratorPNG();

        $refNumber = $transaction->transaction_number ?? '000000'; 
        $barcode = base64_encode($generator->getBarcode($refNumber, $generator::TYPE_CODE_128));

        $pdf = Pdf::loadView('pdf.invoice-a5', [
            'data' => $transaction,   
            'barcode' => $barcode     
        ])->setPaper('a5', 'landscape');

        return $pdf->stream('Invoice_'.$transaction->transaction_number.'.pdf');
    }
}