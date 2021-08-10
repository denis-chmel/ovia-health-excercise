<?php declare(strict_types=1);

namespace App\Module\EventReward\Test;

use App\Model\Employer;
use App\Model\User;
use App\Module\EventReward\Model\IncentiveProgram;
use App\Module\EventReward\Model\UserEventLog;
use App\Module\EventReward\Repository\IncentiveProgramRepo;
use App\Module\EventReward\Repository\UserEventLogRepo;
use App\Module\EventReward\Service\RewardService;
use App\Module\EventReward\Service\Strategy\EventStructureStrategy;
use App\Module\EventReward\Service\Strategy\SingleEventStrategy;
use App\Module\EventReward\ValueObject\Event;
use App\Module\EventReward\ValueObject\Reward;
use DateTime;
use PHPUnit\Framework\TestCase;

class RewardServiceTest extends TestCase
{
    private ?User $currentUser = null;

    // TODO add many simple cases here

    public function testOneEventAchievesAwardsFromDifferentIncentives(): void
    {
        // Given
        $incentiveRepo = new IncentiveProgramRepo();
        $userEventLogRepo = new UserEventLogRepo();

        $user = $this->getCurrentUser();
        $pastEvents = new UserEventLog([
            new Event($user, new DateTime('3 days ago'), 'mood', 'perfect'),
            new Event($user, new DateTime('2 days ago'), 'mood', 'perfect'),
            new Event($user, new DateTime('1 day ago'), 'mood', 'perfect'),
        ]);
        $userEventLogRepo->store($user, $pastEvents);

        $incentiveRepo->store(
            new IncentiveProgram(
                $user->getEmployer(),
                new Reward('50 oviacoins'),
                new EventStructureStrategy('mood', 'perfect', 5), // 5 days in a row
            ),
        );

        $incentiveRepo->store(
            new IncentiveProgram(
                $user->getEmployer(),
                new Reward('30 oviacoins'),
                new EventStructureStrategy('mood', 'perfect', 3), // 3 days in a row
            ),
        );

        $incentiveRepo->store(
            new IncentiveProgram(
                $user->getEmployer(),
                new Reward('20 oviacoins'),
                new EventStructureStrategy('mood', 'perfect', 2), // 2 days in a row
            ),
        );

        $incentiveRepo->store(
            new IncentiveProgram(
                $user->getEmployer(),
                new Reward('10 oviacoins'),
                new SingleEventStrategy('mood', 'perfect'),
            ),
        );

        // When
        $service = new RewardService($userEventLogRepo, $incentiveRepo);
        $event = $service->registerEvent(
            new Event($user, new DateTime('now'), 'mood', 'perfect'),
        );

        // Then
        $this->assertEquals(
            [
                '10 oviacoins',
                '20 oviacoins',
                '30 oviacoins',
                // not '50 oviacoins' as that one is not eligible by the rules
            ],
            $event->getRewardLabels(),
        );
    }

    // TODO add many complicated and edge-cases here

    private function getCurrentUser(): User
    {
        if (!$this->currentUser) {
            $this->currentUser = new User(1, new Employer());
        }
        return $this->currentUser;
    }
}
