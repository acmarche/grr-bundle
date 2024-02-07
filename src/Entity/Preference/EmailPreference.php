<?php

namespace Grr\GrrBundle\Entity\Preference;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Doctrine\Traits\IdEntityTrait;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;

#[ORM\Entity(repositoryClass: EmailPreferenceRepository::class)]
class EmailPreference
{
    use IdEntityTrait;

    #[ORM\Column(type: 'boolean')]
    private bool $onCreated;
    #[ORM\Column(type: 'boolean')]
    private bool $onUpdated;
    #[ORM\Column(type: 'boolean')]
    private bool $onDeleted;

    public function __construct(
        #[ORM\ManyToOne(UserInterface::class)]
        #[ORM\JoinColumn(nullable: false)]
        private UserInterface $user
    ) {
        $this->onCreated = false;
        $this->onDeleted = false;
        $this->onUpdated = false;
    }

    public function getOnCreated(): bool
    {
        return $this->onCreated;
    }

    public function setOnCreated(bool $onCreated): self
    {
        $this->onCreated = $onCreated;

        return $this;
    }

    public function getOnUpdated(): bool
    {
        return $this->onUpdated;
    }

    public function setOnUpdated(bool $onUpdated): self
    {
        $this->onUpdated = $onUpdated;

        return $this;
    }

    public function getOnDeleted(): bool
    {
        return $this->onDeleted;
    }

    public function setOnDeleted(bool $onDeleted): self
    {
        $this->onDeleted = $onDeleted;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
