<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240722110619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE littoral (id INT AUTO_INCREMENT NOT NULL, annee_cog VARCHAR(255) DEFAULT NULL, insee_reg_2016 VARCHAR(255) DEFAULT NULL, nom_reg_2016 VARCHAR(255) DEFAULT NULL, insee_dep VARCHAR(255) DEFAULT NULL, nom_dept VARCHAR(255) DEFAULT NULL, insee_com VARCHAR(255) DEFAULT NULL, nom_com VARCHAR(255) DEFAULT NULL, classement VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE littoral');
    }
}