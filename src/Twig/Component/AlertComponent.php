<?php

namespace Grr\GrrBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert', template: '@grr_front/components/alert.html.twig')]
class AlertComponent
{
    public string $type = 'success';

    public string $message;

    public function __construct()
    {
    }

    public function getIconClass(): string
    {
        return match ($this->type) {
            'success' => 'fa fa-circle-check',
            'danger' => 'fa fa-circle-exclamation',
        };
    }

    public function getPackageCount(): int
    {
        return 5;
    }
}