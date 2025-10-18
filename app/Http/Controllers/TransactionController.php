<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;

class TransactionController extends Controller
{

public function printInvoice($id)
{
    $transaction = Transaction::with(['items.product.category', 'member'])->findOrFail($id);
    $pdf = Pdf::loadView('pdf.invoice-a5', compact('transaction'));
    return $pdf->stream('invoice-'.$transaction->transaction_number.'.pdf');
}

}
