<?php declare(strict_types=1);

namespace App\Module\EventReward\Service;

use App\Module\EventReward\Contract\Repository\IncentiveProgramRepoInterface;
use App\Module\EventReward\Contract\Repository\UserEventLogRepoInterface;
use App\Module\EventReward\Contract\Service\RewardServiceInterface;
use App\Module\EventReward\ValueObject\Event;

class RewardService implements RewardServiceInterface
{
    private UserEventLogRepoInterface $userEventLogRepo;
    private IncentiveProgramRepoInterface $incentiveRepo;

    public function __construct(
        UserEventLogRepoInterface $userEventLogRepo,
        IncentiveProgramRepoInterface $incentiveRepo
    ) {
        $this->userEventLogRepo = $userEventLogRepo;
        $this->incentiveRepo = $incentiveRepo;
    }

    public function registerEvent(Event $event): Event
    {
        $user = $event->getUser();
        $employer = $user->getEmployer();
        $userEventLog = $this->userEventLogRepo->findForUser($user);
        if ($employer) {
            $incentives = $this->incentiveRepo->findForEmployer($employer);
            foreach ($incentives as $incentive) {
                if ($incentive->getRules()->isEligible($event, $userEventLog)) {
                    $event->addReward($incentive->getReward());
                }
            }
        }

        $userEventLog->addEvent($event);
        $this->userEventLogRepo->store($user, $userEventLog);

        return $event;
    }
}
