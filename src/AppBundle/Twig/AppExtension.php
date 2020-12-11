<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 11.12.2020
 *
 * @package estateblock20
 */

namespace App\AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {

        return [
            new TwigFilter('cast_to_array', [$this, 'objectFilter']),
        ];
    }

    /**
     * Casting object to an array
     * @param $stdClassObject
     * @return array|null
     */
    public function objectFilter($stdClassObject): ?array
    {
        return (array)$stdClassObject;
    }
}