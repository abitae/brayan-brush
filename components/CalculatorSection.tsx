
import React, { useState, useEffect } from 'react';
import { ICONS } from '../constants';

interface CalculatorProps {
  onQuoteSubmit?: (quote: any) => void;
}

const CalculatorSection: React.FC<CalculatorProps> = ({ onQuoteSubmit }) => {
  const [calcMode, setCalcMode] = useState<'weight' | 'dimensions'>('weight');
  const [weight, setWeight] = useState(5);
  const [length, setLength] = useState(30);
  const [width, setWidth] = useState(30);
  const [height, setHeight] = useState(30);
  const [origin, setOrigin] = useState('Lima');
  const [destination, setDestination] = useState('Arequipa');
  const [serviceType, setServiceType] = useState('standard'); // standard, express
  const [result, setResult] = useState<{ total: number; volumetric: number; charged: number }>({ total: 0, volumetric: 0, charged: 0 });

  // Booking Modal States
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [bookingName, setBookingName] = useState('');
  const [bookingPhone, setBookingPhone] = useState('');
  const [bookingEmail, setBookingEmail] = useState('');
  const [bookingDetails, setBookingDetails] = useState('');
  const [isSuccess, setIsSuccess] = useState(false);

  useEffect(() => {
    // Road freight volumetric factor (standard in Peru is approx 5000 or 4000)
    const factor = 5000;
    const volumetricWeight = (length * width * height) / factor;
    const chargedWeight = calcMode === 'weight' ? weight : Math.max(weight, volumetricWeight);

    // Base rates per route (simplified simulation)
    const baseRates: Record<string, number> = {
      'Lima-Arequipa': 1.5,
      'Lima-Trujillo': 1.2,
      'Lima-Cusco': 1.8,
      'Lima-Piura': 2.0,
      'Default': 1.4
    };

    const routeKey = `${origin}-${destination}`;
    const rate = baseRates[routeKey] || baseRates['Default'];
    const serviceMultiplier = serviceType === 'express' ? 1.5 : 1;
    
    const baseFee = 25; // Tarifa mínima administrativa
    const total = baseFee + (chargedWeight * rate * serviceMultiplier);

    setResult({
      total: parseFloat(total.toFixed(2)),
      volumetric: parseFloat(volumetricWeight.toFixed(2)),
      charged: parseFloat(chargedWeight.toFixed(2))
    });
  }, [weight, length, width, height, origin, destination, serviceType, calcMode]);

  const handleBookingSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const fullDetails = `Cotización Calculada: S/ ${result.total}. Ruta: ${origin} a ${destination}. Peso: ${result.charged}kg. Detalles: ${bookingDetails}`;
    
    if (onQuoteSubmit) {
      onQuoteSubmit({
        nombre: bookingName,
        email: bookingEmail || 'reserva@brayanbrush.com',
        telefono: bookingPhone,
        servicio: `Reserva - ${serviceType.toUpperCase()}`,
        mensaje: fullDetails
      });
    }

    setIsSuccess(true);
    setTimeout(() => {
      setIsSuccess(false);
      setIsModalOpen(false);
      // Reset form
      setBookingName('');
      setBookingPhone('');
      setBookingDetails('');
      setBookingEmail('');
    }, 4000);
  };

  return (
    <section className="py-32 bg-slate-50 relative overflow-hidden" id="cotizador">
      {/* Decorative Elements */}
      <div className="absolute top-0 right-0 w-96 h-96 bg-emerald-500/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
      <div className="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/5 rounded-full -ml-48 -mb-48 blur-3xl"></div>

      <div className="max-w-7xl mx-auto px-4 relative z-10">
        <div className="text-center mb-20">
          <span className="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-black uppercase tracking-widest mb-4">Cotizador Inteligente</span>
          <h2 className="text-5xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight">Calcula el costo de tu <span className="text-emerald-600">envío</span></h2>
          <p className="text-slate-500 max-w-2xl mx-auto text-lg">Obtén una tarifa inmediata basada en peso real o dimensiones volumétricas según estándares internacionales de logística.</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
          {/* Form Side */}
          <div className="lg:col-span-7 bg-white rounded-[48px] p-8 md:p-12 shadow-2xl shadow-slate-200/50 border border-slate-100">
            <div className="space-y-10">
              {/* Route Selection */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-3">
                  <label className="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Origen</label>
                  <select 
                    value={origin}
                    onChange={(e) => setOrigin(e.target.value)}
                    className="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none font-bold transition-all appearance-none cursor-pointer"
                  >
                    <option>Lima</option>
                    <option>Callao</option>
                    <option>Trujillo</option>
                  </select>
                </div>
                <div className="space-y-3">
                  <label className="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Destino</label>
                  <select 
                    value={destination}
                    onChange={(e) => setDestination(e.target.value)}
                    className="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none font-bold transition-all appearance-none cursor-pointer"
                  >
                    <option>Arequipa</option>
                    <option>Cusco</option>
                    <option>Piura</option>
                    <option>Trujillo</option>
                  </select>
                </div>
              </div>

              {/* Toggle Weight/Dimensions */}
              <div>
                <label className="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 block ml-1">Método de Cálculo</label>
                <div className="flex p-1.5 bg-slate-100 rounded-2xl w-full">
                  <button 
                    onClick={() => setCalcMode('weight')}
                    className={`flex-1 flex items-center justify-center gap-2 py-4 rounded-xl font-black text-sm transition-all ${calcMode === 'weight' ? 'bg-white text-emerald-600 shadow-xl' : 'text-slate-500 hover:text-slate-700'}`}
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                    Por Peso Real
                  </button>
                  <button 
                    onClick={() => setCalcMode('dimensions')}
                    className={`flex-1 flex items-center justify-center gap-2 py-4 rounded-xl font-black text-sm transition-all ${calcMode === 'dimensions' ? 'bg-white text-emerald-600 shadow-xl' : 'text-slate-500 hover:text-slate-700'}`}
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Por Volumen
                  </button>
                </div>
              </div>

              {/* Dynamic Inputs */}
              <div className="animate-in fade-in slide-in-from-bottom-4 duration-500">
                {calcMode === 'weight' ? (
                  <div className="space-y-4">
                    <div className="flex justify-between items-end">
                      <label className="text-sm font-bold text-slate-700">Peso estimado del paquete</label>
                      <span className="text-3xl font-black text-emerald-600">{weight} <small className="text-sm font-bold text-slate-400 uppercase">KG</small></span>
                    </div>
                    <input
                      type="range"
                      min="1"
                      max="500"
                      value={weight}
                      onChange={(e) => setWeight(parseInt(e.target.value))}
                      className="w-full h-2.5 bg-slate-100 rounded-full appearance-none cursor-pointer accent-emerald-600"
                    />
                    <div className="flex justify-between text-[10px] font-black text-slate-300 uppercase tracking-widest px-1">
                      <span>1 KG</span>
                      <span>250 KG</span>
                      <span>500 KG</span>
                    </div>
                  </div>
                ) : (
                  <div className="space-y-6">
                    <label className="text-sm font-bold text-slate-700 block">Dimensiones en centímetros (cm)</label>
                    <div className="grid grid-cols-3 gap-6">
                      <div className="space-y-2">
                        <input type="number" value={length} onChange={(e) => setLength(Number(e.target.value))} className="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-4 font-black text-lg text-center focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none" />
                        <p className="text-[10px] text-center font-black text-slate-400 uppercase tracking-widest">Largo</p>
                      </div>
                      <div className="space-y-2">
                        <input type="number" value={width} onChange={(e) => setWidth(Number(e.target.value))} className="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-4 font-black text-lg text-center focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none" />
                        <p className="text-[10px] text-center font-black text-slate-400 uppercase tracking-widest">Ancho</p>
                      </div>
                      <div className="space-y-2">
                        <input type="number" value={height} onChange={(e) => setHeight(Number(e.target.value))} className="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-4 font-black text-lg text-center focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none" />
                        <p className="text-[10px] text-center font-black text-slate-400 uppercase tracking-widest">Alto</p>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Service Selection */}
              <div className="flex gap-4">
                <button 
                  onClick={() => setServiceType('standard')}
                  className={`flex-1 p-5 rounded-3xl border-2 transition-all ${serviceType === 'standard' ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-100 bg-white'}`}
                >
                  <p className="font-black text-slate-900 mb-1">Económico</p>
                  <p className="text-xs text-slate-500 font-medium">3 a 5 días hábiles</p>
                </button>
                <button 
                  onClick={() => setServiceType('express')}
                  className={`flex-1 p-5 rounded-3xl border-2 transition-all ${serviceType === 'express' ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-100 bg-white'}`}
                >
                  <p className="font-black text-slate-900 mb-1">Express 🚀</p>
                  <p className="text-xs text-slate-500 font-medium">24 a 48 horas</p>
                </button>
              </div>
            </div>
          </div>

          {/* Result Side */}
          <div className="lg:col-span-5 space-y-8 lg:sticky lg:top-32">
            <div className="bg-slate-900 rounded-[48px] p-10 text-white shadow-2xl relative overflow-hidden group">
              <div className="absolute top-0 right-0 w-32 h-32 bg-emerald-600/20 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-emerald-600/40 transition-colors"></div>
              
              <h3 className="text-xl font-black mb-10 border-b border-white/10 pb-6 uppercase tracking-widest flex items-center justify-between">
                Resumen de Tarifa
                <ICONS.Container />
              </h3>

              <div className="space-y-6 mb-12">
                <div className="flex justify-between items-center text-slate-400 font-bold">
                  <span>Peso Base para Cobro:</span>
                  <span className="text-white">{result.charged} KG</span>
                </div>
                {calcMode === 'dimensions' && (
                  <div className="flex justify-between items-center text-slate-500 text-xs font-bold italic">
                    <span>* Peso Volumétrico calculado:</span>
                    <span>{result.volumetric} KG</span>
                  </div>
                )}
                <div className="flex justify-between items-center text-slate-400 font-bold">
                  <span>Ruta:</span>
                  <span className="text-white">{origin} → {destination}</span>
                </div>
                <div className="flex justify-between items-center text-slate-400 font-bold">
                  <span>Servicio:</span>
                  <span className="text-emerald-400 uppercase text-xs">{serviceType === 'express' ? 'Prioritario' : 'Estándar'}</span>
                </div>
              </div>

              <div className="space-y-1">
                <span className="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] ml-1">Total Estimado</span>
                <div className="flex items-baseline gap-2">
                  <span className="text-7xl font-black tracking-tighter">S/ {result.total}</span>
                </div>
                <p className="text-[10px] text-slate-500 font-medium leading-relaxed mt-4">
                  * Tarifa aproximada sujeta a verificación física del paquete al momento del recojo. Incluye IGV y seguros básicos.
                </p>
              </div>

              <button 
                className="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-6 rounded-3xl font-black text-lg mt-12 transition-all transform hover:-translate-y-1 active:scale-95 shadow-xl shadow-emerald-900/50"
                onClick={() => setIsModalOpen(true)}
              >
                Reservar Envío Ahora
              </button>
            </div>

            {/* Support Box */}
            <div className="bg-emerald-50 rounded-[40px] p-8 border border-emerald-100 flex items-center gap-6 group hover:bg-emerald-100 transition-all cursor-pointer">
              <div className="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm group-hover:rotate-12 transition-transform">
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
              </div>
              <div>
                <p className="font-black text-slate-900 uppercase text-xs tracking-widest mb-1">¿Carga Especial?</p>
                <p className="text-sm text-slate-500 font-medium">Cotiza volúmenes industriales con un asesor humano en tiempo real.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Booking Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-[110] flex items-center justify-center px-4">
          <div 
            className="absolute inset-0 bg-slate-900/80 backdrop-blur-md animate-in fade-in duration-300"
            onClick={() => !isSuccess && setIsModalOpen(false)}
          ></div>
          
          <div className="bg-white rounded-[40px] w-full max-w-lg p-8 md:p-12 relative z-10 shadow-3xl animate-in zoom-in slide-in-from-bottom-12 duration-500">
            {isSuccess ? (
              <div className="text-center py-16 animate-in fade-in zoom-in duration-500">
                <div className="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-8 animate-bounce">
                  ✓
                </div>
                <h3 className="text-3xl font-black text-slate-900 mb-4">¡Reserva Registrada!</h3>
                <p className="text-slate-500 text-lg">Un asesor validará tu cotización de S/ {result.total} y te contactará en breve.</p>
              </div>
            ) : (
              <>
                <div className="flex justify-between items-start mb-10">
                  <div>
                    <h3 className="text-3xl font-black text-slate-900 tracking-tight">Finalizar Reserva</h3>
                    <p className="text-slate-500 font-medium mt-1">Completa tus datos para agendar el envío.</p>
                  </div>
                  <button onClick={() => setIsModalOpen(false)} className="text-slate-300 hover:text-slate-900 transition-colors p-2 bg-slate-50 rounded-full">
                    ✕
                  </button>
                </div>

                <div className="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100 flex justify-between items-center">
                   <div>
                     <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Resumen de Envío</p>
                     <p className="font-black text-slate-900 text-lg">{origin} → {destination}</p>
                   </div>
                   <div className="text-right">
                     <p className="text-emerald-600 font-black text-2xl tracking-tighter">S/ {result.total}</p>
                     <p className="text-[10px] font-bold text-slate-400 uppercase">{serviceType}</p>
                   </div>
                </div>

                <form onSubmit={handleBookingSubmit} className="space-y-6">
                  <div className="space-y-2">
                    <label className="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Nombre Completo</label>
                    <input 
                      required
                      type="text"
                      placeholder="Ej: Juan Pérez"
                      value={bookingName}
                      onChange={e => setBookingName(e.target.value)}
                      className="w-full bg-slate-50 border-2 border-transparent focus:border-emerald-500 rounded-2xl px-6 py-4 outline-none font-bold transition-all"
                    />
                  </div>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <label className="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Teléfono</label>
                      <input 
                        required
                        type="tel"
                        placeholder="Ej: 987 654 321"
                        value={bookingPhone}
                        onChange={e => setBookingPhone(e.target.value)}
                        className="w-full bg-slate-50 border-2 border-transparent focus:border-emerald-500 rounded-2xl px-6 py-4 outline-none font-bold transition-all"
                      />
                    </div>
                    <div className="space-y-2">
                      <label className="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Correo (Opcional)</label>
                      <input 
                        type="email"
                        placeholder="tu@email.com"
                        value={bookingEmail}
                        onChange={e => setBookingEmail(e.target.value)}
                        className="w-full bg-slate-50 border-2 border-transparent focus:border-emerald-500 rounded-2xl px-6 py-4 outline-none font-bold transition-all"
                      />
                    </div>
                  </div>
                  <div className="space-y-2">
                    <label className="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Detalles de la Carga</label>
                    <textarea 
                      rows={3}
                      placeholder="Ej: Mercadería frágil, requiere estiba..."
                      value={bookingDetails}
                      onChange={e => setBookingDetails(e.target.value)}
                      className="w-full bg-slate-50 border-2 border-transparent focus:border-emerald-500 rounded-2xl px-6 py-4 outline-none font-bold transition-all"
                    />
                  </div>
                  <button 
                    type="submit"
                    className="w-full bg-emerald-600 text-white py-6 rounded-3xl font-black text-xl hover:bg-emerald-500 transition-all shadow-2xl shadow-emerald-500/30 active:scale-95"
                  >
                    Confirmar Reserva
                  </button>
                  <p className="text-center text-[10px] text-slate-400 font-medium">Al confirmar, aceptas nuestros términos y condiciones de transporte nacional.</p>
                </form>
              </>
            )}
          </div>
        </div>
      )}
    </section>
  );
};

export default CalculatorSection;
