<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateItemsTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('items');
        
        //  Define columns here
        $table->id();
        $table->string('ics_code')->notNullable();
        $table->string('ics_name')->notNullable();
        $table->string('type')->nullable();
        $table->string('unit')->default('PCS');
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['ics_code', 'ics_name']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'items' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('items');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'items' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
