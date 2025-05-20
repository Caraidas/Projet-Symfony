<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520225200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__commentaire AS SELECT id, user_id, webtoon_id, episode_id, message FROM commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commentaire
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE commentaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, webtoon_id INTEGER NOT NULL, episode_id INTEGER DEFAULT NULL, message CLOB NOT NULL, CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67F068BCCB3BA083 FOREIGN KEY (webtoon_id) REFERENCES webtoon (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67F068BC362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO commentaire (id, user_id, webtoon_id, episode_id, message) SELECT id, user_id, webtoon_id, episode_id, message FROM __temp__commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__commentaire
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC362B62A0 ON commentaire (episode_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BCCB3BA083 ON commentaire (webtoon_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__commentaire AS SELECT id, user_id, webtoon_id, episode_id, message FROM commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commentaire
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE commentaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, webtoon_id INTEGER NOT NULL, episode_id INTEGER NOT NULL, message CLOB NOT NULL, CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67F068BCCB3BA083 FOREIGN KEY (webtoon_id) REFERENCES webtoon (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67F068BC362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO commentaire (id, user_id, webtoon_id, episode_id, message) SELECT id, user_id, webtoon_id, episode_id, message FROM __temp__commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__commentaire
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BCCB3BA083 ON commentaire (webtoon_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67F068BC362B62A0 ON commentaire (episode_id)
        SQL);
    }
}
