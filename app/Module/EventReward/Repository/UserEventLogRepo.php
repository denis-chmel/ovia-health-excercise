<?php declare(strict_types=1);

namespace App\Module\EventReward\Repository;

use App\Model\User;
use App\Module\EventReward\Contract\Repository\UserEventLogRepoInterface;
use App\Module\EventReward\Model\UserEventLog;

class UserEventLogRepo implements UserEventLogRepoInterface
{
    /** @var UserEventLog[] */
    private array $nonPersistentCache = []; // TODO store onto disk

    public function store(User $user, UserEventLog $eventLog): void
    {
        $this->nonPersistentCache[$user->getId()] = $eventLog;
    }

    public function findForUser(User $user): UserEventLog
    {
        return $this->nonPersistentCache[$user->getId()] ?? new UserEventLog([]);
    }
}
