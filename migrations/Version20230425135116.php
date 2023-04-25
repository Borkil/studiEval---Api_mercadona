<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425135116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD finishe_deald_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD percentage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD price_deal DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN product.finishe_deald_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP finishe_deald_at');
        $this->addSql('ALTER TABLE product DROP percentage');
        $this->addSql('ALTER TABLE product DROP price_deal');
    }
}
