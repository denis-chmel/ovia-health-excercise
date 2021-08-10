<?php declare(strict_types=1);

namespace App\Module\EventReward\Test;

use App\Model\Employer;
use App\Model\User;
use App\Module\EventReward\Model\IncentiveProgram;
use App\Module\EventReward\Model\UserEventLog;
use App\Module\EventReward\Service\Strategy\EventStructureStrategy;
use App\Module\EventReward\ValueObject\Event;
use App\Module\EventReward\ValueObject\Reward;
use PHPUnit\Framework\TestCase;

class EventStructureStrategyTest extends TestCase
{
    private ?User $currentUser = null;

    public function eventStructureDataProvider(): iterable
    {
        $user = $this->getCurrentUser();

        yield [
            'Reward 3 days in a row, and 3 days in a row it happens, must be eligible',
            'mood', 'perfect', 3,
            [
                new Event($user, new \DateTime('-2 day'), 'mood', 'perfect'),
                new Event($user, new \DateTime('-1 day'), 'mood', 'perfect'),
            ], // 2 last days it was logged
            new Event($user, new \DateTime('now'), 'mood', 'perfect'), // and one more time today
            true, // eligible
        ];

        yield [
            'Reward 3 days in a row, logged 3 days, but with a gap 1 day, must NOT be eligible',
            'mood', 'perfect', 3, // Reward if 3 days in a row
            [ // But there was a 1 day gap
                new Event($user, new \DateTime('-3 day'), 'mood', 'perfect'),
                new Event($user, new \DateTime('-1 day'), 'mood', 'perfect'),
                new Event($user, new \DateTime('-1 day'), 'mood', 'perfect'),
            ],
            new Event($user, new \DateTime('now'), 'mood', 'perfect'), // and one more today
            false, // not eligible
        ];

        yield [
            'Reward if 3 days in a row, two events are not eligible, must NOT be ineligible',
            'mood', 'perfect', 3, // Reward if mood perfect 3 days in a row
            [ // But there was a 1 day gap
                new Event($user, new \DateTime('-2 day'), 'health', 'perfect'), // not mood
                new Event($user, new \DateTime('-1 day'), 'mood', 'good'), // not perfect
                new Event($user, new \DateTime('-1 day'), 'mood', 'perfect'),
            ],
            new Event($user, new \DateTime('now'), 'mood', 'perfect'), // and one more today
            false, // not eligible
        ];

        yield [
            'Reward 3 days in a row, and 3 days in a row it happens, logged with 1 day delay, must be eligible',
            'mood', 'perfect', 3,
            [
                new Event($user, new \DateTime('-3 day'), 'mood', 'perfect'),
                new Event($user, new \DateTime('-2 day'), 'mood', 'perfect'),
            ], // 2 last days it was logged
            new Event($user, new \DateTime('-1 day'), 'mood', 'perfect'), // and one more time today
            true, // eligible
        ];
    }

    /**
     * @dataProvider eventStructureDataProvider
     * @param string $explanation
     * @param string $eventType
     * @param string $eventValue
     * @param int $daysInARow
     * @param Event[] $pastEvents
     * @param Event $currentEvent
     * @param bool $expectedIsEligible
     */
    public function testAllCases(
        string $explanation,
        string $eventType,
        string $eventValue,
        int $daysInARow,
        array $pastEvents,
        Event $currentEvent,
        bool $expectedIsEligible
    ): void {
        // Given
        $user = $this->getCurrentUser();
        $pastEvents = new UserEventLog($pastEvents);
        $incentiveProgram = new IncentiveProgram(
            $user->getEmployer(),
            new Reward('10 oviacoins'),
            new EventStructureStrategy($eventType, $eventValue, $daysInARow),
        );

        // When
        $isEligible = $incentiveProgram->getRules()->isEligible($currentEvent, $pastEvents);

        // Then
        $this->assertEquals($expectedIsEligible, $isEligible, $explanation);
    }

    private function getCurrentUser(): User
    {
        if (!$this->currentUser) {
            $this->currentUser = new User(1, new Employer());
        }
        return $this->currentUser;
    }
}
