<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Illuminate\Support\Collection;
use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\FromCollection;
use VioletWaves\Excel\Concerns\RegistersEventListeners;
use VioletWaves\Excel\Concerns\ShouldAutoSize;
use VioletWaves\Excel\Concerns\WithEvents;
use VioletWaves\Excel\Concerns\WithTitle;
use VioletWaves\Excel\Events\BeforeWriting;
use VioletWaves\Excel\Tests\TestCase;
use VioletWaves\Excel\Writer;

class SheetWith100Rows implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    /**
     * @var string
     */
    private $title;

    /**
     * @param  string  $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $collection = new Collection;
        for ($i = 0; $i < 100; $i++) {
            $row = new Collection();
            for ($j = 0; $j < 5; $j++) {
                $row[] = $this->title() . '-' . $i . '-' . $j;
            }

            $collection->push($row);
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param  BeforeWriting  $event
     */
    public static function beforeWriting(BeforeWriting $event)
    {
        TestCase::assertInstanceOf(Writer::class, $event->writer);
    }
}
