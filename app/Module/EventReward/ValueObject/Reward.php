<?php declare(strict_types=1);

namespace App\Module\EventReward\ValueObject;

class Reward
{
    private string $label;
    /**
     * @var string[]
     */
    private array $payload;

    public function __construct(string $label, array $payload = [])
    {
        $this->setLabel($label);
        $this->setPayload($payload);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }
}
