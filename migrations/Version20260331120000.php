<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260331120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes category, description et image sur la table product';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD category VARCHAR(100) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP category, DROP description, DROP image');
    }
}
