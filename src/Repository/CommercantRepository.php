<?php

namespace App\Repository;

use App\Entity\Commercant;
use App\Entity\TypeCommercant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommercantRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Commercant::class);
    }


    public function calculNbType()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT count(commercants.id) as count , commercants.id_type_commercant , type_commercants.*  FROM commercants INNER JOIN type_commercants ON commercants.id_type_commercant = type_commercants.id GROUP BY type_commercants.id ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['price' => 10]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
        ;
    }


    public function nbCommercant(){
        return $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
