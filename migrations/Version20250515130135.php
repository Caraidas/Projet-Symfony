<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515130135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__episode AS SELECT id, title, created_at, number FROM episode
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE episode
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE episode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, webtoon_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , number INTEGER NOT NULL, CONSTRAINT FK_DDAA1CDACB3BA083 FOREIGN KEY (webtoon_id) REFERENCES webtoon (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO episode (id, title, created_at, number) SELECT id, title, created_at, number FROM __temp__episode
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__episode
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DDAA1CDACB3BA083 ON episode (webtoon_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__image AS SELECT id, url, position FROM image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE image
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, episode_id INTEGER NOT NULL, url VARCHAR(255) NOT NULL, position INTEGER NOT NULL, CONSTRAINT FK_C53D045F362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO image (id, url, position) SELECT id, url, position FROM __temp__image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__image
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C53D045F362B62A0 ON image (episode_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__episode AS SELECT id, title, created_at, number FROM episode
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE episode
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE episode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , number INTEGER NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO episode (id, title, created_at, number) SELECT id, title, created_at, number FROM __temp__episode
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__episode
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__image AS SELECT id, url, position FROM image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE image
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, position INTEGER NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO image (id, url, position) SELECT id, url, position FROM __temp__image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__image
        SQL);
    }
}
