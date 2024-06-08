<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240608164258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE record_airtable (id INT AUTO_INCREMENT NOT NULL, relais_id INT DEFAULT NULL, typurba VARCHAR(255) DEFAULT NULL, rpg VARCHAR(255) DEFAULT NULL, typdis_racc VARCHAR(255) DEFAULT NULL, typcap_racc VARCHAR(255) DEFAULT NULL, typnom_racc VARCHAR(255) DEFAULT NULL, typville_racc VARCHAR(255) DEFAULT NULL, INDEX IDX_3FAF706A5B41AD20 (relais_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_airtable ADD CONSTRAINT FK_3FAF706A5B41AD20 FOREIGN KEY (relais_id) REFERENCES relais (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_airtable DROP FOREIGN KEY FK_3FAF706A5B41AD20');
        $this->addSql('DROP TABLE record_airtable');
    }
}
