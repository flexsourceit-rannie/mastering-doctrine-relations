<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * @return \Doctrine\Common\Collections\Criteria
     */
    public static function createApprovedAnswers(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('status', Answer::STATUS_APPROVED));
    }

    /**
     * @param int $max
     *
     * @return Answer[]
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllApproved(int $max = 10): array
    {
        return $this->createQueryBuilder('answer')
                ->addCriteria(self::createApprovedAnswers())
                ->setMaxResults($max)
                ->getQuery()
                ->getResult();
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findMostPopular(?string $query = null): array
    {
        $queryBuilder = $this->createQueryBuilder('answer')
            ->addCriteria(self::createApprovedAnswers())
            ->orderBy('answer.votes', 'DESC')
            ->innerJoin('answer.question', 'question')
            ->addSelect([
                'question'
            ]);

        if ($query) {
            $queryBuilder->andWhere('
                answer.content LIKE :searchTerm OR question.question LIKE :searchTerm
            ')
                ->setParameter('searchTerm', "%$query%");
        }

        return $queryBuilder
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
