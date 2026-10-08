<?php


use Bpjs\Framework\Helpers\SchemaBuilder;

class CreateJobsTable
{
    public function up(\PDO $pdo)
    {
        $table = new SchemaBuilder('jobs');
        $table->id();
        $table->string('queue',100)->nullable();
        $table->longText('payload')->notNullable();
        $table->string('status',20)->nullable()->default('pending');
        $table->integer('attempts')->default(0);
        $table->timestamp('available_at')->default('CURRENT_TIMESTAMP');
        $table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
        $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP');
        $sql = $table->buildCreateSQL();
        try {
             $pdo->exec($sql);
             echo "Table 'jobs' berhasil dibuat\n";
        } catch (\PDOException $e) {
             echo "Gagal membuat tabel: ".$e->getMessage()."\n";
             echo "SQL:".$sql;
        }
    }

    public function down(PDO $pdo)
    {
        $table = new SchemaBuilder('jobs');
        $pdo->exec($table->buildDropSQL());
    }
}
