<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateCategoryTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('category');
        
        //  Define columns here
        $table->id();
        $table->string('name')->notNullable();
        $table->string('color',7)->default('#3b82f6');
        $table->string('icon',40)->default('fa-folder');
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['name']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'category' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('category');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'category' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
