<?php

namespace App\Component\Dompdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfFactory
{
    public function create(): Dompdf
    {
        return new Dompdf(
            (new Options())
                ->set('defaultFont', 'DejaVu Serif')
        );
    }
}
