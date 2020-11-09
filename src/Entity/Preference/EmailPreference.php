<?php

namespace Grr\GrrBundle\Entity\Preference;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Doctrine\Traits\IdEntityTrait;
use Grr\GrrBundle\Entity\Security\User;

/**
 * Class NotificationEmailPreference.
 *
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository")
 */
class EmailPreference
{
    use IdEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Grr\Core\Contrat\Entity\Security\UserInterface")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var UserInterface
     */
    private $user;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $onCreated;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $onUpdated;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $onDeleted;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
        $this->onCreated = false;
        $this->onDeleted = false;
        $this->onUpdated = false;
    }

    public function getOnCreated(): ?bool
    {
        return $this->onCreated;
    }

    public function setOnCreated(bool $onCreated): self
    {
        $this->onCreated = $onCreated;

        return $this;
    }

    public function getOnUpdated(): ?bool
    {
        return $this->onUpdated;
    }

    public function setOnUpdated(bool $onUpdated): self
    {
        $this->onUpdated = $onUpdated;

        return $this;
    }

    public function getOnDeleted(): ?bool
    {
        return $this->onDeleted;
    }

    public function setOnDeleted(bool $onDeleted): self
    {
        $this->onDeleted = $onDeleted;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
