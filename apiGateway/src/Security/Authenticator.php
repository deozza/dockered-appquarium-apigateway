<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

use App\Entity\User;

class Authenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $client;
    private $baseUrl;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
		$this->client = HttpClient::create();

		$this->baseUrl = $_ENV['USER_BASE_URL'];
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization')
            && 0 === strpos($request->headers->get('Authorization'), 'Bearer ');
    }

    public function getCredentials(Request $request)
    {
        $header = $request->headers->get('Authorization');
        return substr($header, 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
		$responseFromMS = $this->client->request("GET", $this->baseUrl.'api/users/current', [
			'headers'=>[
				'Authorization'=>'Bearer '.$credentials
			]
		]);

		if($responseFromMS->getStatusCode() !== 200)
		{
			throw new CustomUserMessageAuthenticationException("Invalid token");
		}

        return new User(json_decode($responseFromMS->getContent(), true), $credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            "message" => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
