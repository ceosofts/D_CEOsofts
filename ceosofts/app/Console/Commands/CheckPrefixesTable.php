<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Prefix;

class CheckPrefixesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:prefixes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check prefixes table structure and data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking prefixes table structure and data...');

        // 1. ตรวจสอบการเชื่อมต่อฐานข้อมูล
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $this->error('❌ Database connection: FAILED - ' . $e->getMessage());
            return 1;
        }

        // 2. ตรวจสอบว่าตาราง prefixes มีอยู่จริงหรือไม่
        if (!Schema::hasTable('prefixes')) {
            $this->error('❌ Table "prefixes" does not exist');
            return 1;
        } else {
            $this->info('✅ Table "prefixes": EXISTS');
        }

        // 3. ตรวจสอบคอลัมน์ในตาราง
        $columns = Schema::getColumnListing('prefixes');
        $this->info('📋 Columns in prefixes table: ' . implode(', ', $columns));

        // 4. ตรวจสอบจำนวนข้อมูลในตาราง
        $count = DB::table('prefixes')->count();
        $this->info("📊 Records count: $count");

        // 5. ดึงข้อมูลตัวอย่าง
        if ($count > 0) {
            $records = DB::table('prefixes')->take(3)->get();
            $this->info('📝 Sample records:');
            foreach ($records as $record) {
                $this->line("   ID: {$record->id}, Name: " . ($record->prefix_th ?? $record->name ?? 'NULL'));
            }
        } else {
            $this->warn('⚠️ No records found in prefixes table');
        }

        // 6. ตรวจสอบว่าสามารถดึงข้อมูลด้วย Eloquent ได้หรือไม่
        try {
            $this->info('🔍 Testing Eloquent findOrFail with ID = 1:');
            if (DB::table('prefixes')->where('id', 1)->exists()) {
                $prefix = Prefix::findOrFail(1);
                $this->info("   Found: ID: {$prefix->id}, Name: " . ($prefix->prefix_th ?? $prefix->name ?? 'NULL'));
            } else {
                $this->warn("   ⚠️ No record with ID = 1");
            }
        } catch (\Exception $e) {
            $this->error('❌ Error with Eloquent findOrFail: ' . $e->getMessage());
        }

        return 0;
    }
}
