<?php declare(strict_types=1);

namespace App\Module\EventReward\Test;

use App\Model\Employer;
use App\Model\User;
use App\Module\EventReward\Model\IncentiveProgram;
use App\Module\EventReward\Model\UserEventLog;
use App\Module\EventReward\Service\Strategy\SingleEventStrategy;
use App\Module\EventReward\ValueObject\Event;
use App\Module\EventReward\ValueObject\Reward;
use DateTime;
use PHPUnit\Framework\TestCase;

class SingleEventStrategyTest extends TestCase
{
    public function simpleEventDataProvider(): iterable
    {
        yield ['fact', '', false];
        yield ['fact', 'married', false];
        yield ['fact', 'gave birth', true];
        yield ['status', 'gave birth', false];
    }

    /**
     * @dataProvider simpleEventDataProvider
     */
    public function testAllCases(string $eventType, string $eventValue, bool $expectedIsEligible): void
    {
        // Given
        $employer = new Employer();
        $user = new User(1, new Employer());
        $incentiveProgram = new IncentiveProgram(
            $employer,
            new Reward('10 oviacoins'),
            new SingleEventStrategy('fact', 'gave birth'),
        );

        // When
        $isEligible = $incentiveProgram->getRules()->isEligible(
            new Event($user, new DateTime('now'), $eventType, $eventValue),
            new UserEventLog([]),
        );

        // Then
        $this->assertEquals($expectedIsEligible, $isEligible);
    }
}
