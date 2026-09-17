import {api,context,unwrap,url,date,notify} from '../../api.js';
export {api,context,unwrap,url,date,notify};
export const specs=['Software','Embedded','Network','Multimedia','AI','Blockchain'];
export const groupFrom=r=>{const d=unwrap(r);return d && Object.hasOwn(d,'group') ? d.group : (d?.id ? d : null);};
export const list=(r,key)=>{const d=unwrap(r);return Array.isArray(d)?d:(d?.[key]??[]);};
export const leader=g=>!!context.actor?.student_id && !!g?.members?.some(m=>m.is_leader && String(m.student_id??m.student?.id)===String(context.actor.student_id));
export const person=p=>p?.name || p?.user?.name || '-';
export const email=p=>p?.email || p?.user?.email || '';
export const show=id=>document.getElementById(id)?.showModal();
export const close=id=>document.getElementById(id)?.close();
export const reasonText={NO_GROUP:'Anda harus memiliki kelompok terlebih dahulu.',LEADER_ONLY:'Hanya ketua kelompok yang dapat mengelola judul.',INSUFFICIENT_MEMBERS:'Jumlah anggota belum memenuhi batas minimal.',TITLE_ALREADY_ASSIGNED:'Kelompok sudah memiliki judul.',PENDING_PROPOSAL_EXISTS:'Masih ada proposal yang sedang ditinjau.',INVALID_GROUP_STATUS:'Status kelompok belum memperbolehkan aksi ini.',ACTIVE_BID_EXISTS:'Masih ada bid aktif.',ACTIVE_PROPOSAL_EXISTS:'Masih ada proposal aktif.',TITLE_LIMIT_REACHED:'Maksimal 3 slot judul sudah tercapai.',NO_ACTIVE_PERIOD:'Periode aktif tidak ditemukan.',PERIOD_FINALIZED:'Periode sudah ditutup oleh admin.',BIDDING_LOCKED:'Bidding sudah dikunci.',BIDDING_WINDOW_CLOSED:'Jendela waktu bidding belum dibuka atau sudah berakhir.',SOLO_GROUP_CANNOT_BID:'Solo seeker dapat bidding judul dosen setelah jumlah anggota memenuhi batas minimal.',GROUP_LOCKED:'Kelompok sudah terkunci untuk finalisasi.',LEADER_SOLO_ONLY:'Hanya mahasiswa tanpa kelompok atau ketua kelompok seeker yang dapat request join.'};
export const badge=s=>['REJECTED','DECLINE'].includes(s)?'bg-destructive/10 text-destructive':['APPROVED','ACCEPT','ACCEPTED'].includes(s)?'bg-green-100 text-green-800':'bg-secondary text-secondary-foreground';
export function base(){return {loading:true,error:'',saving:false,errors:{},group:null,url,date,person,email,badge,reasonText,show,close,get isLeader(){return leader(this.group);},get members(){return this.group?.members??[];},get minMembers(){return this.group?.period?.min_group_size??3;},get maxMembers(){return this.group?.period?.max_group_size??4;},async init(){await this.load();},async action(path,method='POST',body,dialog){if(this.saving)return false;this.saving=true;this.errors={};try{await api(path,{method,body});if(dialog)close(dialog);notify('Perubahan berhasil disimpan');return true;}catch(e){this.errors={...e.errors,root:e.message};notify(e.message,true);return false;}finally{this.saving=false;}}};}

export function withBase(page){return Object.defineProperties(base(),Object.getOwnPropertyDescriptors(page));}
