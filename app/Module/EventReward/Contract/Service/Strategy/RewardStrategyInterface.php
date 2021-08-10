<?php declare(strict_types=1);

namespace App\Module\EventReward\Contract\Service\Strategy;

use App\Module\EventReward\Model\UserEventLog;
use App\Module\EventReward\ValueObject\Event;

interface RewardStrategyInterface
{
    /**
     * Check the event eligibility against the incentive rules
     * (but not for whether it was already rewarded or not, that complete check is done in RewardService)
     *
     * @param Event $event
     * @param UserEventLog $pastEvents
     * @return bool
     */
    public function isEligible(Event $event, UserEventLog $pastEvents): bool;
}
