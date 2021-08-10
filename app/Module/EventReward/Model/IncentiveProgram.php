<?php declare(strict_types=1);

namespace App\Module\EventReward\Model;

use App\Model\BaseRelationalModel;
use App\Model\Employer;
use App\Module\EventReward\Contract\Service\Strategy\RewardStrategyInterface;
use App\Module\EventReward\Exception\NotSupportedTypeException;
use App\Module\EventReward\ValueObject\Reward;

class IncentiveProgram extends BaseRelationalModel
{
    private Employer $employer;
    private Reward $reward;
    private RewardStrategyInterface $rules;

    public function __construct(Employer $employer, Reward $reward, RewardStrategyInterface $rules)
    {
        $this->setEmployer($employer);
        $this->setReward($reward);
        $this->setRules($rules);
    }

    public function getEmployer(): Employer
    {
        return $this->employer;
    }

    public function setEmployer(Employer $employer): void
    {
        $this->employer = $employer;
    }

    public function getRules(): RewardStrategyInterface
    {
        return $this->rules;
    }

    public function setRules(RewardStrategyInterface $rules): void
    {
        $this->rules = $rules;
    }

    public function getReward(): Reward
    {
        return $this->reward;
    }

    public function setReward(Reward $reward): void
    {
        $this->reward = $reward;
    }
}
