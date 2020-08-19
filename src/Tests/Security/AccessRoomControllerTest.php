<?php

namespace Grr\GrrBundle\Tests\Security;

use Grr\Core\Tests\BaseTesting;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class AccessRoomControllerTest extends BaseTesting
{
    /**
     * @dataProvider provideCases
     *
     * @param string $url
     */
    public function testArea(string $action, string $roomName, array $datas): void
    {
        $this->loadFixtures();
        $room = $token = null;
        if ('' !== $roomName) {
            $room = $this->getRoom($roomName);
            $tokenManager = new CsrfTokenManager();
            // bug : session_start(): Cannot start session when headers already sent
            //$token = $tokenManager->getToken('delete'.$room->getId())->getValue();
        }

        $method = 'GET';
        switch ($action) {
            case 'new':
                $area = $this->getArea('Esquare');
                $url = '/fr/admin/room/new/'.$area->getId();
                break;
            case 'show':
                $url = '/fr/admin/room/'.$room->getId();
                break;
            case 'edit':
                $url = '/fr/admin/room/'.$room->getId().'/edit';
                break;
            case 'delete':
                $url = '/fr/admin/room/'.$room->getId();
                $method = 'DELETE';
                break;
            default:
                $url = null;
                break;
        }

        foreach ($datas as $data) {
            $email = $data[1];
            $code = $data[0];
            $client = !$email ? $this->createAnonymousClient() : $this->createGrrClient($email);
            $client->request($method, $url, ['_token' => $token]);
            self::assertResponseStatusCodeSame($code, $email.' '.$url);
        }
    }

    public function provideCases(): iterable
    {
        yield [
            'new',
            'Box',
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
                    Response::HTTP_FORBIDDEN,
                    'alice@domain.be',
                ],
                [
                    Response::HTTP_FORBIDDEN,
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
            'Box',
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
            'edit',
            'Box',
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
                    Response::HTTP_FORBIDDEN,
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
            'Box',
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
                    Response::HTTP_FORBIDDEN,
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
            ];

        $this->loader->load($files);
    }
}
