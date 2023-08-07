<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerVoter extends Voter
{
    public const BELONGS_TO_ME = 'CUSTOMER_BELONGS_TO_ME';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::BELONGS_TO_ME])
            && $subject instanceof \App\Entity\Customer;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($attribute === self::BELONGS_TO_ME) {
            return $subject->getOwner() === $user;
        }

        return false;
    }
}
