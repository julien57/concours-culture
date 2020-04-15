<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function nextConcours()
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateNow->format('Y-m-d H:i');

        return $this->createQueryBuilder('q')
            ->where('q.atDate > :now')
            ->setParameter('now', $dateNow)
            ->orderBy('q.atDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function APINextConcours()
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateNow->format('Y-m-d H:i');

        return $this->createQueryBuilder('q')
            ->where('q.atDate > :now')
            ->setParameter('now', $dateNow)
            ->orderBy('q.atDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();
    }

    public function nextConcoursWithGift()
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateNow->format('Y-m-d H:i');

        return $this->createQueryBuilder('q')
            ->leftJoin('q.gift', 'gift')
            ->addSelect('gift')
            ->where('q.atDate > :now')
            ->setParameter('now', $dateNow)
            ->orderBy('q.atDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function concoursInCurse()
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateNow->format('Y-m-d H:i');

        return $this->createQueryBuilder('q')
            ->where(':now BETWEEN q.atDate AND q.atFinish')
            ->setParameter('now', $dateNow)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function APIConcoursInCurse()
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateNow->format('Y-m-d H:i');

        return $this->createQueryBuilder('q')
            ->where(':now BETWEEN q.atDate AND q.atFinish')
            ->setParameter('now', $dateNow)
            ->getQuery()
            ->getArrayResult();
    }

    public function getByTheme()
    {
        return $this->createQueryBuilder('q')
            ->select('q.theme')
            ->groupBy('q.theme')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function getQuizPassed()
    {
        $dateNow = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateNow->format('Y-m-d H:i');

        return $this->createQueryBuilder('q')
            ->where('q.atFinish < :now')
            ->setParameter('now', $dateNow)
            ->orderBy('q.atDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Quiz[] Returns an array of Quiz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quiz
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
