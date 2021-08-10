<?php declare(strict_types=1);

namespace App\Module\EventReward\Service\Strategy;

use App\Module\EventReward\Contract\Service\Strategy\RewardStrategyInterface;
use App\Module\EventReward\Model\UserEventLog;
use App\Module\EventReward\ValueObject\Event;

class SingleEventStrategy implements RewardStrategyInterface
{
    private string $eventType;
    private string $eventValue;

    public function __construct(string $eventType, string $eventValue)
    {
        $this->eventType = $eventType;
        $this->eventValue = $eventValue;
    }

    /**
     * @inheritDoc
     */
    public function isEligible(Event $event, UserEventLog $pastEvents): bool
    {
        return $event->getType() === $this->eventType
            && $event->getValue() === $this->eventValue;
    }
}
