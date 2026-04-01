<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260401120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename auto_ecole to brand, eleve to customer with proper e-commerce field names';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, nom_brand VARCHAR(50) NOT NULL, tel_brand VARCHAR(20) NOT NULL, siret_brand VARCHAR(20) NOT NULL, image_brand VARCHAR(100) NOT NULL, site_web_brand VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, last_name VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, phone VARCHAR(20) NOT NULL, birth_date DATE DEFAULT NULL, registration_date DATE NOT NULL, INDEX IDX_81398E0944F5D008 (brand_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0944F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('DROP TABLE IF EXISTS eleve');
        $this->addSql('DROP TABLE IF EXISTS auto_ecole');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS customer');
        $this->addSql('DROP TABLE IF EXISTS brand');
    }
}
