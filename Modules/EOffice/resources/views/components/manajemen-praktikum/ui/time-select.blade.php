@props(['min'=>'00:00','max'=>'23:59','label'=>'Waktu'])
@php
    $timeHours=[];
    for($hour=(int)substr($min,0,2);$hour<=(int)substr($max,0,2);$hour++){$key=sprintf('%02d',$hour);$timeHours[$key]=$key;}
@endphp
<div x-data="{
    timeValue:'',timeMin:@js($min),timeMax:@js($max),
    get timeHour(){return this.timeValue.slice(0,2)},
    set timeHour(hour){let minute=this.timeMinute||'00';const low=hour===this.timeMin.slice(0,2)?this.timeMin.slice(3,5):'00',high=hour===this.timeMax.slice(0,2)?this.timeMax.slice(3,5):'59';minute=minute<low?low:(minute>high?high:minute);this.timeValue=hour+':'+minute},
    get timeMinute(){return this.timeValue.slice(3,5)},
    set timeMinute(minute){if(this.timeHour)this.timeValue=this.timeHour+':'+minute},
    get timeMinuteOptions(){const hour=this.timeHour,low=hour===this.timeMin.slice(0,2)?Number(this.timeMin.slice(3,5)):0,high=hour===this.timeMax.slice(0,2)?Number(this.timeMax.slice(3,5)):59;return Object.fromEntries(Array.from({length:high-low+1},(_,i)=>{const value=String(i+low).padStart(2,'0');return [value,value]}))}
}" x-modelable="timeValue" {{ $attributes }} class="mp-schedule-time-select" role="group" aria-label="{{ $label }}">
    <div><x-eoffice::manajemen-praktikum.ui.ta-select x-model="timeHour" :options="$timeHours" placeholder="Jam" aria-label="{{ $label }}: jam" /></div>
    <span aria-hidden="true">:</span>
    <div><x-eoffice::manajemen-praktikum.ui.ta-select x-model="timeMinute" :options="[]" x-effect="options=timeMinuteOptions" placeholder="Menit" aria-label="{{ $label }}: menit" /></div>
</div>
