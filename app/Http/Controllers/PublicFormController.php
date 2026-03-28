<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\FormStore;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;


class PublicFormController extends Controller
{
    /**
     * Tampilkan QR (view di browser)
     */
    public function qr($slug)
    {
        // ambil data berdasarkan slug
        $formStore = FormStore::where('custom_url_slug', $slug)->firstOrFail();

        // link tujuan saat QR di-scan
        $link = url('/form/' . $formStore->custom_url_slug);

        // generate QR (SVG default)
        $qr = QrCode::size(300)->generate($link);

        return view('qr.view', compact('qr', 'link', 'formStore'));
    }

    /**
     * Download QR dalam bentuk PNG
     */
    public function downloadQr($slug)
    {
        $formStore = FormStore::where('custom_url_slug', $slug)->firstOrFail();

        $link = url('/form/' . $formStore->custom_url_slug);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($link)
            ->size(300)
            ->margin(10)
            ->build();

        return response($result->getString())
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$formStore->custom_url_slug.'.png"');
    }
}