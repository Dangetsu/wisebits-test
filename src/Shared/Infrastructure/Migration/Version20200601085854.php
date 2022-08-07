<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601085854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Init users table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            <<<SQL
            create table users
            (
                id int auto_increment,
                name varchar(64) not null,
                email varchar(256) not null,
                created DATETIME not null default CURRENT_TIMESTAMP,
                deleted DATETIME null,
                notes TEXT null,
                constraint users_pk
                    primary key (id)
            );
SQL
        );
        $this->addSql('create unique index users_email_uindex on users (email);');
        $this->addSql('create unique index users_name_uindex on users (name);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users');
    }
}
