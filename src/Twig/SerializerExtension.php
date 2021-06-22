<?php

namespace Grr\GrrBundle\Twig;

use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SerializerExtension extends AbstractExtension
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('jsonld', function ($data): string {
                return $this->serializeToJsonLd($data);
            }, ['is_safe' => ['html']]),
        ];
    }

    public function serializeToJsonLd($data): string
    {
        return $this->serializer->serialize($data, 'jsonld');
    }
}
