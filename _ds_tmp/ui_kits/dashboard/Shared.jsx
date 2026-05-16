// SITKOM Design System — Shared Components
// Sidebar, TopBar, StatCard, etc.

const SITKOM_COLORS = {
  primary: '#7A4DF5',
  primaryHover: '#6B39F4',
  primarySubtle: '#F0EDFE',
  primaryBorder: '#B59CFA',
  bg: '#F6F8FA',
  card: '#FFFFFF',
  border: '#DFE1E7',
  fg: '#0D0D12',
  fgSecondary: '#353849',
  fgMuted: '#666D80',
  fgPlaceholder: '#A4ABB8',
  success: '#40C4AA',
  successSubtle: '#DDF2EE',
  warning: '#D39C3D',
  warningSubtle: '#F9ECCB',
  error: '#DF1C41',
  errorSubtle: '#FADAE1',
};

// ── Sidebar ────────────────────────────────────────────────────
function Sidebar({ activePage, onNavigate }) {
  const sections = [
    {
      title: 'Utama',
      items: [
        { id: 'dashboard', label: 'Dashboard', icon: IconGrid },
      ]
    },
    {
      title: 'Kendali',
      items: [
        { id: 'banksoal', label: 'Bank Soal', icon: IconLayers, badge: '3' },
        { id: 'mahasiswa', label: 'Manajemen Mahasiswa', icon: IconUsers },
        { id: 'capstone', label: 'Capstone', icon: IconClipboard },
        { id: 'eoffice', label: 'E-Office', icon: IconFile },
      ]
    },
    {
      title: 'Setting',
      items: [
        { id: 'settings', label: 'Modul Setting', icon: IconGear },
        { id: 'monitor', label: 'System Monitor', icon: IconBarChart, disabled: true },
      ]
    },
  ];

  const sidebarStyles = {
    width: 272,
    height: '100%',
    background: '#fff',
    borderRight: `1px solid ${SITKOM_COLORS.border}`,
    display: 'flex',
    flexDirection: 'column',
    padding: '20px 0',
    flexShrink: 0,
    overflowY: 'auto',
  };

  return (
    <div style={sidebarStyles}>
      {/* Brand header */}
      <div style={{ padding: '0 8px', marginBottom: 8 }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '12px', borderRadius: 10 }}>
          <div style={{ width: 32, height: 32, borderRadius: 8, background: 'linear-gradient(135deg,#897EFA,#6B39F4)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
            <svg width="18" height="18" viewBox="0 0 18 18" fill="white">
              <path d="M12.448 0L5.552 0L0 5.552L0 12.448L5.552 18L12.448 18L18 12.448L18 5.552L12.448 0ZM6.506 12.535L2.949 8.978L6.506 5.422C7.851 4.077 10.063 4.077 11.407 5.422L14.964 8.978L11.407 12.535C10.063 13.88 7.894 13.88 6.506 12.535Z"/>
            </svg>
          </div>
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ fontFamily: "'Geist','Inter Tight',sans-serif", fontWeight: 700, fontSize: 18, color: '#0D0D12', lineHeight: 1.2 }}>SITKOM</div>
            <div style={{ fontSize: 9, fontWeight: 600, color: '#A4ABB8', lineHeight: 1.3, letterSpacing: '0.03em', textTransform: 'uppercase', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>Sistem Informasi Teknik Komputer</div>
          </div>
          <div style={{ width: 24, height: 24, border: `1px solid ${SITKOM_COLORS.border}`, borderRadius: 6, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', flexShrink: 0 }}>
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="#808897" strokeWidth="1.5"><path d="M7 2L3 6L7 10"/></svg>
          </div>
        </div>
      </div>

      {/* Nav sections */}
      {sections.map((section, si) => (
        <div key={si} style={{ padding: '0 16px', marginTop: si === 0 ? 0 : 4, display: 'flex', flexDirection: 'column', gap: 2 }}>
          <div style={{ fontSize: 11, fontWeight: 500, color: '#A4ABB8', letterSpacing: '0.04em', padding: '4px 12px', textTransform: 'uppercase', marginBottom: 2 }}>{section.title}</div>
          {section.items.map(item => {
            const isActive = activePage === item.id;
            const isDisabled = item.disabled;
            return (
              <div
                key={item.id}
                onClick={() => !isDisabled && onNavigate && onNavigate(item.id)}
                style={{
                  display: 'flex', alignItems: 'center', gap: 10,
                  padding: '10px 12px', borderRadius: 8, cursor: isDisabled ? 'default' : 'pointer',
                  background: isActive ? SITKOM_COLORS.primary : 'transparent',
                  opacity: isDisabled ? 0.5 : 1,
                  transition: 'background 0.12s',
                }}
                onMouseEnter={e => { if (!isActive && !isDisabled) e.currentTarget.style.background = '#F6F8FA'; }}
                onMouseLeave={e => { if (!isActive) e.currentTarget.style.background = 'transparent'; }}
              >
                <item.icon color={isActive ? '#fff' : '#666D80'} size={16} />
                <span style={{ fontSize: 14, fontWeight: isActive ? 600 : 500, color: isActive ? '#fff' : '#353849', flex: 1, letterSpacing: '0.01em', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{item.label}</span>
                {item.badge && (
                  <span style={{ background: isActive ? 'rgba(255,255,255,0.25)' : '#FADAE1', color: isActive ? '#fff' : '#DF1C41', fontSize: 11, fontWeight: 600, padding: '2px 7px', borderRadius: 9999 }}>{item.badge}</span>
                )}
              </div>
            );
          })}
          {si < sections.length - 1 && <div style={{ height: 1, background: '#F0F1F4', margin: '6px 0' }}></div>}
        </div>
      ))}

      {/* Bottom user */}
      <div style={{ marginTop: 'auto', padding: '0 16px' }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '10px 12px', borderRadius: 8, cursor: 'pointer', borderTop: `1px solid ${SITKOM_COLORS.border}`, paddingTop: 16, marginTop: 8 }}>
          <div style={{ width: 32, height: 32, borderRadius: '50%', background: 'linear-gradient(135deg,#897EFA,#6B39F4)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontWeight: 700, fontSize: 12, flexShrink: 0 }}>AM</div>
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: '#0D0D12', lineHeight: 1.2 }}>Admin SITKOM</div>
            <div style={{ fontSize: 11, color: '#808897' }}>admin@undip.ac.id</div>
          </div>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" strokeWidth="1.5"><path d="M9 18l6-6-6-6"/></svg>
        </div>
      </div>
    </div>
  );
}

// ── TopBar ─────────────────────────────────────────────────────
function TopBar({ title }) {
  return (
    <div style={{ height: 53, borderBottom: `1px solid ${SITKOM_COLORS.border}`, display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '0 24px', background: '#fff', flexShrink: 0 }}>
      <div style={{ fontFamily: "'Inter Tight',sans-serif", fontWeight: 700, fontSize: 16, color: '#0D0D12' }}>{title}</div>
      <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
        <div style={{ width: 36, height: 36, borderRadius: 8, border: `1px solid ${SITKOM_COLORS.border}`, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', position: 'relative' }}>
          <IconBell color="#666D80" size={16} />
          <span style={{ position: 'absolute', top: 8, right: 8, width: 7, height: 7, background: '#DF1C41', borderRadius: '50%', border: '1.5px solid #fff' }}></span>
        </div>
        <div style={{ width: 36, height: 36, borderRadius: 8, border: `1px solid ${SITKOM_COLORS.border}`, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer' }}>
          <IconSearch color="#666D80" size={16} />
        </div>
        <div style={{ width: 32, height: 32, borderRadius: '50%', background: 'linear-gradient(135deg,#897EFA,#6B39F4)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontWeight: 700, fontSize: 12, cursor: 'pointer' }}>AM</div>
      </div>
    </div>
  );
}

// ── StatCard ───────────────────────────────────────────────────
function StatCard({ label, value, sub, trend, trendUp, color }) {
  return (
    <div style={{ background: '#fff', border: `1px solid ${SITKOM_COLORS.border}`, borderRadius: 14, padding: '20px', flex: 1, display: 'flex', flexDirection: 'column', gap: 8, boxShadow: '0px 1px 2px rgba(228,229,231,0.24)' }}>
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
        <span style={{ fontSize: 13, fontWeight: 500, color: '#666D80', letterSpacing: '0.01em' }}>{label}</span>
        <div style={{ width: 32, height: 32, borderRadius: 8, background: color || SITKOM_COLORS.primarySubtle, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke={color ? '#fff' : SITKOM_COLORS.primary} strokeWidth="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
      </div>
      <div style={{ fontSize: 28, fontWeight: 700, color: '#0D0D12', lineHeight: 1.1 }}>{value}</div>
      {trend && (
        <div style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
          <span style={{ fontSize: 12, fontWeight: 600, color: trendUp ? '#40C4AA' : '#DF1C41' }}>
            {trendUp ? '↑' : '↓'} {trend}
          </span>
          <span style={{ fontSize: 12, color: '#808897' }}>{sub}</span>
        </div>
      )}
    </div>
  );
}

// ── Mini Icons ─────────────────────────────────────────────────
function IconGrid({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>;
}
function IconLayers({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>;
}
function IconUsers({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>;
}
function IconClipboard({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>;
}
function IconFile({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13,2 13,9 20,9"/></svg>;
}
function IconGear({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>;
}
function IconBarChart({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>;
}
function IconBell({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>;
}
function IconSearch({ color = '#666D80', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>;
}
function IconPlus({ color = '#fff', size = 16 }) {
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2" strokeLinecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>;
}

Object.assign(window, {
  Sidebar, TopBar, StatCard,
  SITKOM_COLORS,
  IconGrid, IconLayers, IconUsers, IconClipboard, IconFile, IconGear, IconBarChart, IconBell, IconSearch, IconPlus
});
