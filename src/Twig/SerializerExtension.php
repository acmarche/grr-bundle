<?php

namespace Grr\GrrBundle\Twig;

use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SerializerExtension extends AbstractExtension
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('jsonld', fn ($data): string => $this->serializeToJsonLd($data), [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function serializeToJsonLd($data): string
    {
        return $this->serializer->serialize($data, 'jsonld');
    }
}
