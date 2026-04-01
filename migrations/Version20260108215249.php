<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260108215249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add client_id to sale table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sale ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC00519EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E54BC00519EB6921 ON sale (client_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sale DROP FOREIGN KEY FK_E54BC00519EB6921');
        $this->addSql('DROP INDEX IDX_E54BC00519EB6921 ON sale');
        $this->addSql('ALTER TABLE sale DROP client_id');
    }
}
