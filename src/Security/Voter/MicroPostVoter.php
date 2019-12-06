<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MicroPostVoter extends Voter
{
    const EDIT = 'micro_post_edit';
    const DELETE = 'micro_post_delete';
    const VIEW = 'micro_post_view';
    const ACTION = [self::VIEW, self::EDIT, self::DELETE];

    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, self::ACTION)
            && $subject instanceof \App\Entity\MicroPost;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->accessDecisionManager->decide($token, [User::ROLE_ADMIN])) {
            return true;
        }

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var MicroPost $microPost */
        $microPost = $subject;

        return $user->getUsername() === $microPost->getUser()->getUsername();
    }
}
