<?php declare(strict_types=1);

namespace App\Module\EventReward\ValueObject;

use App\Model\User;
use DateTimeInterface;

class Event {
    private User $user;
    private DateTimeInterface $dateTime;
    private string $type;
    private string $value;
    /** @var Reward[] */
    private array $rewards;

    /**
     * @var User $user
     * @param DateTimeInterface $dateTime
     * @param string $eventType
     * @param string $eventLabel
     * @param Reward[] $rewards
     */
    public function __construct(
        User $user,
        DateTimeInterface $dateTime,
        string $eventType,
        string $eventLabel,
        array $rewards = []
    ) {
        $this->setUser($user);
        $this->setDateTime($dateTime);
        $this->setType($eventType);
        $this->setValue($eventLabel);
        $this->setRewards($rewards);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTimeInterface $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRewards(): array
    {
        return $this->rewards;
    }

    public function setRewards(array $rewards): void
    {
        $this->rewards = $rewards;
    }

    public function addReward(Reward $reward): void
    {
        $this->rewards[] = $reward;
    }

    /**
     * @return string[]
     */
    public function getRewardLabels(): array
    {
        $labels = [];
        foreach ($this->getRewards() as $reward) {
            $labels[] = $reward->getLabel();
        }
        sort($labels);
        return $labels;
    }
}
