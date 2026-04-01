<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260107181517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create sale table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS sale (id INT AUTO_INCREMENT NOT NULL, product_sku VARCHAR(50) NOT NULL, quantity_sold INT NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS sale');
    }
}
