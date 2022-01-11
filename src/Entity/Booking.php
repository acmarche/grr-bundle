<?php

namespace Grr\GrrBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Doctrine\Traits\IdEntityTrait;
use Grr\GrrBundle\Booking\Repository\BookingRepository;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    use IdEntityTrait;
    #[ORM\Column(type: 'string', length: 120)]
    private $nom;
    #[ORM\Column(type: 'string', length: 120)]
    private $prenom;
    #[ORM\Column(type: 'string', length: 120)]
    private $telephone;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $informations;
    #[ORM\Column(type: 'string', length: 120, nullable: true)]
    private $tva;
    #[ORM\Column(type: 'string', length: 120)]
    private $email;
    #[ORM\Column(type: 'string', length: 120)]
    private $horaire_name;
    #[ORM\Column(type: 'integer', nullable: false)]
    private $horaire_id;
    #[ORM\Column(type: 'date')]
    private $jour;
    #[ORM\Column(type: 'integer')]
    private $room_id;
    #[ORM\Column(type: 'string', length: 120)]
    private $room_name;
    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $done = false;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getJour(): ?DateTimeInterface
    {
        return $this->jour;
    }

    public function setJour(DateTimeInterface $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getRoomId(): ?int
    {
        return $this->room_id;
    }

    public function setRoomId(int $room_id): self
    {
        $this->room_id = $room_id;

        return $this;
    }

    public function getRoomName(): ?string
    {
        return $this->room_name;
    }

    public function setRoomName(string $room_name): self
    {
        $this->room_name = $room_name;

        return $this;
    }

    public function getDone(): bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getHoraireName(): ?string
    {
        return $this->horaire_name;
    }

    public function setHoraireName(string $horaire_name): self
    {
        $this->horaire_name = $horaire_name;

        return $this;
    }

    public function getHoraireId(): ?int
    {
        return $this->horaire_id;
    }

    public function setHoraireId(int $horaire_id): self
    {
        $this->horaire_id = $horaire_id;

        return $this;
    }
}
