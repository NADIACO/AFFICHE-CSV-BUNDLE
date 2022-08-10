<?php

namespace NadiaAhoure\Bundle\AfficheCsvBundle;

use DateTime;
use NadiaAhoure\Bundle\AfficheCsvBundle\Slugify;

class TableContenuFormat
{
    public $tabinfo;

    public function tableContenuFormat($tabinfo)
    {

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
                $slug = new slugify();
                $tabinfo['title'] = $slug->slugify($values);
            } else if ($key == "description") {
                $tabinfo['description'] = str_replace(['\r\n', '\r', '\n', '<br/>', '<br>'], "\n", $values);
            }
        }
        return $tabinfo;
    }
}
