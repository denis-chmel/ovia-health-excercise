<?php declare(strict_types=1);

namespace App\Model;

class User extends BaseRelationalModel
{
    private int $id;
    private ?Employer $employer;

    public function __construct(int $id, ?Employer $employer = null)
    {
        $this->setId($id);
        $this->setEmployer($employer);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmployer(): ?Employer
    {
        return $this->employer;
    }

    public function setEmployer(?Employer $employer): void
    {
        $this->employer = $employer;
    }
}
