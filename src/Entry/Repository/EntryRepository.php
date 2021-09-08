<?php

namespace Grr\GrrBundle\Entry\Repository;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\OrmCrudInterface;
use Grr\Core\Doctrine\OrmCrudTrait;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Webmozart\Assert\Assert;

/**
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryRepository extends ServiceEntityRepository implements EntryRepositoryInterface, OrmCrudInterface
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Entry::class);
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('entry')
            ->leftJoin('entry.periodicity', 'periodicity', 'WITH')
            ->leftJoin('entry.room', 'room', 'WITH')
            ->leftJoin('entry.type', 'type', 'WITH')
            ->addSelect('room', 'type');
    }

    /**
     * @return Entry[] Returns an array of Entry objects
     */
    public function findForMonth(
        DateTimeInterface $firstDayOfMonth,
        ?AreaInterface $area,
        RoomInterface $room = null
    ): array {
        $endDayOfMonth = clone $firstDayOfMonth;
        $endDayOfMonth->modify('last day of this month');

        $qb = $this->createQBL()
            ->andWhere('DATE(entry.startTime) >= :begin AND DATE(entry.endTime) <= :end')
            ->setParameter('begin', $firstDayOfMonth->format('Y-m-d'))
            ->setParameter('end', $endDayOfMonth->format('Y-m-d'));

        if (null !== $room) {
            $qb->andWhere('entry.room = :room')
                ->setParameter('room', $room);
        } elseif($area) {
            $rooms = $this->getRooms($area);
            $qb->andWhere('entry.room IN (:rooms)')
                ->setParameter('rooms', $rooms);
        }

        return $qb
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Entry[]
     */
    public function findForDay(CarbonInterface $carbon, RoomInterface $room): array
    {
        return $this->createQBL()
            ->andWhere('DATE(entry.startTime) <= :date AND DATE(entry.endTime) >= :date')
            ->setParameter('date', $carbon->format('Y-m-d'))
            ->andWhere('entry.room = :room')
            ->setParameter('room', $room)
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Entry[]
     */
    public function isBusy(EntryInterface $entry, RoomInterface $room): array
    {
        $begin = $entry->getStartTime();
        $end = $entry->getEndTime();

        $queryBuilder = $this->createQBL()
            ->andWhere('entry.startTime BETWEEN :begin AND :end')
            ->setParameter('begin', $begin->format('Y-m-d H:i'))
            ->setParameter('end', $end->format('Y-m-d H:i'))
            ->orWhere('entry.endTime BETWEEN :begin1 AND :end1')
            ->setParameter('begin1', $begin->format('Y-m-d H:i'))
            ->setParameter('end1', $end->format('Y-m-d H:i'))
            ->andWhere('entry.room = :room')
            ->setParameter('room', $room);

        /**
         * en cas de modif
         */
        if (null !== $entry->getId()) {
            $queryBuilder->andWhere('entry.id != :id')
                ->setParameter('id', $entry->getId());
        }

        return $queryBuilder
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Entry[] Returns an array of Entry objects
     */
    public function search(array $args = []): array
    {
        $name = $args['name'] ?? null;
        $area = $args['area'] ?? null;
        $room = $args['room'] ?? null;
        $typeEntry = $args['entry_type'] ?? null;
        $type = $args['type'] ?? null;

        $queryBuilder = $this->createQBL();

        if ($name) {
            $queryBuilder->andWhere('entry.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if ($area instanceof Area) {
            $rooms = $this->getRooms($area);
            $queryBuilder->andWhere('entry.room IN (:rooms)')
                ->setParameter('rooms', $rooms);
        }

        if ($room instanceof Room) {
            $queryBuilder->andWhere('entry.room = :room')
                ->setParameter('room', $room);
        }

        if ($typeEntry) {
            $queryBuilder->andWhere('entry.entryType = :entryType')
                ->setParameter('entryType', $typeEntry);
        }

        if ($type) {
            $queryBuilder->andWhere('entry.type = :type')
                ->setParameter('type', $type);
        }

        return $queryBuilder
            ->orderBy('entry.startTime', 'DESC')
            ->setMaxResults(500)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Entry[]
     */
    public function withPeriodicity(): array
    {
        return $this->createQBL()
            ->andWhere('entry.periodicity IS NOT NULL')
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Entry[]
     */
    public function findByPeriodicity(PeriodicityInterface $periodicity): array
    {
        return $this->createQBL()
            ->andWhere('entry.periodicity = :periodicity')
            ->setParameter('periodicity', $periodicity)
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Ajouter pour sqlLite.
     *
     * @return Room[]|iterable
     */
    private function getRooms(Area $area): array
    {
        $roomRepository = $this->getEntityManager()->getRepository(Room::class);

        return $roomRepository->findByArea($area);
    }

    /**
     * Retourne l'entry de base de la repetition.
     * @throws NonUniqueResultException
     */
    public function getBaseEntryForPeriodicity(PeriodicityInterface $periodicity): ?EntryInterface
    {
        return $this->createQBL()
            ->andWhere('entry.periodicity = :periodicity')
            ->setParameter('periodicity', $periodicity)
            ->orderBy('entry.startTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPeriodicityEntry(EntryInterface $entry): ?EntryInterface
    {
        $periodicity = $entry->getPeriodicity();
        Assert::notNull($periodicity);

        return $this->createQBL()
            ->andWhere('entry.startTime = :start')
            ->setParameter('start', $entry->getStartTime())
            ->andWhere('entry.endTime = :end')
            ->setParameter('end', $entry->getEndTime())
            ->andWhere('entry.periodicity = :periodicity')
            ->setParameter('periodicity', $periodicity)
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removeEntriesByPeriodicity(PeriodicityInterface $periodicity, EntryInterface $entryToSkip): void
    {
        foreach ($this->findByPeriodicity($periodicity) as $entry) {
            if ($entry->getId() !== $entryToSkip->getId()) {
                $this->remove($entry);
            }
        }
    }

    /**
     * @return array|EntryInterface[]
     */
    public function findModerates(): array
    {
        return $this->createQBL()
            ->andWhere('entry.moderate = 1')
            ->addOrderBy('entry.createdBy', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Entry[]
     */
    public function findByMonthAndRoom(CarbonInterface $carbon, RoomInterface $room): array
    {

        $firstDayOfMonth = $carbon->copy()->startOfMonth();
        $lastDayOfMonth = $carbon->copy()->endOfMonth();

        return $this->createQBL()
            ->andWhere('entry.startTime >= :begin AND entry.endTime <= :end')
            ->setParameter('begin', $firstDayOfMonth->format('Y-m-d'))
            ->setParameter('end', $lastDayOfMonth->format('Y-m-d'))
            ->andWhere('entry.room = :room')
            ->setParameter('room', $room)
            ->orderBy('entry.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
