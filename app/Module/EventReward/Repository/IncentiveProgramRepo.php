<?php declare(strict_types=1);

namespace App\Module\EventReward\Repository;

use App\Model\Employer;
use App\Module\EventReward\Contract\Repository\IncentiveProgramRepoInterface;
use App\Module\EventReward\Model\IncentiveProgram;

class IncentiveProgramRepo implements IncentiveProgramRepoInterface
{
    /** @var IncentiveProgram[] */
    private array $nonPersistentCache = []; // TODO store onto disk

    /**
     * @inheritDoc
     */
    public function findForEmployer(Employer $employer): array
    {
        $results = [];
        foreach ($this->nonPersistentCache as $incentiveProgram) {
            if ($incentiveProgram->getEmployer() === $employer) {
                $results[] = $incentiveProgram;
            }
        }
        return $results;
    }

    public function store(IncentiveProgram $incentive): void
    {
        $this->nonPersistentCache[] = $incentive;
    }
}
