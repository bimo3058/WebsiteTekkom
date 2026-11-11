<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ParticipantListFilters
{
    public static function apply(Builder $query, Request $request): Builder
    {
        $filters=$request->validate(['search'=>'nullable|string|max:255','kelompok'=>'nullable|string|max:50','shift'=>'nullable|string|max:50']);
        foreach(['kelompok','shift'] as $field){
            $value=trim($filters[$field]??'');
            if($value!=='')$query->where($field,$value);
        }
        $search=trim($filters['search']??'');
        if($search!==''){
            $search='%'.mb_strtolower($search).'%';
            $query->whereHas('user',fn($q)=>$q->whereRaw('LOWER(name) LIKE ?',[$search])->orWhereRaw('LOWER(email) LIKE ?',[$search])->orWhereHas('student',fn($s)=>$s->where('student_number','like',$search)));
        }
        return $query;
    }
}
