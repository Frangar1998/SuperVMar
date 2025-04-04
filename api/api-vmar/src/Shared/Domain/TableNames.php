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
    case TABLE_JOB = 'job';
    case TABLE_WORKER_ALLOCATION = 'worker_allocation';
    case TABLE_CATEGORY = 'category';
    case TABLE_TAX = 'tax';
    case TABLE_PRODUCT = 'product';
}
