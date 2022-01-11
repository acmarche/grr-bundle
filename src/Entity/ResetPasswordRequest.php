<?php

namespace Grr\GrrBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Doctrine\Traits\IdEntityTrait;
use Grr\GrrBundle\Password\Repository\ResetPasswordRequestRepository;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;
    use IdEntityTrait;

    #[ORM\ManyToOne(UserInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private UserInterface $user;

    public function __construct(
        UserInterface $user,
        DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken
    ) {
        $this->user = $user;
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getUser(): object
    {
        return $this->user;
    }
}
