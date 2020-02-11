<?php

namespace Grr\GrrBundle\Tests\Security;

use DateTime;
use Grr\Core\Tests\BaseTesting;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class AccessEntryControllerTest extends BaseTesting
{
    /**
     * @dataProvider provideCases
     *
     * @throws \Exception
     */
    public function testArea(string $action, string $entryName, array $datas): void
    {
        $this->loadFixtures();
        $token = null;
        $entry = $this->getEntry($entryName);
        $tokenManager = new CsrfTokenManager();
        $token = $tokenManager->getToken('delete'.$entry->getId())->getValue();

        $method = 'GET';
        switch ($action) {
            case 'new':
                $today = new DateTime();
                $esquare = $this->getArea('Esquare');
                $room = $this->getRoom('Box');
                $url = '/front/entry/new/area/'.$esquare->getId().'/room/'.$room->getId().'/year/'.$today->format(
                        'Y'
                    ).'/month/'.$today->format('m').'/day/'.$today->format('d').'/hour/9/minute/30';
                break;
            case 'show':
                $url = '/front/entry/'.$entry->getId();
                break;
            case 'edit':
                $url = '/front/entry/'.$entry->getId().'/edit';
                break;
            case 'delete':
                $url = '/front/entry/'.$entry->getId();
                $method = 'DELETE';
                break;
            default:
                $url = null;
                break;
        }

        foreach ($datas as $data) {
            $email = $data[1];
            $code = $data[0];
            $client = !$email ? static::createClient() : $this->createGrrClient($email);
            $client->request($method, $url, ['_token' => $token]);
            self::assertResponseStatusCodeSame($code, $email.' '.$url);
        }
    }

    public function provideCases(): iterable
    {
        yield [
            'new',
            'Réunion cst',
            [
                [
                    Response::HTTP_FOUND,
                    null,
                ],
                [
                    Response::HTTP_OK,
                    'bob@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'alice@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'raoul@domain.be',
                ],
                [
                    Response::HTTP_FORBIDDEN,
                    'fred@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            'show',
            'Réunion cst',
            [
                [
                    Response::HTTP_OK,
                    null,
                ],
                [
                    Response::HTTP_OK,
                    'bob@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'alice@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'raoul@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'fred@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            'edit',
            'Réunion cst',
            [
                [
                    Response::HTTP_FOUND,
                    null,
                ],
                [
                    Response::HTTP_OK,
                    'bob@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'alice@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'raoul@domain.be',
                ],
                [
                    Response::HTTP_FORBIDDEN,
                    'fred@domain.be',
                ],
                [
                    Response::HTTP_OK,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            'delete',
            'Réunion cst',
            [
                [
                    Response::HTTP_FOUND,
                    null,
                ],
                [
                    Response::HTTP_FOUND,
                    'bob@domain.be',
                ],
                [
                    Response::HTTP_FOUND,
                    'alice@domain.be',
                ],
                [
                    Response::HTTP_FOUND,
                    'raoul@domain.be',
                ],
                [
                    Response::HTTP_FORBIDDEN,
                    'fred@domain.be',
                ],
                [
                    Response::HTTP_FOUND,
                    'grr@domain.be',
                ],
            ],
        ];
    }

    protected function loadFixtures(): void
    {
        $files =
            [
                $this->pathFixtures.'area.yaml',
                $this->pathFixtures.'room.yaml',
                $this->pathFixtures.'user.yaml',
                $this->pathFixtures.'authorization.yaml',
                $this->pathFixtures.'entry_type.yaml',
                $this->pathFixtures.'entry.yaml',
            ];

        $this->loader->load($files);
    }
}
