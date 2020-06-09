<?php
namespace App\Controller\UserMicroservice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Api\Communication\RequestMaker;
use App\Api\Communication\RequestToSend;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractController
{
	private $baseUrl;
	private $requestMaker;

	public function __construct(RequestMaker $requestMaker)
	{
		$this->baseUrl = $_ENV['USER_BASE_URL'];
		$this->requestMaker = $requestMaker;
	}

	/**
	 * @Route("/profile", name="get_user_current", methods={"GET"})
	 * @IsGranted("ROLE_USER")
	 */
	public function getUserFromMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/users/current';
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setHeaders($headers);
		$requestToSend->setCacheKey('user.current');
		$requestToSend->setCacheUrl($this->getUser()->getId());

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/list", name="get_user_list", methods={"GET"})
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function getUserListFromMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/users';
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setQueryParams($request->query->all());
		$requestToSend->setHeaders($headers);
		$requestToSend->setCacheKey('user.list');

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());;

		return $responseFromApi;
	}

	/**
	 * @Route("/{id}", name="get_user_item", methods={"GET"}, requirements={"id"="\d+"})
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function getUserItemFromMicroservice(Request $request, int $id): Response
	{
		$url = $this->baseUrl.'api/users/'.$id;
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setQueryParams($request->query->all());
		$requestToSend->setHeaders($headers);
		$requestToSend->setCacheKey('user.specific');

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("", name="post_user", methods={"POST"})
	 */
	public function postUserIntoMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/users';

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setBody(json_decode($request->getContent(), true));

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/profile", name="patch_user_current", methods={"PATCH"})
	 * @IsGranted("ROLE_USER")
	 */
	public function patchUserCurrentIntoMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/users/current';
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];
		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setHeaders($headers);
		$requestToSend->setBody(json_decode($request->getContent(), true));

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/profile/password", name="patch_user_current_password", methods={"PATCH"})
	 * @IsGranted("ROLE_USER")
	 */
	public function patchUserCurrentPasswordIntoMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/users/current/password';
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];
		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setHeaders($headers);
		$requestToSend->setBody(json_decode($request->getContent(), true));

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/activate/{token}", name="patch_user_activate", methods={"PATCH"}, requirements={"token"=".+"})
	 */
	public function patchUserActivateIntoMicroservice(Request $request, string $token): Response
	{
		$url = $this->baseUrl.'api/users/activate/'.$token;
		$requestToSend = new RequestToSend($url, $request->getMethod());

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/{id}", name="patch_user_item", methods={"PATCH"}, requirements={"id"="\d+"})
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function patchUserItemIntoMicroservice(Request $request, $id): Response
	{
		$url = $this->baseUrl.'api/users/'.$id;
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];
		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setHeaders($headers);
		$requestToSend->setBody(json_decode($request->getContent(), true));

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}
}