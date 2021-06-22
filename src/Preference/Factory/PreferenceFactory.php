<?php

namespace Grr\GrrBundle\Preference\Factory;

use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\GrrBundle\Entity\Preference\EmailPreference;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;

class PreferenceFactory
{
    private EmailPreferenceRepository $emailPreferenceRepository;

    public function __construct(EmailPreferenceRepository $emailPreferenceRepository)
    {
        $this->emailPreferenceRepository = $emailPreferenceRepository;
    }

    public function createEmailPreferenceByUser(UserInterface $user): EmailPreference
    {
        if (($preference = $this->emailPreferenceRepository->findOneByUser($user)) === null) {
            $preference = new EmailPreference($user);
        }

        return $preference;
    }
}
