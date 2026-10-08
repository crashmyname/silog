<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateRoutesTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('routes');
        
        //  Define columns here
        $table->id();
        $table->string('name')->notNullable();
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['name', 'slug']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'routes' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('routes');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'routes' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
