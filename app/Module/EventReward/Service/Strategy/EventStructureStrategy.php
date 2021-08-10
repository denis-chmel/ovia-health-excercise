<?php declare(strict_types=1);

namespace App\Module\EventReward\Service\Strategy;

use App\Module\EventReward\Contract\Service\Strategy\RewardStrategyInterface;
use App\Module\EventReward\Model\UserEventLog;
use App\Module\EventReward\ValueObject\Event;

class EventStructureStrategy implements RewardStrategyInterface
{
    private string $eventType;
    private string $eventValue;
    private int $daysInARow;

    public function __construct(string $eventType, string $eventValue, int $daysInARow)
    {
        $this->eventType = $eventType;
        $this->eventValue = $eventValue;
        $this->daysInARow = $daysInARow;
    }

    /**
     * @inheritDoc
     */
    public function isEligible(Event $event, UserEventLog $pastEvents): bool
    {
        $daysAgo = [];
        $events = array_merge($pastEvents->getEvents(), [$event]);
        foreach ($events as $pastEvent) {
            if ($pastEvent->getType() !== $this->eventType) {
                continue;
            }
            if ($pastEvent->getValue() !== $this->eventValue) {
                continue;
            }
            $daysAgo[] = $this->getDiffInDays($pastEvent->getDateTime(), $event->getDateTime());
        }
        // leave unique day nos, and sort ascending
        sort($daysAgo);
        $normalizedUniqueDaysAgo = array_values(array_unique($daysAgo));
        $compareWith = range(0, $this->daysInARow - 1);

        return !array_diff($compareWith, $normalizedUniqueDaysAgo);
    }

    private function getDiffInDays(\DateTimeInterface $date1, \DateTimeInterface $date2): int
    {
        return abs($date1->diff($date2)->days);
    }
}
