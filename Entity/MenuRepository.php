<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PageMenuRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuRepository extends EntityRepository
{
    public function findOneByCountryAndName($country, $name)
    {
        $query = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.name = :name AND m.country = :country')
            ->setParameters(array(
                'name' => $name,
                'country' => $country
            ))
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}
