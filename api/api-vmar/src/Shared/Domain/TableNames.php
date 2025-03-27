<?php

namespace SuperVMar\Shared\Domain;

enum TableNames: string
{
    case TABLE_SUPERMARKET = 'supermarket';
    case TABLE_ADDRESS = 'address';
    case TABLE_ZONE = 'zone';
    case TABLE_SPACE = 'space';
    case TABLE_USER = 'user';
    case TABLE_USER_DATA = 'user_data';
    case TABLE_USER_JOB = 'user_job';
}
