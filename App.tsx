import React, { useState, useEffect } from 'react';
import Layout from './components/Layout';
import ContactSection from './components/ContactSection';
import AboutSection from './components/AboutSection';
import AgenciesSection from './components/AgenciesSection';
import CalculatorSection from './components/CalculatorSection';
import TrackingSection from './components/TrackingSection';
import ServicesSection from './components/ServicesSection';
import ComplaintsSection from './components/ComplaintsSection';
import ProhibitedSection from './components/ProhibitedSection';
import AdminDashboard from './components/AdminDashboard';
import SmartAssistant from './components/SmartAssistant';
import { getConfig } from './apiClient';

const INITIAL_CONFIG = {
  company_name: "Brayan Brush",
  logo_text: "Corporación Logística",
  hero_title: "Logística Inteligente.",
  hero_subtitle: "Especialistas en transporte terrestre nacional con la flota más segura del Perú.",
  primary_color: "#059669",
  logo_url: null,
  banner_url: "https://images.unsplash.com/photo-1501700493788-fa1a4fc9fe62?auto=format&fit=crop&q=80&w=1200",
  banner_bg_url: "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=2000"
};

const INITIAL_PROHIBITED = [
  {
    title: 'Materiales Peligrosos',
    items: ['Explosivos y materiales inflamables', 'Sustancias tóxicas o corrosivas', 'Gases comprimidos']
  },
  {
    title: 'Artículos Ilegales',
    items: ['Drogas y estupefacientes', 'Armas y municiones', 'Mercancía de contrabando']
  },
  {
    title: 'Restricciones Especiales',
    items: ['Dinero en efectivo', 'Joyas y valores', 'Documentos confidenciales']
  }
];

