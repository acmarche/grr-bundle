<?php

namespace Grr\GrrBundle\Tests\Security;

use Grr\Core\Tests\BaseTesting;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class AccessAreaControllerTest extends BaseTesting
{
    /**
     * @dataProvider provideCases
     *
     * @param string $url
     */
    public function testArea(string $action, array $datas, ?string $areaName = null): void
    {
        $this->loadFixtures();
        $area = null;
        $token = null;
        if ($areaName) {
            $area = $this->getArea($areaName);
            $tokenManager = new CsrfTokenManager();
            // bug : session_start(): Cannot start session when headers already sent
            // $token = $tokenManager->getToken('delete'.$area->getId())->getValue();
        }

        $method = 'GET';
        switch ($action) {
            case 'index':
                $url = '/fr/admin/area/';
                break;
            case 'new':
                $url = '/fr/admin/area/new';
                break;
            case 'show':
                $url = '/fr/admin/area/'.$area->getId();
                break;
            case 'edit':
                $url = '/fr/admin/area/'.$area->getId().'/edit';
                break;
            case 'delete':
                $url = '/fr/admin/area/'.$area->getId();
                $method = 'DELETE';
                break;
            default:
                $url = null;
                break;
        }

        foreach ($datas as $data) {
            $email = $data[1];
            $code = $data[0];
            $client = $email ? $this->createGrrClient($email) : $this->createAnonymousClient();
            $client->request($method, $url, [
                '_token' => $token,
            ]);
            self::assertResponseStatusCodeSame($code, $email.' '.$url);
        }
    }

    public function provideCases(): iterable
    {
        yield [
            'index',
            null,
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
            'new',
            'Esquare',
            [
                [
                    Response::HTTP_FOUND,
                    null,
                ],
                [
                    Response::HTTP_FORBIDDEN,
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
            'Esquare',
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
            'edit',
            'Esquare',
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
            'delete',
            'Esquare',
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
                    Response::HTTP_FORBIDDEN,
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
