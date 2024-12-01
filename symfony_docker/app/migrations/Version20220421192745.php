<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220421192745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tienda (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, direccion VARCHAR(255) NOT NULL, telefono VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tipo_impositivo (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, porcentaje INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE producto ADD tipo_iva_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB061567889002 FOREIGN KEY (tipo_iva_id) REFERENCES tipo_impositivo (id)');
        $this->addSql('CREATE INDEX IDX_A7BB061567889002 ON producto (tipo_iva_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB061567889002');
        $this->addSql('DROP TABLE tienda');
        $this->addSql('DROP TABLE tipo_impositivo');
        $this->addSql('DROP INDEX IDX_A7BB061567889002 ON producto');
        $this->addSql('ALTER TABLE producto DROP tipo_iva_id');
    }
}
