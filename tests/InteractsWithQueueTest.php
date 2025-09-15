<?php

namespace VioletWaves\Excel\Tests;

use Illuminate\Queue\InteractsWithQueue;
use VioletWaves\Excel\Jobs\AppendDataToSheet;
use VioletWaves\Excel\Jobs\AppendQueryToSheet;
use VioletWaves\Excel\Jobs\AppendViewToSheet;
use VioletWaves\Excel\Jobs\QueueExport;
use VioletWaves\Excel\Jobs\ReadChunk;

class InteractsWithQueueTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_read_chunk_job_can_interact_with_queue()
    {
        $this->assertContains(InteractsWithQueue::class, class_uses(ReadChunk::class));
    }

    public function test_append_data_to_sheet_job_can_interact_with_queue()
    {
        $this->assertContains(InteractsWithQueue::class, class_uses(AppendDataToSheet::class));
    }

    public function test_append_query_to_sheet_job_can_interact_with_queue()
    {
        $this->assertContains(InteractsWithQueue::class, class_uses(AppendQueryToSheet::class));
    }

    public function test_append_view_to_sheet_job_can_interact_with_queue()
    {
        $this->assertContains(InteractsWithQueue::class, class_uses(AppendViewToSheet::class));
    }

    public function test_queue_export_job_can_interact_with_queue()
    {
        $this->assertContains(InteractsWithQueue::class, class_uses(QueueExport::class));
    }
}
