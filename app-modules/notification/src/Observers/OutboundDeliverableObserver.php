<?php

namespace AdvisingApp\Notification\Observers;

use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;
use AdvisingApp\Timeline\Events\TimelineableRecordDeleted;

class OutboundDeliverableObserver
{
    public function created(OutboundDeliverable $outboundDeliverable): void
    {
        if ($outboundDeliverable->related && in_array($outboundDeliverable->related::class, $outboundDeliverable->timelineables)) {
            TimelineableRecordCreated::dispatch($outboundDeliverable, $outboundDeliverable);
        }
    }

    public function deleted(OutboundDeliverable $outboundDeliverable): void
    {
        if ($outboundDeliverable->related && in_array($outboundDeliverable->related::class, $outboundDeliverable->timelineables)) {
            TimelineableRecordDeleted::dispatch($outboundDeliverable, $outboundDeliverable);
        }
    }
}
