<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionXXXXXX extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert admin user with email and password "admin"';
    }

    public function up(Schema $schema): void
    {
        // This migration is for adding data to the user table.
        $this->addSql('INSERT INTO user (email, password) VALUES (:email, :password)', [
            'email' => 'admin@admin.com',
            'password' => password_hash('admin', PASSWORD_BCRYPT), // Use bcrypt or another secure hashing algorithm
        ]);
    }

    public function down(Schema $schema): void
    {
        // Rollback the inserted user.
        $this->addSql('DELETE FROM user WHERE email = :email', [
            'email' => 'admin',
        ]);
    }
}
