<?php


namespace App\Voter\Species;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class HasCorrespondingRoleVoter extends Voter
{
	private $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	protected function supports(string $attribute, $subject)
	{
		return in_array($attribute, ['SPECIES_POST']);
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
	{
		if($this->security->isGranted('ROLE_ADMIN')) return true;
		switch(json_decode($subject->getContent(), true)['kind'])
		{
			case 'fish': if(!$this->security->isGranted('ROLE_FISH_EDITOR')) return false;
				break;
			case 'plant': if(!$this->security->isGranted('ROLE_PLANT_EDITOR')) return false;
				break;
			case 'invertebrate': if(!$this->security->isGranted('ROLE_INVERTEBRATE_EDITOR')) return false;
				break;
		}

		return true;
	}
}