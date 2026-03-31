<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105204824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, prix_ht NUMERIC(10, 2) NOT NULL, vat_rate NUMERIC(3, 2) NOT NULL, quantity INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE sales');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP INDEX siret ON auto_ecole');
        $this->addSql('ALTER TABLE auto_ecole ADD nom_auto_ecole VARCHAR(50) NOT NULL, ADD tel_auto_ecole VARCHAR(20) NOT NULL, ADD siret_auto_ecole VARCHAR(20) NOT NULL, ADD image_auto_ecole VARCHAR(100) NOT NULL, ADD lien_web_auto_ecole VARCHAR(100) NOT NULL, DROP nom_commercial, DROP adresse, DROP telephone, DROP siret, DROP image, DROP site_web, DROP email');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY `eleve_ibfk_1`');
        $this->addSql('DROP INDEX utilisateur_id ON eleve');
        $this->addSql('ALTER TABLE eleve ADD nom_eleve VARCHAR(50) NOT NULL, ADD tel_eleve VARCHAR(20) NOT NULL, ADD date_inscription DATE NOT NULL, ADD prenom_eleve VARCHAR(50) NOT NULL, DROP utilisateur_id, DROP nom, DROP prenom, DROP telephone, DROP email, DROP date_naissance, DROP date_inscription_site, DROP neph, CHANGE date_inscription_auto_ecole naissance_eleve DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE eleve RENAME INDEX auto_ecole_id TO IDX_ECA105F7B1C987E1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, price_ht NUMERIC(10, 2) NOT NULL, vat_rate NUMERIC(4, 2) DEFAULT \'0.20\', quantity INT DEFAULT 0 NOT NULL, UNIQUE INDEX sku (sku), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sales (id INT AUTO_INCREMENT NOT NULL, product_sku VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, quantity_sold INT NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, sale_date DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, mot_de_passe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, role ENUM(\'ELEVE\', \'FORMATEUR\', \'RESPONSABLE\', \'MODERATEUR\', \'COMMERCIAL\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, date_creation DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE auto_ecole ADD nom_commercial VARCHAR(150) NOT NULL, ADD adresse TEXT NOT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD siret VARCHAR(14) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD site_web VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(150) NOT NULL, DROP nom_auto_ecole, DROP tel_auto_ecole, DROP siret_auto_ecole, DROP image_auto_ecole, DROP lien_web_auto_ecole');
        $this->addSql('CREATE UNIQUE INDEX siret ON auto_ecole (siret)');
        $this->addSql('ALTER TABLE eleve ADD utilisateur_id INT NOT NULL, ADD nom VARCHAR(100) NOT NULL, ADD prenom VARCHAR(100) NOT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD email VARCHAR(150) DEFAULT NULL, ADD date_inscription_site DATE NOT NULL, ADD neph VARCHAR(20) DEFAULT NULL, DROP nom_eleve, DROP tel_eleve, DROP prenom_eleve, CHANGE date_inscription date_naissance DATE NOT NULL, CHANGE naissance_eleve date_inscription_auto_ecole DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT `eleve_ibfk_1` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX utilisateur_id ON eleve (utilisateur_id)');
        $this->addSql('ALTER TABLE eleve RENAME INDEX idx_eca105f7b1c987e1 TO auto_ecole_id');
    }
}
