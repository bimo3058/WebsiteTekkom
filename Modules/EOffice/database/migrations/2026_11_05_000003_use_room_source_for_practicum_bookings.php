<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function(){
            if(DB::getDriverName()==='pgsql')DB::statement("SET LOCAL lock_timeout='5s'");
            if(!Schema::hasColumn('eo_mr_peminjamans','source'))Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$t->string('source',50)->nullable());
            if(!Schema::hasColumn('eo_prak_booking_meetings','note'))Schema::table('eo_prak_booking_meetings',fn(Blueprint $t)=>$t->string('note',500)->nullable());
            if(Schema::hasColumn('eo_mr_peminjamans','practicum_meeting_id')){
                foreach(DB::table('eo_mr_peminjamans')->whereNotNull('practicum_meeting_id')->get(['id','practicum_meeting_id']) as $booking){
                    $meeting=DB::table('eo_prak_booking_meetings')->where('id',$booking->practicum_meeting_id)->first();
                    if(!$meeting || ($meeting->booking_id!==null && (int)$meeting->booking_id!==(int)$booking->id))throw new RuntimeException('Hubungan booking dan pertemuan tidak konsisten. Migrasi dihentikan tanpa menghapus kolom.');
                    DB::table('eo_prak_booking_meetings')->where('id',$meeting->id)->update(['booking_id'=>$booking->id]);
                }
            }
            DB::table('eo_mr_peminjamans')->whereExists(fn($q)=>$q->selectRaw('1')->from('eo_prak_booking_meetings')->whereColumn('eo_prak_booking_meetings.booking_id','eo_mr_peminjamans.id'))->update(['source'=>'praktikum']);
            if(!Schema::hasIndex('eo_prak_booking_meetings','eo_prak_meeting_booking_unique'))Schema::table('eo_prak_booking_meetings',fn(Blueprint $t)=>$t->unique('booking_id','eo_prak_meeting_booking_unique'));
            if(Schema::hasColumn('eo_mr_peminjamans','practicum_meeting_id')){
                foreach(Schema::getIndexes('eo_mr_peminjamans') as $index)if($index['columns']===['practicum_meeting_id'])Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$index['unique']?$t->dropUnique($index['name']):$t->dropIndex($index['name']));
                Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$t->dropColumn('practicum_meeting_id'));
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function(){
            if(!Schema::hasColumn('eo_mr_peminjamans','practicum_meeting_id'))Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$t->uuid('practicum_meeting_id')->nullable()->unique());
            foreach(DB::table('eo_prak_booking_meetings')->whereNotNull('booking_id')->get(['id','booking_id']) as $meeting)DB::table('eo_mr_peminjamans')->where('id',$meeting->booking_id)->update(['practicum_meeting_id'=>$meeting->id]);
            // Keep shared source classifications and notes; rollback must not erase these data.
        });
    }
};
