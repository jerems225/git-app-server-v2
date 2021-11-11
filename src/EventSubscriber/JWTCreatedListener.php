<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener 
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user instanceof User) {
            $data['data'] = array(
                'uuid'        => $user->getUuid(),
                'username'  => $user->getUserIdentifier(),
                'email'     => $user->getEmail(),
                'roles'     => $user->getRoles(),
                // 'password'  => $user->getPassword(),
                'userprofile_id' => $user->getUserprofile()->getId()
            );
        }

        $event->setData($data);
    }

}
