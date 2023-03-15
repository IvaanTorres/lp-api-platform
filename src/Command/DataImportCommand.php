<?php

namespace App\Command;

use App\Entity\Ability;
use App\Entity\Pokemon;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:data:import',
    description: 'Add a short description for your command',
)]
class DataImportCommand extends Command
{
    private $client;
    private EntityManagerInterface $em;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->client = $client;
        $this->em = $entityManager;
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

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $allPokemonResponse = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/pokemon?limit=50&offset=0'
        );

        $allPokemonArray = $allPokemonResponse->toArray()['results'];

        foreach ($allPokemonArray as $pokemon) {
            $currPokemonResponse = $this->client->request(
                'GET',
                $pokemon['url']
            );
            $currPokemon = $currPokemonResponse->toArray();

            $newPokemon = new Pokemon();

            foreach ($currPokemon['abilities'] as $ability) {
                $currAbilityResponse = $this->client->request(
                    'GET',
                    $ability['ability']['url']
                );
                $currAbility = $currAbilityResponse->toArray();
                $abilityInDb = $this->em->getRepository(Ability::class)->findOneBy(['name' => $currAbility['name']]);
                if(!$abilityInDb){
                    $newAbility = new Ability();
                    $newAbility->setName($currAbility['name']);
                    $newAbility->setDescription($currAbility['effect_entries'][1]['effect']);
                    if(isset($currAbility['effect_changes']) && isset($currAbility['effect_changes'][0]) && isset($currAbility['effect_changes'][0]['effect_entries']) && isset($currAbility['effect_changes'][0]['effect_entries'][1])){
                        $newAbility->setEffectChangeDescription($currAbility['effect_changes'][0]['effect_entries'][1]['effect']);
                    }
                    $newPokemon->addAbility($newAbility);
                }
            }

            foreach ($currPokemon['types'] as $type) {
                $typeInDb = $this->em->getRepository(Type::class)->findOneBy(['name' => $type['type']['name']]);
                if(!$typeInDb){
                    $newType = new Type();
                    $newType->setName($type['type']['name']);
                    $newPokemon->addType($newType);
                }
            }

            $newPokemon->setName($currPokemon['name']);
            $newPokemon->setHeight($currPokemon['height']);
            $newPokemon->setWeight($currPokemon['weight']);
            $newPokemon->setPokemonOrder($currPokemon['order']);
            $newPokemon->setImagePath($currPokemon['sprites']['front_default']);

            // dump($currPokemon['weight']);
            $this->em->persist($newPokemon);
            $this->em->flush();
        }

        return Command::SUCCESS;
    }
}
