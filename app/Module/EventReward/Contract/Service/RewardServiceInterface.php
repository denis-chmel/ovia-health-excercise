<?php declare(strict_types=1);

namespace App\Module\EventReward\Contract\Service;

use App\Module\EventReward\ValueObject\Event;

interface RewardServiceInterface
{
    /**
     * Register a new event and return it with all awards it has achieved
     *
     * @param Event $event
     * @return Event
     */
    public function registerEvent(Event $event): Event;
}
