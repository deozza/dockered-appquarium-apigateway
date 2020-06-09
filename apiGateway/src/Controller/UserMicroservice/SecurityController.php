<?php
namespace App\Controller\UserMicroservice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Api\Communication\RequestToSend;
use App\Api\Communication\RequestMaker;

class SecurityController extends AbstractController
{
	private $baseUrl;
	private $requestMaker;

	public function __construct(RequestMaker $requestMaker)
	{
		$this->baseUrl = $_ENV['USER_BASE_URL'];
		$this->requestMaker = $requestMaker;
	}

	/**
	 * @Route("/api/token", name="post_token", methods={"POST"})
	 */
	public function loginFromUserMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/token';

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setBody(json_decode($request->getContent(), true));

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}
}