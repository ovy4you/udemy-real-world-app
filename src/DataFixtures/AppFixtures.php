<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    private const USERS = [
        [
            'username' => 'john_doe',
            'email' => 'john_doe@doe.com',
            'password' => 'john123',
            'fullName' => 'John Doe',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob_smith@smith.com',
            'password' => 'rob12345',
            'fullName' => 'Rob Smith',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'marry_gold',
            'email' => 'marry_gold@gold.com',
            'password' => 'marry12345',
            'fullName' => 'Marry Gold',
            'roles' => [User::ROLE_ADMIN]
        ],
    ];

    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {

        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicropost($manager);
    }

    private function loadMicropost(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $micropost = new MicroPost();
            $micropost->setText('Some random text' . rand(1, 500))
                ->setTime(new \DateTime('2018-10-10'))->setUser($this->getReference(array_column(self::USERS, 'username')[rand(0, 2)]));
            $manager->persist($micropost);
        }
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setEmail($userFixture['email'])
                ->setUsername($userFixture['username'])
                ->setFullName($userFixture['fullName'])
                ->setPassword($this->userPasswordEncoder->encodePassword($user, $userFixture['password']))
                ->setRoles($userFixture['roles']);

            $this->addReference($userFixture['username'], $user);
            $manager->persist($user);
        }
        $manager->flush();
    }

}