const App: React.FC = () => {
  const [activeSection, setActiveSection] = useState('inicio');
  const [config, setConfig] = useState(INITIAL_CONFIG);
  const [prohibitedItems, setProhibitedItems] = useState(INITIAL_PROHIBITED);
  const [services, setServices] = useState([
    { id: 'courier', title: 'Courier Nacional', description: 'Envíos rápidos de sobres y paquetería menor a 10kg.', icon_type: 'Box' },
    { id: 'mudanza', title: 'Mudanza Corporativa', description: 'Traslados integrales con embalaje profesional.', icon_type: 'Home' },
    { id: 'carga', title: 'Carga Pesada', description: 'Logística B2B para envíos de gran tonelaje.', icon_type: 'Package' }
  ]);
  const [quotes, setQuotes] = useState<any[]>([]);

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [activeSection]);

  useEffect(() => {
    getConfig()
      .then((data) => setConfig((prev) => ({ ...prev, ...data })))
      .catch(() => {});
  }, []);

  const handleUpdateConfig = (newConfig: any) => setConfig(newConfig);
  const handleUpdateServices = (newServices: any[]) => setServices(newServices);
  const handleUpdateProhibited = (newProhibited: any[]) => setProhibitedItems(newProhibited);
  const handleAddQuote = (quote: any) => setQuotes([quote, ...quotes]);

  const renderHome = () => (
    <>
      <section className="relative min-h-[95vh] flex items-center overflow-hidden">
        <div className="absolute inset-0 z-0 overflow-hidden">
          {config.banner_bg_url ? (
            <img 
              src={config.banner_bg_url} 
              alt="Fondo Brayan Brush" 
              className="w-full h-full object-cover animate-pulse-slow brightness-[0.9]"
            />
          ) : (
            <div className="w-full h-full bg-slate-900"></div>
          )}
          <div className="absolute inset-0 bg-gradient-to-r from-white via-white/95 to-white/10"></div>
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,transparent_40%,rgba(5,150,105,0.05))]"></div>
        </div>

        <div className="max-w-7xl mx-auto px-4 relative z-10 w-full pt-20">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div className="animate-in fade-in slide-in-from-left-12 duration-1000">
              <div className="inline-flex items-center gap-4 py-2 px-6 rounded-full bg-emerald-100/60 backdrop-blur-xl border border-emerald-200/50 text-emerald-800 font-black text-[10px] uppercase tracking-[0.4em] mb-12 shadow-sm">
                <span className="relative flex h-3 w-3">
                  <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span className="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                Conectando Regiones de Perú
              </div>
              
              <h1 className="text-7xl md:text-[100px] font-black text-slate-900 leading-[0.85] tracking-tighter mb-10">
                {config.company_name} <br />
                <span className="text-transparent bg-clip-text bg-gradient-to-br from-emerald-600 via-emerald-500 to-emerald-400">
                   {config.hero_title}
                </span>
              </h1>
              
              <div className="relative mb-16 pl-8">
                <div className="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-emerald-600 to-emerald-400 rounded-full"></div>
                <p className="text-2xl text-slate-500 leading-relaxed max-w-lg font-semibold italic">
                  "{config.hero_subtitle}"
                </p>
              </div>
              
              <div className="flex flex-col sm:flex-row gap-8 items-center sm:items-start">
                <button 
                  onClick={() => setActiveSection('cotizar')}
                  className="w-full sm:w-auto bg-emerald-600 text-white px-14 py-6 rounded-[32px] font-black text-xl transition-all shadow-[0_30px_60px_-15px_rgba(5,150,105,0.4)] hover:bg-emerald-500 hover:-translate-y-2 active:scale-95 flex items-center justify-center gap-3 group"
                >
                  Cotizar Ahora
                  <span className="group-hover:translate-x-1 transition-transform">→</span>
                </button>
                <div className="flex items-center gap-4">
                    <div className="flex -space-x-3">
                        <img className="w-12 h-12 rounded-full border-4 border-white object-cover" src="https://i.pravatar.cc/100?u=1" alt="Client"/>
                        <img className="w-12 h-12 rounded-full border-4 border-white object-cover" src="https://i.pravatar.cc/100?u=2" alt="Client"/>
                        <img className="w-12 h-12 rounded-full border-4 border-white object-cover" src="https://i.pravatar.cc/100?u=3" alt="Client"/>
                    </div>
                    <div>
                        <p className="text-sm font-black text-slate-900 leading-none mb-1">+500 Clientes</p>
                        <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Confían cada mes</p>
                    </div>
                </div>
              </div>
            </div>

            <div className="relative animate-in fade-in slide-in-from-right-12 duration-1000 delay-300 hidden lg:block">
               <div className="relative z-10 rounded-[100px] overflow-hidden shadow-[0_80px_160px_-40px_rgba(0,0,0,0.4)] group aspect-[4/5] max-w-[550px] ml-auto">
                  <img 
                    src={config.banner_url} 
                    alt="Truck Brayan Brush" 
                    className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
                  <div className="absolute bottom-16 left-16 right-16">
                     <div className="w-20 h-1.5 bg-emerald-500 rounded-full mb-6"></div>
                     <h3 className="text-white text-4xl font-black leading-tight drop-shadow-xl">Infraestructura de Clase Mundial en Carretera.</h3>
                  </div>
               </div>

               <div className="absolute -top-12 -left-16 bg-white p-12 rounded-[56px] shadow-3xl animate-float z-20 border border-slate-100 max-w-[280px]">
                  <div className="flex flex-col gap-6">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                           <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div>
                           <p className="text-2xl font-black text-slate-900 leading-none mb-1">99.8%</p>
                           <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Entregas On-time</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                           <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02(0)003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div>
                           <p className="text-2xl font-black text-slate-900 leading-none mb-1">Full</p>
                           <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Seguro de Carga</p>
                        </div>
                    </div>
                  </div>
               </div>
            </div>
          </div>
        </div>
      </section>

      <div id="servicios"><ServicesSection items={services} /></div>
      <div id="nosotros"><AboutSection companyName={config.company_name} /></div>
      <div id="agencias"><AgenciesSection /></div>
      <div id="contacto-home"><ContactSection onQuoteSubmit={handleAddQuote} /></div>
    </>
  );

  return (
    <Layout 
      activeSection={activeSection} 
      setActiveSection={setActiveSection}
      siteName={config.company_name}
      logoText={config.logo_text}
      logoUrl={config.logo_url}
    >
      {activeSection === 'inicio' && renderHome()}
      {activeSection === 'cotizar' && (
        <div className="min-h-[80vh] py-10 animate-in fade-in duration-500">
          <CalculatorSection onQuoteSubmit={handleAddQuote} />
        </div>
      )}
      {activeSection === 'rastreo' && (
        <div className="min-h-[80vh] py-10 animate-in fade-in duration-500">
          <TrackingSection />
        </div>
      )}
      {activeSection === 'servicios' && <ServicesSection items={services} />}
      {activeSection === 'nosotros' && <AboutSection companyName={config.company_name} />}
      {activeSection === 'agencias' && <AgenciesSection />}
      {activeSection === 'contacto' && <ContactSection onQuoteSubmit={handleAddQuote} />}
      {activeSection === 'prohibiciones' && <ProhibitedSection items={prohibitedItems} onSwitchToContact={() => setActiveSection('contacto')} />}
      {activeSection === 'reclamos' && <ComplaintsSection onSwitchToContact={() => setActiveSection('contacto')} />}
      {activeSection === 'admin' && (
        <AdminDashboard 
          config={config} 
          onUpdateConfig={handleUpdateConfig}
          services={services}
          onUpdateServices={handleUpdateServices}
          prohibitedItems={prohibitedItems}
          onUpdateProhibited={handleUpdateProhibited}
          quotes={quotes}
        />
      )}
      {activeSection !== 'admin' && <SmartAssistant />}
    </Layout>
  );
};

export default App;
