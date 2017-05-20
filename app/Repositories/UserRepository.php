<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PaginationResult;
use Repositories\Interfaces\IUserRepository;
use Doctrine\ORM\Query\Expr\Join;
use Role;
use RoleUser;
use StudentGroup;
use User;
use UserRole;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, User::class);
    }




}