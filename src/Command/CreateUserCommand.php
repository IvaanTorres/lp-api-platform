<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:user:create';
    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Creates the first user (admin). If a user already exists in database, nothing happens';

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->em = $entityManager;
        $this->hasher = $hasher;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $questionLogin = new Question("username ? ");
        $questionPassword = new Question("password ? ");
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);

        $questionLastName = new Question("last name ? ");
        $questionFirstName = new Question("first name ? ");

        $login = $helper->ask($input, $output, $questionLogin);
        $password = $helper->ask($input, $output, $questionPassword);

        $output->writeln('Username: ' . $login);
        $output->writeln('Password: ' . $password);

        // No user must be in database
        // $users = $this->em->getRepository(User::class)->findAll();
        // if ($users) {
        //     $output->writeln(count($users) . ' user(s) in DB. No creation allowed');
        //     return Command::FAILURE;
        // }

        $user = new User();
        $user->setEmail($login);
        $user->setPassword($password);

        $hash = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);


        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Success !');
        return Command::SUCCESS;
    }
}