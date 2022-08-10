<?php

namespace NadiaAhoure\Bundle\AfficheCsvBundle\Command;

use DateTime;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use NadiaAhoure\Bundle\AfficheCsvBundle\TableContenuFormat;

class CsvCommand extends Command
{

    protected static $defaultName = 'app:csvcommand';
    protected static $defaultDescription = 'Affiche une grille d\'information à partir de données de type fichier cvs';


    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('file', InputArgument::REQUIRED, 'le lien du fichier csv que vous souhaitez afficher?')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Affiche les informations au format JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $option = $input->getOption('option');
        $io = new SymfonyStyle($input, $output);
        $inputfile =  $file;
        $type = explode(".", $file);

        if (strtolower(end($type)) == 'csv') {
            $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $tabinfos = $decoder->decode(file_get_contents($inputfile), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);

            if (!$option) {
                foreach ($tabinfos as $tabinfo) {
                    $tableFormater = new TableContenuFormat;
                    $tableinfo = $tableFormater->tableContenuFormat($tabinfo);

                    $tableau = array_values($tableinfo);
                    unset($tableau[4]);
                    $tableau_final[] = $tableau;
                }
                $io->title('grille d\'information sous forme de tableau');
                $table = new Table($output);
                $table
                    ->setHeaders(['SKU', 'Slug', 'Status', 'price', 'Description', 'created_at'])
                    ->setRows($tableau_final);


                $table->render();

                $io->newLine();
            }
            if ($option) {
                $encoder = $decoder = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
                $jsoninfo = $encoder->encode($tabinfos, 'json');
                $io->title('grille d\'information au format JSON');
                $io->text($jsoninfo);
                $io->newLine();
            }
        } else {
            $output->writeln(' Vous n\'avez pas choisi un fichier de type csv');
        }


        return 0;
    }
}
