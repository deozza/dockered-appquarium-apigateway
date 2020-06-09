<?php


namespace App\Entity;


use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{

	private $roles;
	private $id;
	private $username;
	private $token;

	public function __construct(array $dataFromMS, string $token)
	{
		$this->setId($dataFromMS['@id']);
		$this->setRoles($dataFromMS['roles']);
		$this->setUsername($dataFromMS['username']);
		$this->setToken($token);
	}

	public function setId(string $id): self
	{
		$this->id = $id;
		return $this;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;
		return $this;
	}

	public function getRoles(): array
	{
		return $this->roles;
	}

	public function setUsername(string $username): self
	{
		$this->username = $username;
		return $this;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setToken(string $token): self
	{
		$this->token = $token;
		return $this;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	public function eraseCredentials()
	{
	}

	public function getPassword()
	{
	}

	public function getSalt()
	{
	}

}