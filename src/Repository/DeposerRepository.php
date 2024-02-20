<?php

namespace App\Repository;

use App\Entity\Deposer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Deposer>
 *
 * @method Deposer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deposer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deposer[]    findAll()
 * @method Deposer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeposerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deposer::class);
    }

    public function getAllJSON() {
        $categorias = $this->getDoctrine()
            ->getRepository('AppBundle:Categoria')
            ->findAll();

        $response = new JsonResponse();
        $response->setData($categorias);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

//    /**
//     * @return Deposer[] Returns an array of Deposer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Deposer
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
