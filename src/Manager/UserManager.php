<?php


namespace App\Manager;

use App\Entity\User;
use App\Entity\Userprofile;
use App\Repository\UserRepository;
use App\Services\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid as Uuid;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PasswordService
     */
    protected $passwordService;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param PasswordService $passwordService
     * @param UserRepository $userRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordService $passwordService,
        UserRepository $userRepository
    ) 
    {
        $this->em = $entityManager;
        $this->passwordService = $passwordService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            return $user[0];
        }

        return null;
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function findByUuid(string $uuid)
    {
        $user = $this->userRepository->findByUuid($uuid);

        if ($user) {
            return $user[0];
        }

        return null;
    }

    /**
     * REP + ANNEE + MOIS + JOUR + TOKEN GENERER.
     *
     * @return string
     */
    public function referenceFormat()
    {
        return 'REF'.substr(date('Y'), 2).date('md').uniqid();
    }

    /**
     * @param User $user
     * @return array|string
     * @throws \Exception
     */
    public function registerAccount(User $user)
    {
  
        if ($this->findByEmail($user->getEmail())) {
            throw new BadRequestHttpException('This Email is Already Used!');
        }

        $user->setEmail($user->getEmail());
        $pass = $this->passwordService->hashPassowrd($user,$user->getPassword());
        // dd($pass);
        $user->setPassword($pass); 
        $user->setRoles($user->getRoles());
        $uuid = Uuid::v4();
        while($this->findByUuid($uuid))
        {
            $uuid = Uuid::v4();
        }
        $user->setUuid($uuid);
       
        $userprofile = new Userprofile();
        $user->setUserprofile($userprofile);
        $user->getUserprofile()->setReferraltoken($this->referenceFormat());
        $user->getUserprofile()->setKyctoken("empty");

        $this->em->persist($user);
        $this->em->flush();

        return [
            'message' => 'Succeful Registration!',
            'user' => $user
        ];
    }


}