<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220429191055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, dni VARCHAR(9) NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE espacio (id INT AUTO_INCREMENT NOT NULL, zona_id INT DEFAULT NULL, x0 INT NOT NULL, y0 INT NOT NULL, z0 INT NOT NULL, es_oferta TINYINT(1) NOT NULL, porcentaje_oferta INT DEFAULT NULL, INDEX IDX_90BF6AA4104EA8FC (zona_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE factura (id INT AUTO_INCREMENT NOT NULL, cliente_id INT DEFAULT NULL, fecha DATETIME NOT NULL, importe_total DOUBLE PRECISION NOT NULL, importe_sin_iva DOUBLE PRECISION NOT NULL, importe_iva DOUBLE PRECISION NOT NULL, INDEX IDX_F9EBA009DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historial_precio (id INT AUTO_INCREMENT NOT NULL, producto_id INT NOT NULL, precio DOUBLE PRECISION NOT NULL, fecha_inicio DATETIME NOT NULL, fecha_fin DATETIME NOT NULL, INDEX IDX_113665207645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_usuario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_admin TINYINT(1) NOT NULL, is_empleado TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE linea_de_factura (id INT AUTO_INCREMENT NOT NULL, productos_id INT DEFAULT NULL, factura_id INT DEFAULT NULL, cantidad INT NOT NULL, importe DOUBLE PRECISION NOT NULL, INDEX IDX_6CC6030BED07566B (productos_id), INDEX IDX_6CC6030BF04F795F (factura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE producto_espacio (id INT AUTO_INCREMENT NOT NULL, espacio_id INT DEFAULT NULL, producto_id INT DEFAULT NULL, cantidad INT DEFAULT NULL, INDEX IDX_176219C7CFC1D2C (espacio_id), INDEX IDX_176219C7645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proveedor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, telefono VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, contacto VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, info_usuario_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2265B05D83FD7F32 (info_usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario_tienda (id INT AUTO_INCREMENT NOT NULL, tienda_id INT DEFAULT NULL, usuario_id INT DEFAULT NULL, tipo_usuario VARCHAR(255) NOT NULL, INDEX IDX_3AD4063719BA6D46 (tienda_id), INDEX IDX_3AD40637DB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zona (id INT AUTO_INCREMENT NOT NULL, tienda_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, bounding_box_x0 INT NOT NULL, bounding_box_y0 INT NOT NULL, bounding_box_x1 INT NOT NULL, bounding_box_y1 INT NOT NULL, bounding_box_x2 INT NOT NULL, bounding_box_y2 INT NOT NULL, bounding_box_x3 INT NOT NULL, bounding_box_y3 INT NOT NULL, INDEX IDX_A786041E19BA6D46 (tienda_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE espacio ADD CONSTRAINT FK_90BF6AA4104EA8FC FOREIGN KEY (zona_id) REFERENCES zona (id)');
        $this->addSql('ALTER TABLE factura ADD CONSTRAINT FK_F9EBA009DE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE historial_precio ADD CONSTRAINT FK_113665207645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE linea_de_factura ADD CONSTRAINT FK_6CC6030BED07566B FOREIGN KEY (productos_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE linea_de_factura ADD CONSTRAINT FK_6CC6030BF04F795F FOREIGN KEY (factura_id) REFERENCES factura (id)');
        $this->addSql('ALTER TABLE producto_espacio ADD CONSTRAINT FK_176219C7CFC1D2C FOREIGN KEY (espacio_id) REFERENCES espacio (id)');
        $this->addSql('ALTER TABLE producto_espacio ADD CONSTRAINT FK_176219C7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D83FD7F32 FOREIGN KEY (info_usuario_id) REFERENCES info_usuario (id)');
        $this->addSql('ALTER TABLE usuario_tienda ADD CONSTRAINT FK_3AD4063719BA6D46 FOREIGN KEY (tienda_id) REFERENCES tienda (id)');
        $this->addSql('ALTER TABLE usuario_tienda ADD CONSTRAINT FK_3AD40637DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE zona ADD CONSTRAINT FK_A786041E19BA6D46 FOREIGN KEY (tienda_id) REFERENCES tienda (id)');
        $this->addSql('ALTER TABLE producto ADD proveedor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB0615CB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id)');
        $this->addSql('CREATE INDEX IDX_A7BB0615CB305D73 ON producto (proveedor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE factura DROP FOREIGN KEY FK_F9EBA009DE734E51');
        $this->addSql('ALTER TABLE producto_espacio DROP FOREIGN KEY FK_176219C7CFC1D2C');
        $this->addSql('ALTER TABLE linea_de_factura DROP FOREIGN KEY FK_6CC6030BF04F795F');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D83FD7F32');
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB0615CB305D73');
        $this->addSql('ALTER TABLE usuario_tienda DROP FOREIGN KEY FK_3AD40637DB38439E');
        $this->addSql('ALTER TABLE espacio DROP FOREIGN KEY FK_90BF6AA4104EA8FC');
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP TABLE espacio');
        $this->addSql('DROP TABLE factura');
        $this->addSql('DROP TABLE historial_precio');
        $this->addSql('DROP TABLE info_usuario');
        $this->addSql('DROP TABLE linea_de_factura');
        $this->addSql('DROP TABLE producto_espacio');
        $this->addSql('DROP TABLE proveedor');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE usuario_tienda');
        $this->addSql('DROP TABLE zona');
        $this->addSql('DROP INDEX IDX_A7BB0615CB305D73 ON producto');
        $this->addSql('ALTER TABLE producto DROP proveedor_id');
    }
}
