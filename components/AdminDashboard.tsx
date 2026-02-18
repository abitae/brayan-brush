import React, { useState, useEffect } from 'react';
import { uploadLogo, uploadBanner, uploadBannerBg, updateConfig } from '../apiClient';

interface AdminProps {
  config: any;
  onUpdateConfig: (c: any) => void;
  services: any[];
  onUpdateServices: (s: any[]) => void;
  prohibitedItems: any[];
  onUpdateProhibited: (p: any[]) => void;
  quotes: any[];
}

const AdminDashboard: React.FC<AdminProps> = ({ config, onUpdateConfig, services, onUpdateServices, prohibitedItems, onUpdateProhibited, quotes }) => {
  const [activeTab, setActiveTab] = useState('branding');
  const [localConfig, setLocalConfig] = useState(config);
  const [localProhibited, setLocalProhibited] = useState(prohibitedItems);
  const [uploading, setUploading] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    setLocalConfig(config);
  }, [config]);
  useEffect(() => {
    setLocalProhibited(prohibitedItems);
  }, [prohibitedItems]);

  const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>, type: 'logo' | 'banner' | 'banner_bg') => {
    const file = e.target.files?.[0];
    if (!file) return;
    setUploading(type);
    try {
      const res = type === 'logo' ? await uploadLogo(file) : type === 'banner' ? await uploadBanner(file) : await uploadBannerBg(file);
      const url = res?.url ?? null;
      if (type === 'logo') setLocalConfig((c: any) => ({ ...c, logo_url: url }));
      else if (type === 'banner') setLocalConfig((c: any) => ({ ...c, banner_url: url }));
      else setLocalConfig((c: any) => ({ ...c, banner_bg_url: url }));
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al subir el archivo.');
    } finally {
      setUploading(null);
    }
    e.target.value = '';
  };

  const saveBranding = async () => {
    setSaving(true);
    try {
      await updateConfig(localConfig);
      onUpdateConfig(localConfig);
      alert('Configuración guardada en la API.');
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al guardar.');
    } finally {
      setSaving(false);
    }
  };

  const handleUpdateCategoryTitle = (idx: number, title: string) => {
    const newProhibited = [...localProhibited];
    newProhibited[idx].title = title;
    setLocalProhibited(newProhibited);
  };

  const handleAddItemToCategory = (idx: number) => {
    const newProhibited = [...localProhibited];
    newProhibited[idx].items.push('Nuevo artículo');
    setLocalProhibited(newProhibited);
  };

  const saveProhibited = () => {
    onUpdateProhibited(localProhibited);
    alert('Lista de prohibiciones actualizada.');
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 p-6 md:p-12 pt-28">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col md:flex-row gap-12">
          {/* Sidebar Admin */}
          <aside className="md:w-64 space-y-2">
            <h2 className="text-xs font-black text-slate-500 uppercase tracking-widest mb-6">Administración Web</h2>
            {[
              { id: 'branding', label: '🎨 Branding & Media' },
              { id: 'servicios', label: '🛠️ Servicios' },
              { id: 'prohibiciones', label: '🚫 Prohibiciones' },
              { id: 'cotizaciones', label: '💰 Reservas/Cotizaciones' },
            ].map(tab => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`w-full text-left px-4 py-3 rounded-xl font-bold transition-all ${
                  activeTab === tab.id ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-900'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </aside>

          {/* Main Panel Content */}
          <main className="flex-grow bg-slate-900/50 border border-slate-800 rounded-[40px] p-8 md:p-12 backdrop-blur-sm">
            
            {activeTab === 'branding' && (
              <div className="space-y-10 animate-in fade-in duration-500">
                <h3 className="text-2xl font-black mb-8">Gestión de Identidad</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                  <div className="space-y-6">
                    <div className="space-y-2">
                        <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Logo (cabecera)</label>
                        <div className="flex items-center gap-4 bg-slate-800 p-4 rounded-2xl border-2 border-dashed border-slate-700">
                           {localConfig.logo_url && <img src={localConfig.logo_url} className="h-10 w-auto rounded max-h-12 object-contain" alt="Logo" />}
                           <input type="file" accept=".png,.jpg,.jpeg,.webp" onChange={(e) => handleFileUpload(e, 'logo')} className="text-xs text-slate-400" disabled={!!uploading} />
                           {uploading === 'logo' && <span className="text-xs text-slate-400">Subiendo…</span>}
                        </div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Banner (imagen principal hero)</label>
                        <div className="bg-slate-800 p-4 rounded-2xl border-2 border-dashed border-slate-700">
                           {localConfig.banner_url && <img src={localConfig.banner_url} className="w-full h-32 object-cover rounded-lg mb-2" alt="Banner" />}
                           <input type="file" accept=".png,.jpg,.jpeg,.webp" onChange={(e) => handleFileUpload(e, 'banner')} className="w-full text-xs text-slate-400" disabled={!!uploading} />
                           {uploading === 'banner' && <span className="text-xs text-slate-400">Subiendo…</span>}
                        </div>
                    </div>
                    <div className="space-y-2">
                        <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Fondo hero</label>
                        <div className="bg-slate-800 p-4 rounded-2xl border-2 border-dashed border-slate-700">
                           {localConfig.banner_bg_url && <img src={localConfig.banner_bg_url} className="w-full h-24 object-cover rounded-lg mb-2" alt="Fondo" />}
                           <input type="file" accept=".png,.jpg,.jpeg,.webp" onChange={(e) => handleFileUpload(e, 'banner_bg')} className="w-full text-xs text-slate-400" disabled={!!uploading} />
                           {uploading === 'banner_bg' && <span className="text-xs text-slate-400">Subiendo…</span>}
                        </div>
                    </div>
                  </div>
                  <div className="space-y-6">
                    <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Nombre de la empresa</label>
                    <input 
                      type="text" 
                      value={localConfig.company_name || ''}
                      onChange={e => setLocalConfig({...localConfig, company_name: e.target.value})}
                      placeholder="Ej. Brayan Brush"
                      className="w-full bg-slate-800 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                    />
                    <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Texto bajo el logo</label>
                    <input 
                      type="text" 
                      value={localConfig.logo_text || ''}
                      onChange={e => setLocalConfig({...localConfig, logo_text: e.target.value})}
                      placeholder="Ej. Corporación Logística"
                      className="w-full bg-slate-800 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                    />
                    <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Título del hero</label>
                    <input 
                      type="text" 
                      value={localConfig.hero_title || ''}
                      onChange={e => setLocalConfig({...localConfig, hero_title: e.target.value})}
                      placeholder="Ej. Logística Inteligente."
                      className="w-full bg-slate-800 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                    />
                    <label className="text-xs font-bold text-slate-500 uppercase tracking-widest">Subtítulo del hero</label>
                    <input 
                      type="text" 
                      value={localConfig.hero_subtitle || ''}
                      onChange={e => setLocalConfig({...localConfig, hero_subtitle: e.target.value})}
                      placeholder="Ej. Especialistas en transporte..."
                      className="w-full bg-slate-800 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                    />
                  </div>
                </div>
                <p className="text-xs text-slate-500">Sube las imágenes, revisa la vista previa y pulsa &quot;Guardar Cambios&quot; para que se actualicen en la web.</p>
                <button onClick={saveBranding} disabled={saving} className="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold disabled:opacity-50">
                  {saving ? 'Guardando…' : 'Guardar Cambios'}
                </button>
              </div>
            )}

            {activeTab === 'prohibiciones' && (
              <div className="space-y-10 animate-in fade-in duration-500">
                <div className="flex justify-between items-center">
                  <h3 className="text-2xl font-black">Editor de Artículos Prohibidos</h3>
                  <button onClick={saveProhibited} className="bg-rose-600 text-white px-6 py-2 rounded-xl font-bold">Publicar Prohibiciones</button>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  {localProhibited.map((cat, catIdx) => (
                    <div key={catIdx} className="bg-slate-800 p-6 rounded-3xl border border-slate-700">
                      <input 
                        className="bg-transparent text-xl font-black text-rose-400 w-full mb-4 border-b border-slate-700 pb-2 outline-none"
                        value={cat.title}
                        onChange={(e) => handleUpdateCategoryTitle(catIdx, e.target.value)}
                      />
                      <ul className="space-y-2">
                        {cat.items.map((item, itemIdx) => (
                          <li key={itemIdx} className="flex gap-2">
                            <input 
                              className="bg-slate-900 border-none rounded-lg px-3 py-2 text-xs flex-grow outline-none focus:ring-1 focus:ring-rose-500"
                              value={item}
                              onChange={(e) => {
                                const newP = [...localProhibited];
                                newP[catIdx].items[itemIdx] = e.target.value;
                                setLocalProhibited(newP);
                              }}
                            />
                            <button className="text-rose-500 font-bold px-2">✕</button>
                          </li>
                        ))}
                      </ul>
                      <button 
                        onClick={() => handleAddItemToCategory(catIdx)}
                        className="mt-4 text-[10px] font-black uppercase text-emerald-400 hover:underline"
                      >
                        + Agregar Ítem
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeTab === 'cotizaciones' && (
              <div className="space-y-8 animate-in fade-in duration-500">
                <h3 className="text-2xl font-black">Reservas Recibidas</h3>
                {quotes.length === 0 ? (
                    <div className="text-center py-20 bg-slate-800/20 rounded-[40px] border border-slate-800">
                        <p className="text-slate-500">No hay nuevas solicitudes de ruta.</p>
                    </div>
                ) : (
                    <div className="grid gap-4">
                        {quotes.map((q, i) => (
                            <div key={i} className="bg-slate-800 p-6 rounded-3xl border border-slate-700">
                                <div className="flex justify-between items-start">
                                    <div>
                                        <p className="font-bold text-lg">{q.nombre}</p>
                                        <p className="text-xs text-emerald-400 font-bold">{q.servicio}</p>
                                        <p className="text-xs text-slate-400 mt-2">{q.mensaje}</p>
                                        <p className="text-xs text-slate-300 mt-1">📞 {q.telefono}</p>
                                    </div>
                                    <span className="text-[10px] bg-emerald-950 px-3 py-1 rounded-full text-emerald-400 uppercase font-black">Nueva</span>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
              </div>
            )}
          </main>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;
