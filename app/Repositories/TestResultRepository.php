<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use ResultSelectionCriterion;
use Test;
use TestResult;
use User;

class TestResultRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, TestResult::class);
    }

}