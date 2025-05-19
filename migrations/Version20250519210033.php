<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519210033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_favoris (user_id INTEGER NOT NULL, webtoon_id INTEGER NOT NULL, PRIMARY KEY(user_id, webtoon_id), CONSTRAINT FK_D13EDA38A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D13EDA38CB3BA083 FOREIGN KEY (webtoon_id) REFERENCES webtoon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D13EDA38A76ED395 ON user_favoris (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D13EDA38CB3BA083 ON user_favoris (webtoon_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE user_favoris
        SQL);
    }
}
