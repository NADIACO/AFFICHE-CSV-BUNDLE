<?php

namespace NadiaAhoure\Bundle\AfficheCsvBundle\Command;

use DateTime;
use Monolog\Formatter\HtmlFormatter;
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

class CsvCommand extends Command
{

    protected static $defaultName = 'app:csvcommand';
    protected static $defaultDescription = 'Affiche une grille d\'information à partir de données de type fichier cvs';


    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::REQUIRED, 'le lien du fichier csv que vous souhaitez afficher?')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Affiche les informations au format JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $arg1 = $input->getArgument('arg1');
        $option = $input->getOption('option');
        $io = new SymfonyStyle($input, $output);
        $inputfile =  $arg1;
        if ($arg1) {
            $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $tabinfos = $decoder->decode(file_get_contents($inputfile), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
            $info = $tabinfos;

            /*------------------------*/
            function slugify($string, $delimiter = '-')
            {
                $oldLocale = setlocale(LC_ALL, '0');
                setlocale(LC_ALL, 'en_US.UTF-8');
                $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
                $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
                $clean = strtolower($clean);
                $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
                $clean = trim($clean, $delimiter);
                setlocale(LC_ALL, $oldLocale);
                return $clean;
            }


            /*---------------------------------------*/
            // dd($tab2);
            if (!$option) {
                foreach ($tabinfos as $tabinfo) {
                    foreach ($tabinfo as $key => $values) {
                        if ($key == "is_enabled" and $values == 1) {
                            $tabinfo['is_enabled'] = "enabled";
                        } else if ($key == "is_enabled" and $values == 0) {
                            $tabinfo['is_enabled'] = "desabled";
                        } else if ($key == "price") {
                            $tabinfo["price"] = number_format($values, 2, ',', ' ');
                            $tabinfo['price'] .=  $tabinfo["currency"];
                        } else if ($key == "created_at") {
                            $date = new DateTime($values);
                            $tabinfo['created_at'] = $date->format('l d-M-y H:i:s T');
                        } else if ($key == "title") {
                            $tabinfo['title'] = slugify($values);
                        }
                    }
                    $tab = array_values($tabinfo);
                    unset($tab[4]);
                    $tab2[] = $tab;
                }
                $io->title('grille d\'information sous forme de tableau');
                $table = new Table($output);
                $table
                    ->setHeaders(['SKU', 'Slug', 'Status', 'price', 'Description', 'created_at'])
                    ->setRows($tab2);
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
        }


        return 0;
    }
}
