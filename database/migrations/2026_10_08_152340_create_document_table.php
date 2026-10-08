<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateDocumentTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('documents');
        
        //  Define columns here
        $table->id();
        $table->bigInteger('category_id')->unsigned()->notNullable();
        $table->string('title')->notNullable();
        $table->text('description')->nullable();
        $table->string('original_name');
        $table->string('stored_name');
        $table->string('file_path');
        $table->bigInteger('file_size');
        $table->string('mime_type');
        $table->string('file_ext');
        $table->string('file_hash');
        $table->enum('status',['active','archived','deleted'])->default('active');
        $table->bigInteger('upload_by')->unsigned();
        $table->dateTime('uploaded_at')->default('CURRENT_TIMESTAMP');
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['title']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'document' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('documents');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'document' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
