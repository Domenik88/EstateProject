<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 30.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Page;

use App\Entity\Page;
use App\Repository\PageRepository;

class PageService
{

    private PageRepository $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function search(array $criteria): ?Page
    {
        return $this->pageRepository->findOneBy($criteria);
    }
}