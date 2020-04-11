<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    public function getByUser(User $user)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.quiz', 'quiz')
            ->leftJoin('quiz.results', 'results')
            ->select('quiz.title, quiz.atDate, quiz.theme, quiz.atFinish, quiz.id')
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->groupBy('quiz')
            ->orderBy('quiz.id', 'DESC')
            ;

        return $qb->getQuery()
            ->getResult();
    }

    public function getGoodResultsByQuiz(User $user, Quiz $quiz)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.response', 'response')
            ->where('r.quiz = :quiz')
            ->andWhere('r.user = :user')
            ->setParameter('quiz', $quiz)
            ->setParameter('user', $user)
            ->andWhere('response.isGood = 1')
            ->getQuery()
            ->getResult();
    }

    public function getUserPerQuiz(Quiz $quiz)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'user')
            ->addSelect('user')
            ->leftJoin('r.response', 'response')
            ->where('r.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Result[] Returns an array of Result objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Result
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
