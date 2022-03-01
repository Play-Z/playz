<?php

namespace App\Command;

use App\Entity\User;
use App\Security\EmailVerifier;
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
use Symfony\Component\Validator\Constraints as Assert;


#[AsCommand(
    name: 'createUser',
    description: 'create a user',
)]
class CreateUserCommand extends Command
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var UserPasswordHasherInterface $userPasswordHasherInterface */
    private $userPasswordHasherInterface;

    public function __construct(string $name = null, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'User email')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password')
            ->addOption('isAdmin', 'a', InputOption::VALUE_NONE, 'User is admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();
        if ($email && $password) {
            if (preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
                $user->setEmail($email);
                if ($input->getOption('isAdmin')) {
                    $user->setRoles(['ROLE_ADMIN']);
                }

                $user->setUsername('username');
                $user->setPassword($this->userPasswordHasherInterface->hashPassword(
                    $user,
                    $password
                ));

                $this->em->persist($user);
                $this->em->flush();
                $io->success('Votre utilisateur ' . $email . ',' . $password . ' a bien été créé');

            } else {
                $io->error('Votre email n\'est pas valide');
            }
        } else {
            $io->error('Veuillez entrer un email et un mot de passe pour votre utilisateur');
        }


        return Command::SUCCESS;
    }
}
