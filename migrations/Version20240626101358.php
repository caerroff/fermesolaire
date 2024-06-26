<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626101358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_airtable ADD typghi VARCHAR(255) DEFAULT NULL, ADD typenviro JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD znieff1 VARCHAR(255) DEFAULT NULL, ADD znieff2 VARCHAR(255) DEFAULT NULL, ADD n2000_habitats VARCHAR(255) DEFAULT NULL, ADD n2000_doiseaux VARCHAR(255) DEFAULT NULL, ADD pnr VARCHAR(255) DEFAULT NULL, ADD typppri VARCHAR(255) DEFAULT NULL, ADD typzone_ppri VARCHAR(255) DEFAULT NULL, ADD mh VARCHAR(255) DEFAULT NULL, ADD zone_humide VARCHAR(255) DEFAULT NULL, ADD typinfo_comp LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_airtable DROP typghi, DROP typenviro, DROP znieff1, DROP znieff2, DROP n2000_habitats, DROP n2000_doiseaux, DROP pnr, DROP typppri, DROP typzone_ppri, DROP mh, DROP zone_humide, DROP typinfo_comp');
    }
}
