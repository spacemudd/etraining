<?php

namespace App\Services;

use App\Models\Back\TraineeCertificate;
use PDF;

class CertificatesService
{
    public $certificate;

    public function __construct(TraineeCertificate $certificate)
    {
        $this->certificate = $certificate;
    }

    static public function new($certificate_id)
    {
        return new CertificatesService(TraineeCertificate::with(['trainee', 'course'])->findOrFail($certificate_id));
    }

    public function pdf()
    {
        return PDF::setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape')
            ->setOption('encoding','utf-8')
            ->setOption('dpi', 300)
            ->setOption('image-dpi', 300)
            ->setOption('lowquality', false)
            ->setOption('no-background', false)
            ->setOption('enable-internal-links', true)
            ->setOption('enable-external-links', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('no-background', false)
            ->setOption('margin-right', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)
            ->loadView('pdf.certificate.show', [
                'certificate' => $this->certificate,
            ]);
    }
}
