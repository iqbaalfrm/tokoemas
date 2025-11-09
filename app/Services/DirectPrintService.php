<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Mike42\Escpos\Printer;
use App\Models\Transaction;
use Mike42\Escpos\EscposImage;
use Filament\Notifications\Notification;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Exception;

class DirectPrintService
{
    public function print($orderToPrint)
    {
        try {
            $order = Transaction::with(['items.product', 'paymentMethod'])->findOrFail($orderToPrint);
            
            $setting = Setting::first();

            if (!$setting || !$setting->name_printer_local) {
                throw new Exception("Nama printer lokal belum di-setting.");
            }

            $connector = new WindowsPrintConnector($setting->name_printer_local);
            $printer = new Printer($connector);

            $logoPath = public_path('storage/' . $setting->image);
            if (file_exists($logoPath) && $setting->image) {
                $logo = EscposImage::load($logoPath, true);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->bitImage($logo);
            }

            $lineWidth = 32;

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 2);
            $printer->setEmphasis(true);
            $printer->text($setting->shop . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text($setting->address . "\n");
            $printer->text($setting->phone . "\n");
            $printer->text("================================\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No.Transaksi: " . $order->transaction_number . "\n");
            $printer->text("Pembayaran: " . $order->paymentMethod->name . "\n");
            $printer->text("Tanggal: " . $order->created_at->format('d-m-Y H:i:s') . "\n");
            $printer->text("================================\n");
            $printer->text($this->formatRow("Nama Barang", "Qty", "Harga", $lineWidth) . "\n");
            $printer->text("--------------------------------\n");
            
            foreach ($order->items as $item) {
                $printer->text($this->formatRow(
                    $item->product->name, 
                    $item->quantity, 
                    number_format($item->price),
                    $lineWidth
                ) . "\n");
            }

            $printer->text("--------------------------------\n");

            $printer->setEmphasis(true);
            $printer->text($this->formatRow("Total", "", number_format($order->total), $lineWidth) . "\n");
            $printer->text($this->formatRow("Nominal Bayar", "", number_format($order->cash_received), $lineWidth) . "\n");
            $printer->text($this->formatRow("Kembalian", "", number_format($order->change), $lineWidth) . "\n");
            $printer->setEmphasis(false);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("================================\n");
            $printer->text("Terima Kasih!\n");
            $printer->text("================================\n");

            $printer->cut();
            $printer->close();
            
            Notification::make()
                ->title('Struk berhasil dicetak')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Gagal Mencetak Struk')
                ->body($e->getMessage())
                ->icon('heroicon-o-printer')
                ->danger()
                ->send();
        }
    }

    private function formatRow($name, $qty, $price, $lineWidth)
    {
        $nameWidth = 16;
        $qtyWidth = 8;
        $priceWidth = 8;

        $nameLines = str_split($name, $nameWidth);
        $output = '';

        for ($i = 0; $i < count($nameLines) - 1; $i++) {
            $output .= str_pad($nameLines[$i], $lineWidth) . "\n";
        }

        $lastLine = $nameLines[count($nameLines) - 1];
        $lastLine = str_pad($lastLine, $nameWidth);
        $qty = str_pad($qty, $qtyWidth, " ", STR_PAD_BOTH);
        $price = str_pad($price, $priceWidth, " ", STR_PAD_LEFT);

        $output .= $lastLine . $qty . $price;

        return $output;
    }
}