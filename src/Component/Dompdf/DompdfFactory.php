<?php

namespace App\Component\Dompdf;

use Dompdf\Dompdf;

class DompdfFactory
{
    public function create(): Dompdf
    {
        return new Dompdf();
    }
}
