<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240703134541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_airtable DROP FOREIGN KEY FK_3FAF706A5B41AD20');
        $this->addSql('DROP INDEX IDX_3FAF706A5B41AD20 ON record_airtable');
        $this->addSql('ALTER TABLE record_airtable ADD relais VARCHAR(255) DEFAULT NULL, DROP relais_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_airtable ADD relais_id INT DEFAULT NULL, DROP relais');
        $this->addSql('ALTER TABLE record_airtable ADD CONSTRAINT FK_3FAF706A5B41AD20 FOREIGN KEY (relais_id) REFERENCES relais (id)');
        $this->addSql('CREATE INDEX IDX_3FAF706A5B41AD20 ON record_airtable (relais_id)');
    }
}
