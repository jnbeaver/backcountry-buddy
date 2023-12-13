<?php

namespace App\Domain\Services;

use App\Component\Dompdf\DompdfFactory;
use App\Domain\Entity\GearItemImmutable;
use App\Domain\Entity\TripImmutable;
use App\Domain\ValueObject\TripPlan;
use Clegginabox\PDFMerger\PDFMerger;
use Illuminate\Support\Collection;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

readonly class TripPlanService
{
    private string $cssFilename;

    public function __construct(
        private TripCriteriaService $tripCriteriaService,
        private MarkdownConverter $markdownConverter,
        private DompdfFactory $dompdfFactory,
        private Filesystem $filesystem,
        private PDFMerger $pdfMerger,
        #[Autowire(param: 'app.trip_plan.css_file')] string $cssFilename
    ) {
        $this->cssFilename = $cssFilename;
    }

    /**
     * @param string $filename
     * @param TripImmutable $trip
     * @param GearItemImmutable[] $gear
     */
    public function create(
        string $filename,
        TripImmutable $trip,
        array $gear
    ): void {
        $tripPlan = new TripPlan($trip, $gear, $this->tripCriteriaService);
        $chapterFilenames = new Collection();

        foreach ($tripPlan->getChapters() as $i => $chapter) {
            $markdown = $chapter->getContent();

            if ($markdown === null) {
                continue;
            }

            $htmlContent = $this->markdownConverter->convert($markdown);
            $css = $this->cssFilename ? file_get_contents($this->cssFilename) : '';

            $html = <<<HTML
<html>
    <head>
        <style>
            {$css}
        </style>
    </head>
    <body>
        {$htmlContent}
    </body>
</html>
HTML;

            $chapterFilename = $this->getChapterFilename($filename, $i + 1);
            $dompdf = $this->dompdfFactory->create();
            $dompdf->loadHtml($html);
            $dompdf->render();
            $this->filesystem->dumpFile($chapterFilename, $dompdf->output());

            $chapterFilenames->add($chapterFilename);
        }

        if ($chapterFilenames->isEmpty()) {
            return;
        }

        if ($chapterFilenames->count() === 1) {
            if ($this->filesystem->exists($filename)) {
                $this->filesystem->remove($filename);
            }

            $this->filesystem->rename($chapterFilenames->first(), $filename);

            return;
        }

        $chapterFilenames->each(fn (string $chapterFilename) => $this->pdfMerger->addPDF($chapterFilename));
        $this->pdfMerger->merge('file', $filename);
        $chapterFilenames->each(fn (string $chapterFilename) => $this->filesystem->remove($chapterFilename));
    }

    private function getChapterFilename(string $filename, int $num): string
    {
        return sprintf(
            '%s_%s.pdf',
            str_ends_with($filename, '.pdf') ? substr($filename, 0, -4) : $filename,
            $num
        );
    }
}
