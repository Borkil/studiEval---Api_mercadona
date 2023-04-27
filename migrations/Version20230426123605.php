<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426123605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04ad557b630');
        $this->addSql('DROP SEQUENCE product_status_id_seq CASCADE');
        $this->addSql('DROP TABLE product_status');
        $this->addSql('DROP INDEX idx_d34a04ad557b630');
        $this->addSql('ALTER TABLE product DROP product_status_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE product_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product_status (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE product ADD product_status_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04ad557b630 FOREIGN KEY (product_status_id) REFERENCES product_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d34a04ad557b630 ON product (product_status_id)');
    }
}
