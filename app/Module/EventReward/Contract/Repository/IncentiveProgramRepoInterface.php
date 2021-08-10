<?php declare(strict_types=1);

namespace App\Module\EventReward\Contract\Repository;

use App\Model\Employer;
use App\Module\EventReward\Model\IncentiveProgram;

interface IncentiveProgramRepoInterface
{
    /**
     * @param Employer $employer
     * @return IncentiveProgram[]
     */
    public function findForEmployer(Employer $employer): array;

    public function store(IncentiveProgram $incentive): void;
}
