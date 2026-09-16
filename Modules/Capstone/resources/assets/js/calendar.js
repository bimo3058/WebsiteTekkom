export function calendarDays(year, month, weekStartsOn = 0) {
    const first = new Date(year, month, 1);
    const offset = (first.getDay() - weekStartsOn + 7) % 7;
    const length = Math.ceil((offset + new Date(year, month + 1, 0).getDate()) / 7) * 7;
    return Array.from({length}, (_, index) => {
        const value = new Date(year, month, index - offset + 1);
        return {key:localDateKey(value),number:value.getDate(),current:value.getMonth()===month,today:localDateKey(value)===localDateKey(new Date())};
    });
}
export function localDateKey(value) {
    if(typeof value === 'string' && /^\d{4}-\d{2}-\d{2}(?:$|T| )/.test(value)) return value.slice(0,10);
    const date=value instanceof Date ? value : new Date(value);
    if(Number.isNaN(date.getTime())) return '';
    return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
}
export const eventColors = {BIMBINGAN:'bg-blue-100 text-blue-700 border-blue-200',SEMPRO:'bg-amber-100 text-amber-700 border-amber-200',SIDANG:'bg-primary-100 text-primary-500 border-primary-200',EXPO:'bg-emerald-100 text-emerald-700 border-emerald-200',TA_DEFENSE:'bg-rose-100 text-rose-700 border-rose-200'};
export const eventLabels = {BIMBINGAN:'Bimbingan',SEMPRO:'Sempro',SIDANG:'Sidang TA',EXPO:'Expo',TA_DEFENSE:'TA Defense'};
export function normalizeSchedule(item,type) {
    const kind = type || item.type || 'BIMBINGAN';
    return {...item,type:kind,date:item.date || item.scheduled_at,period_name:item.period_name || item.group?.period?.name || '',_key:`${kind}:${item.id}`,_id:String(item.id).replace(/^(ta_|bim_)/,''),room:item.room || item.location?.name || '',student_name:item.student_name || item.student?.name || item.students?.map(s=>s.name).join(', ')};
}
