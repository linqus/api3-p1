<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404153531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dragon_treasure ADD owned_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE dragon_treasure ADD CONSTRAINT FK_9E31BF5F5E70BCD7 FOREIGN KEY (owned_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9E31BF5F5E70BCD7 ON dragon_treasure (owned_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE dragon_treasure DROP CONSTRAINT FK_9E31BF5F5E70BCD7');
        $this->addSql('DROP INDEX IDX_9E31BF5F5E70BCD7');
        $this->addSql('ALTER TABLE dragon_treasure DROP owned_by_id');
    }
}
