<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;

class InvoiceController extends Controller
{
    public function download($id)
    {
        $transaction = Transaction::with(['transactionItems.product.category', 'member'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice-a5', compact('transaction'))
                  ->setPaper('a5', 'landscape');

        return $pdf->download('Invoice_'.$transaction->transaction_number.'.pdf');
    }
}
