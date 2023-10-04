<?php

namespace Assist\Interaction\Models;

use App\Models\BaseModel;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @mixin IdeHelperInteraction
 */
class Interaction extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use AuditableTrait;

    protected $fillable = [
        'user_id',
        'interactable_id',
        'interactable_type',
        'interaction_campaign_id',
        'interaction_driver_id',
        'interaction_institution_id',
        'interaction_outcome_id',
        'interaction_relation_id',
        'interaction_status_id',
        'interaction_type_id',
        'start_datetime',
        'end_datetime',
        'subject',
        'description',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function getWebPermissions(): Collection
    {
        return collect(['import', ...$this->webPermissions()]);
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->interactable instanceof Subscribable ? $this->interactable : null;
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo(
            name: 'interactable',
            type: 'interactable_type',
            id: 'interactable_id',
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InteractionCampaign::class, 'interaction_campaign_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(InteractionDriver::class, 'interaction_driver_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(InteractionInstitution::class, 'interaction_institution_id');
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(InteractionOutcome::class, 'interaction_outcome_id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(InteractionRelation::class, 'interaction_relation_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InteractionStatus::class, 'interaction_status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InteractionType::class, 'interaction_type_id');
    }
}
