<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260105204824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema - create all tables from scratch';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS auto_ecole (id INT AUTO_INCREMENT NOT NULL, nom_auto_ecole VARCHAR(50) NOT NULL, tel_auto_ecole VARCHAR(20) NOT NULL, siret_auto_ecole VARCHAR(20) NOT NULL, image_auto_ecole VARCHAR(100) NOT NULL, lien_web_auto_ecole VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS eleve (id INT AUTO_INCREMENT NOT NULL, auto_ecole_id INT DEFAULT NULL, nom_eleve VARCHAR(50) NOT NULL, prenom_eleve VARCHAR(50) NOT NULL, tel_eleve VARCHAR(20) NOT NULL, date_inscription DATE NOT NULL, naissance_eleve DATE DEFAULT NULL, INDEX IDX_ECA105F7B1C987E1 (auto_ecole_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, prix_ht NUMERIC(10, 2) NOT NULL, vat_rate NUMERIC(3, 2) NOT NULL, quantity INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7B1C987E1 FOREIGN KEY (auto_ecole_id) REFERENCES auto_ecole (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS eleve');
        $this->addSql('DROP TABLE IF EXISTS auto_ecole');
        $this->addSql('DROP TABLE IF EXISTS product');
        $this->addSql('DROP TABLE IF EXISTS user');
    }
}
