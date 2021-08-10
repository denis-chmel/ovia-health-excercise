<?php declare(strict_types=1);

namespace App\Module\EventReward\Model;

use App\Module\EventReward\ValueObject\Event;

class UserEventLog extends BaseDocumentModel
{
    /** @var Event[] */
    private array $events;

    /**
     * @param Event[] $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param Event[] $events
     */
    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    public function addEvent(Event $event): void
    {
        $this->events[] = $event;
    }
}
