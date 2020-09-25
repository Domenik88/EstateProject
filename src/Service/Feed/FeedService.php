<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 18.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;


use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;

class FeedService
{
    private EntityManagerInterface $entityManager;
    private FeedRepository $feedRepository;

    public function __construct(EntityManagerInterface $entityManager, FeedRepository $feedRepository)
    {
        $this->entityManager = $entityManager;
        $this->feedRepository = $feedRepository;
    }

    public function setBusyByFeedName(string $name, bool $busy): \DateTimeInterface
    {
        $setBusyFeed = $this->feedRepository->findOneBy([
            'name' => $name
        ]);
        $setBusyFeed->setBusy($busy);
        $this->entityManager->flush();
        return $setBusyFeed->getLastRunTime();
    }

    public function isFeedBusy(string $name)
    {
        $busyFeed = $this->feedRepository->findOneBy([
            'name' => $name
        ]);
        return $busyFeed->isBusy();
    }

    public function setLastRunTimeByFeedName(string $name, \DateTimeInterface $date)
    {
        $lastRunTimeFeed = $this->feedRepository->findOneBy([
            'name' => $name
        ]);
        $lastRunTimeFeed->setLastRunTime($date);
    }
}