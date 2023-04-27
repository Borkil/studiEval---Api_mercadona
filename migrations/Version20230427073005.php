<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427073005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD is_deal BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE product ADD is_archive BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE product RENAME COLUMN finishe_deald_at TO finish_deald_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP is_deal');
        $this->addSql('ALTER TABLE product DROP is_archive');
        $this->addSql('ALTER TABLE product RENAME COLUMN finish_deald_at TO finishe_deald_at');
    }
}
