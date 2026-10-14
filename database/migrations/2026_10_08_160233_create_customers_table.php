<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateCustomersTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('customers');
        
        //  Define columns here
        $table->id();
        $table->string('code_customer')->unique();
        $table->string('name')->notNullable();
        $table->text('address')->nullable();
        $table->string('contact_person')->nullable();
        $table->string('phone')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['name', 'code_customer']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'customers' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('customers');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'customers' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
