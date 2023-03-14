<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create:user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create:user';
    protected static $defaultDescription = 'Add a short description for your command';

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $this->em = $em;
        $this->hasher = $hasher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $helper = $this->getHelper('question');

        $questionLogin = new Question('User: ');
        $questionPassword = new Question('Password: ');
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);

        $questionLastName = new Question('Last name: ');
        $questionFirstName = new Question('First name: ');

        $login = $helper->ask($input, $output, $questionLogin);
        $password = $helper->ask($input, $output, $questionPassword);
        $lastName = $helper->ask($input, $output, $questionLastName);
        $firstName = $helper->ask($input, $output, $questionFirstName);
 
        $output->writeln('Username: ' . $login);
        $output->writeln('Password: ' . $password);
        $output->writeln('LastName: ' . $lastName);
        $output->writeln('FirstName: ' . $firstName);

        $users = $this->em->getRepository(User::class)->findAll();
        
        if($users) {
            $output->writeln(count($users) . ' users found');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($login);
        $user->setPassword($password);

        $hash = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('User created');
        return Command::SUCCESS;

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        // return Command::SUCCESS;
    }
}
