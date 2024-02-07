<?php

namespace Grr\GrrBundle\Preference\Factory;

use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\GrrBundle\Entity\Preference\EmailPreference;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;

class PreferenceFactory
{
    public function __construct(
        private readonly EmailPreferenceRepository $emailPreferenceRepository
    ) {
    }

    public function createEmailPreferenceByUser(UserInterface $user): EmailPreference
    {
        if (!($preference = $this->emailPreferenceRepository->findOneByUser($user)) instanceof EmailPreference) {
            $preference = new EmailPreference($user);
        }

        return $preference;
    }
}
