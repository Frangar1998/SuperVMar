<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250404000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration - Create all existing tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `address` (
            `id` char(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `postalCode` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `floor` varchar(10) DEFAULT NULL,
            `door` varchar(10) DEFAULT NULL,
            `other` varchar(255) DEFAULT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `category` (
            `id` varchar(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `job` (
            `id` char(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `sale` (
            `id` varchar(36) NOT NULL,
            `amount` decimal(10,2) NOT NULL DEFAULT \'0.00\',
            `taxes` decimal(10,2) NOT NULL DEFAULT \'0.00\',
            `totalAmount` decimal(10,2) NOT NULL DEFAULT \'0.00\',
            `payMethod` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT \'"NONE"\',
            `date` datetime DEFAULT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `supplier` (
            `id` varchar(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `phone` varchar(12) NOT NULL,
            `email` varchar(100) NOT NULL,
            `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `tax` (
            `id` varchar(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `percent` decimal(5,2) NOT NULL DEFAULT \'0.00\',
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `product` (
            `id` varchar(36) NOT NULL,
            `name` varchar(255) NOT NULL,
            `price` decimal(6,2) NOT NULL,
            `ean` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `stock` int NOT NULL DEFAULT \'0\',
            `image` varchar(255) DEFAULT NULL,
            `idTax` varchar(36) NOT NULL,
            `idCategory` varchar(36) NOT NULL,
            `idSupplier` varchar(36) NOT NULL,
            `active` tinyint NOT NULL DEFAULT \'1\',
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `product_unique` (`ean`),
            KEY `product_tax_FK` (`idTax`),
            KEY `product_supplier_FK` (`idSupplier`),
            KEY `product_category_FK` (`idCategory`),
            KEY `product_price_history_FK` (`price`),
            CONSTRAINT `product_category_FK` FOREIGN KEY (`idCategory`) REFERENCES `category` (`id`),
            CONSTRAINT `product_supplier_FK` FOREIGN KEY (`idSupplier`) REFERENCES `supplier` (`id`),
            CONSTRAINT `product_tax_FK` FOREIGN KEY (`idTax`) REFERENCES `tax` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `sale_line` (
            `id` varchar(36) NOT NULL,
            `idSale` varchar(36) NOT NULL,
            `idProduct` varchar(36) NOT NULL,
            `amount` decimal(10,2) NOT NULL,
            `quantity` int NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `sale_line_product_FK` (`idProduct`),
            KEY `sale_line_sale_FK` (`idSale`),
            CONSTRAINT `sale_line_product_FK` FOREIGN KEY (`idProduct`) REFERENCES `product` (`id`),
            CONSTRAINT `sale_line_sale_FK` FOREIGN KEY (`idSale`) REFERENCES `sale` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `supermarket` (
            `id` char(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
            `phone` varchar(12) DEFAULT NULL,
            `email` varchar(100) NOT NULL,
            `idAddress` char(36) NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `supermarket_address_FK` (`idAddress`),
            CONSTRAINT `supermarket_address_FK` FOREIGN KEY (`idAddress`) REFERENCES `address` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `user_data` (
            `id` char(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
            `surname` varchar(100) DEFAULT NULL,
            `email` varchar(100) DEFAULT NULL,
            `phone` varchar(12) DEFAULT NULL,
            `idAddress` char(36) DEFAULT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `user_data_address_FK` (`idAddress`),
            CONSTRAINT `user_data_address_FK` FOREIGN KEY (`idAddress`) REFERENCES `address` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `zone` (
            `id` char(36) NOT NULL,
            `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
            `idSupermarket` char(36) DEFAULT NULL,
            `cornerTopLeft` varchar(50) DEFAULT NULL,
            `cornerTopRight` varchar(50) DEFAULT NULL,
            `cornerBottomRight` varchar(50) DEFAULT NULL,
            `cornerBottomLeft` varchar(50) DEFAULT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `zone_supermarket_FK` (`idSupermarket`),
            CONSTRAINT `zone_supermarket_FK` FOREIGN KEY (`idSupermarket`) REFERENCES `supermarket` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `price_history` (
            `id` varchar(36) NOT NULL,
            `idProduct` varchar(36) NOT NULL,
            `price` decimal(6,2) NOT NULL,
            `startDate` datetime NOT NULL,
            `endDate` datetime DEFAULT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `price_history_product_FK` (`idProduct`),
            CONSTRAINT `price_history_product_FK` FOREIGN KEY (`idProduct`) REFERENCES `product` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `space` (
            `id` char(36) NOT NULL,
            `position` varchar(100) NOT NULL,
            `idZone` char(36) NOT NULL,
            `maxSpots` int NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `space_FK` (`idZone`),
            CONSTRAINT `space_FK` FOREIGN KEY (`idZone`) REFERENCES `zone` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `user` (
            `id` char(36) NOT NULL,
            `username` varchar(100) NOT NULL,
            `password` varchar(100) DEFAULT NULL,
            `isAdmin` tinyint DEFAULT \'0\',
            `idUserData` char(36) NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `user_username_IDX` (`username`) USING BTREE,
            KEY `user_user_data_FK` (`idUserData`),
            CONSTRAINT `user_user_data_FK` FOREIGN KEY (`idUserData`) REFERENCES `user_data` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `worker_allocation` (
            `idUser` char(36) NOT NULL,
            `idSupermarket` char(36) NOT NULL,
            `idJob` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY `user_supermarket_supermarket_FK` (`idSupermarket`),
            KEY `user_supermarket_user_FK` (`idUser`),
            KEY `user_supermarket_user_job_FK` (`idJob`),
            CONSTRAINT `user_supermarket_supermarket_FK` FOREIGN KEY (`idSupermarket`) REFERENCES `supermarket` (`id`),
            CONSTRAINT `user_supermarket_user_FK` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`),
            CONSTRAINT `user_supermarket_user_job_FK` FOREIGN KEY (`idJob`) REFERENCES `job` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `product_allocation` (
            `idProduct` varchar(36) NOT NULL,
            `idSpace` varchar(36) NOT NULL,
            `quantity` int NOT NULL DEFAULT \'0\',
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`idProduct`,`idSpace`),
            UNIQUE KEY `product_allocation_unique_1` (`idSpace`),
            CONSTRAINT `product_allocation_product_FK` FOREIGN KEY (`idProduct`) REFERENCES `product` (`id`),
            CONSTRAINT `product_allocation_space_FK` FOREIGN KEY (`idSpace`) REFERENCES `space` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');

        $this->addSql('CREATE TABLE `push_subscription` (
            `id` varchar(36) NOT NULL,
            `idUser` varchar(36) NOT NULL,
            `endpoint` text NOT NULL,
            `authKey` varchar(255) NOT NULL,
            `p256dhKey` varchar(255) NOT NULL,
            `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_push_sub_user` (`idUser`),
            CONSTRAINT `push_subscription_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `push_subscription`');
        $this->addSql('DROP TABLE IF EXISTS `product_allocation`');
        $this->addSql('DROP TABLE IF EXISTS `worker_allocation`');
        $this->addSql('DROP TABLE IF EXISTS `user`');
        $this->addSql('DROP TABLE IF EXISTS `space`');
        $this->addSql('DROP TABLE IF EXISTS `price_history`');
        $this->addSql('DROP TABLE IF EXISTS `zone`');
        $this->addSql('DROP TABLE IF EXISTS `user_data`');
        $this->addSql('DROP TABLE IF EXISTS `supermarket`');
        $this->addSql('DROP TABLE IF EXISTS `sale_line`');
        $this->addSql('DROP TABLE IF EXISTS `product`');
        $this->addSql('DROP TABLE IF EXISTS `tax`');
        $this->addSql('DROP TABLE IF EXISTS `supplier`');
        $this->addSql('DROP TABLE IF EXISTS `sale`');
        $this->addSql('DROP TABLE IF EXISTS `job`');
        $this->addSql('DROP TABLE IF EXISTS `category`');
        $this->addSql('DROP TABLE IF EXISTS `address`');
    }
}
