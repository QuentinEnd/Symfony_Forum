<?php
/**
 * Created by PhpStorm.
 * User: quent
 * Date: 08/09/2017
 * Time: 16:11
 */

namespace AppBundle\Twig;


class ElapsedTimeFilter extends \Twig_Extension
{

    /**
     * @var array
     */
    private $intervalFormat = [
        "y" => "an",
        "m" => "mois",
        "d" => "jour",
        "h" => "heure",
        "i" => "minute",
        "s" => "seconde"
    ];

    /**
     * @return string
     */
    public function getName()
    {
        return "elapsedTimeFilter";
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("elapsed", [$this, "elapsed"])
        ];
    }

    /**
     * @param $date
     * @return string
     */
    public function elapsed($date)
    {
        $now = new \DateTime();

        $interval = $now->diff($date);

        $format = "";
        foreach ($this->intervalFormat as $key => $val) {
            $value = $interval->$key;
            if ($value > 0) {
                $format .= "%{$key} $val ";
            }
        }

        return $interval->format($format);
    }

}