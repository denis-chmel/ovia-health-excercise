<?php declare(strict_types=1);

namespace App\Module\EventReward\Contract\Repository;

use App\Model\User;
use App\Module\EventReward\Model\UserEventLog;

interface UserEventLogRepoInterface
{
    public function store(User $user, UserEventLog $eventLog): void;

    public function findForUser(User $user): UserEventLog;
}
