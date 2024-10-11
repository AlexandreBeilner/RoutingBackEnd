<?php

namespace Routing\Service;

use Routing\Repository\ClassdaysRepository;

class ClassDaysService
{
    private ClassdaysRepository $classdaysRepository;

    public function __construct()
    {
        $this->classdaysRepository = new ClassdaysRepository();
    }

    public function getClassDaysByUserID($userID): array
    {
        return $this->classdaysRepository->getClassDaysByUserId($userID);
    }
}