<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TransactionController extends Controller
{

    public function printInvoice($id)
    {
        $transaction = Transaction::with([
            'items.product.subCategory.category', 
            'member'
        ])->findOrFail($id);
        
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($transaction->transaction_number, $generator::TYPE_CODE_128);
     
        $barcode = base64_encode($barcodeData);

        $pdf = Pdf::loadView('pdf.invoice-a5', [
            'transaction' => $transaction,
            'data'        => $transaction, 
            'barcode'     => $barcode 
        ]);

        return $pdf->stream('invoice-' . $transaction->transaction_number . '.pdf');
    }
}