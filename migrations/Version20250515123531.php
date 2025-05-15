<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515123531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__webtoon AS SELECT id, user_id, titre, description, image, slug FROM webtoon
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE webtoon
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE webtoon (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, CONSTRAINT FK_E0D10BFEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO webtoon (id, user_id, titre, description, image, slug) SELECT id, user_id, titre, description, image, slug FROM __temp__webtoon
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__webtoon
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E0D10BFEFF7747B4 ON webtoon (titre)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E0D10BFEA76ED395 ON webtoon (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E0D10BFE989D9B62 ON webtoon (slug)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__webtoon AS SELECT id, user_id, titre, description, image, slug FROM webtoon
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE webtoon
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE webtoon (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_E0D10BFEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO webtoon (id, user_id, titre, description, image, slug) SELECT id, user_id, titre, description, image, slug FROM __temp__webtoon
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__webtoon
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E0D10BFEFF7747B4 ON webtoon (titre)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E0D10BFEA76ED395 ON webtoon (user_id)
        SQL);
    }
}
