<?php
namespace App\Controller\SpeciesMicroservice;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Api\Communication\RequestToSend;
use App\Api\Communication\RequestMaker;

/**
 * @Route("/api/species")
 */
class SpeciesController extends AbstractController
{
	private $baseUrl;
	private $requestMaker;

	public function __construct(RequestMaker $requestMaker)
	{
		$this->baseUrl = $_ENV['SPECIES_BASE_URL'];
		$this->requestMaker = $requestMaker;
	}

	/**
	 * @Route("/list", name="get_species_list", methods={"GET"})
	 */
	public function getSpeciesListFromSpeciesMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/species';
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setQueryParams($request->query->all());
		$requestToSend->setHeaders($headers);

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/{id}", name="get_species_item", methods={"GET"}, requirements={"id"="\d+"})
	 */
	public function getSpeciesItemFromSpeciesMicroservice(Request $request, int $id): Response
	{
		$url = $this->baseUrl.'api/species/'.$id;
		$headers = [
			'Authorization'=>$request->headers->get('Authorization')
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setHeaders($headers);

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("", name="post_species", methods={"POST"})
	 * @IsGranted("SPECIES_POST", subject="request")
	 */
	public function postSpeciesItemIntoSpeciesMicroservice(Request $request): Response
	{
		$url = $this->baseUrl.'api/species';
		$headers = [
			'Authorization'=>$request->headers->get('Authorization'),
			'Content-Type' => 'application/ld+json',
			'Accept'      => 'application/ld+json'
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setBody(json_decode($request->getContent(), true));
		$requestToSend->setHeaders($headers);

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/{encodedLink}", name="patch_species_component", methods={"PATCH"})
	 * @IsGranted("SPECIES_POST", subject="request")
	 */
	public function patchSpeciesComponentIntoSpeciesMicroservice(Request $request, string $encodedLink): Response
	{
		$decodedLink = base64_decode($encodedLink);

		$url = $this->baseUrl.substr($decodedLink, 1);
		$headers = [
			'Authorization'=>$request->headers->get('Authorization'),
			'Content-Type' => 'application/json',
			'Accept'      => 'application/json'
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setBody(json_decode($request->getContent(), true));
		$requestToSend->setHeaders($headers);

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}

	/**
	 * @Route("/{component}", name="post_species_component", methods={"POST"})
	 * @IsGranted("SPECIES_POST", subject="request")
	 */
	public function postSpeciesComponentIntoSpeciesMicroservice(Request $request, string $component): Response
	{
		$url = $this->baseUrl.'api/'.$component;
		$headers = [
			'Authorization'=>$request->headers->get('Authorization'),
			'Content-Type' => 'application/ld+json',
			'Accept'      => 'application/ld+json'
		];

		$requestToSend = new RequestToSend($url, $request->getMethod());
		$requestToSend->setBody(json_decode($request->getContent(), true));
		$requestToSend->setHeaders($headers);

		$responseFromApi = $this->requestMaker->sendRequest($requestToSend, $this->getUser());

		return $responseFromApi;
	}
}