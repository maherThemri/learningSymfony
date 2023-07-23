<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230722091535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $tableName = 'user';
        $columnName = 'is_verified';

        // Check if the column already exists in the table
        if (!$schema->getTable($tableName)->hasColumn($columnName)) {
            $this->addSql('ALTER TABLE ' . $tableName . ' ADD ' . $columnName . ' TINYINT(1) NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $tableName = 'user';
        $columnName = 'is_verified';

        // Check if the column exists in the table before dropping it
        if ($schema->getTable($tableName)->hasColumn($columnName)) {
            $this->addSql('ALTER TABLE ' . $tableName . ' DROP ' . $columnName);
        }
    }
}
