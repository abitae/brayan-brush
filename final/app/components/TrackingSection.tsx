
import React, { useState } from 'react';
import { ICONS } from '../constants';

const TrackingSection: React.FC = () => {
  const [trackingId, setTrackingId] = useState('');
  const [result, setResult] = useState<any | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const handleTrack = () => {
    if (!trackingId) return;
    setIsLoading(true);
    setTimeout(() => {
      setResult({
        id: trackingId,
        status: 'in_transit',
        currentLocation: 'Centro Distribución - Huachipa, Lima',
        origin: 'Puerto del Callao',
        destination: 'Sede Central Arequipa',
        estimatedDelivery: 'Mañana, 09:00 AM',
        progress: 72,
        history: [
          { date: '15 Oct, 08:30', location: 'Callao', desc: 'Paquete recolectado y procesado' },
          { date: '15 Oct, 22:00', location: 'Lima Norte', desc: 'Salida de centro de consolidación' },
          { date: '16 Oct, 11:15', location: 'Huachipa', desc: 'En ruta hacia destino final' },
        ]
      });
      setIsLoading(false);
    }, 1200);
  };

  return (
    <section className="py-24 bg-white relative">
      <div className="max-w-6xl mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Rastreo de <span className="text-emerald-600 font-black">Carga</span></h2>
          <p className="text-slate-500 text-lg">Monitoreo GPS en tiempo real de tu envío nacional.</p>
        </div>

        <div className="bg-slate-50 rounded-[40px] p-10 md:p-14 border border-slate-100 shadow-2xl shadow-slate-200/50 mb-12">
          <div className="flex flex-col md:flex-row gap-6">
            <div className="relative flex-grow group">
              <div className="absolute inset-y-0 left-6 flex items-center text-slate-300 group-focus-within:text-emerald-500 transition-colors">
                <ICONS.Search />
              </div>
              <input
                type="text"
                value={trackingId}
                onChange={(e) => setTrackingId(e.target.value.toUpperCase())}
                placeholder="NÚMERO DE GUÍA (EX: BB-2024-XXXX)"
                className="w-full pl-16 pr-6 py-6 bg-white border-2 border-transparent focus:border-emerald-500 rounded-3xl outline-none shadow-sm font-black tracking-widest transition-all placeholder:text-slate-300 placeholder:tracking-normal text-lg"
              />
            </div>
            <button
              onClick={handleTrack}
              disabled={isLoading}
              className="bg-emerald-600 text-white px-12 py-6 rounded-3xl font-black text-lg hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 flex items-center justify-center gap-3"
            >
              {isLoading ? (
                <div className="w-6 h-6 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
              ) : (
                'Localizar Ahora'
              )}
            </button>
          </div>
        </div>

        {result && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 animate-in fade-in slide-in-from-bottom-8 duration-700">
            {/* Status Card */}
            <div className="lg:col-span-8 bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
               <div className="bg-slate-900 p-10 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                  <div>
                    <p className="text-emerald-500 font-black text-[10px] uppercase tracking-[0.3em] mb-2">Guía de Transporte</p>
                    <h3 className="text-3xl font-black tracking-tighter">{result.id}</h3>
                  </div>
                  <div className="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/5">
                    <p className="text-emerald-200 font-black text-[10px] uppercase tracking-[0.2em] mb-1">Estado Actual</p>
                    <p className="text-xl font-black">EN TRÁNSITO NACIONAL</p>
                  </div>
               </div>

               <div className="p-10">
                  <div className="flex justify-between items-center mb-16 relative">
                    <div className="absolute top-1/2 left-0 right-0 h-1.5 bg-slate-100 -translate-y-1/2 z-0 rounded-full"></div>
                    <div 
                        className="absolute top-1/2 left-0 h-1.5 bg-emerald-500 -translate-y-1/2 z-0 rounded-full transition-all duration-1000" 
                        style={{ width: `${result.progress}%` }}
                    ></div>
                    
                    <div className="z-10 text-center">
                        <div className="w-16 h-16 bg-white border-4 border-emerald-500 rounded-full flex items-center justify-center text-emerald-600 shadow-lg mx-auto mb-3">
                           <ICONS.Box />
                        </div>
                        <p className="text-xs font-black text-slate-900 uppercase tracking-tighter">{result.origin}</p>
                    </div>

                    <div className="z-10 text-center">
                        <div className="w-16 h-16 bg-white border-4 border-slate-100 rounded-full flex items-center justify-center text-slate-300 shadow-sm mx-auto mb-3">
                           <ICONS.MapPin />
                        </div>
                        <p className="text-xs font-black text-slate-400 uppercase tracking-tighter">{result.destination}</p>
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-slate-50">
                    <div className="bg-slate-50 p-6 rounded-3xl">
                        <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ubicación Actual</p>
                        <p className="font-bold text-slate-800 flex items-center gap-2">
                           <span className="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                           {result.currentLocation}
                        </p>
                    </div>
                    <div className="bg-emerald-50 p-6 rounded-3xl">
                        <p className="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Llegada Estimada</p>
                        <p className="font-black text-emerald-900">{result.estimatedDelivery}</p>
                    </div>
                  </div>
               </div>
            </div>

            {/* History Card */}
            <div className="lg:col-span-4 bg-slate-50 rounded-[40px] p-10 border border-slate-100">
               <h4 className="font-black text-slate-900 uppercase tracking-widest text-xs mb-10 pb-4 border-b border-slate-200">Cronograma de Ruta</h4>
               <div className="space-y-10">
                  {result.history.map((step: any, idx: number) => (
                    <div key={idx} className="flex gap-6 relative group">
                        <div className="relative z-10">
                           <div className={`w-4 h-4 rounded-full mt-1 border-4 ${idx === result.history.length - 1 ? 'bg-emerald-500 border-emerald-100' : 'bg-slate-300 border-white'}`}></div>
                           {idx < result.history.length - 1 && (
                             <div className="absolute top-5 left-[7px] bottom-[-40px] w-0.5 bg-slate-200"></div>
                           )}
                        </div>
                        <div>
                           <p className="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{step.date}</p>
                           <p className="font-black text-slate-800 text-sm mb-1">{step.location}</p>
                           <p className="text-xs text-slate-500 font-medium leading-relaxed">{step.desc}</p>
                        </div>
                    </div>
                  ))}
               </div>
            </div>
          </div>
        )}
      </div>
    </section>
  );
};

export default TrackingSection;
