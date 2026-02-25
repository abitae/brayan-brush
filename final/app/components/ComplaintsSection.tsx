
import React, { useState } from 'react';

const ComplaintsSection: React.FC<{ onSwitchToContact: () => void }> = ({ onSwitchToContact }) => {
  const [formData, setFormData] = useState({
    nombre: '',
    documento: '',
    telefono: '',
    email: '',
    direccion: '',
    tipo: 'Queja' as 'Queja' | 'Reclamo',
    detalle: ''
  });
  const [submitted, setSubmitted] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitted(true);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  if (submitted) {
    return (
      <section className="py-24 bg-white flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div className="w-24 h-24 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-5xl mb-8 animate-bounce">
          ✓
        </div>
        <h2 className="text-4xl font-black text-slate-900 mb-4 tracking-tight">Registro Exitoso</h2>
        <p className="text-slate-600 max-w-md mx-auto text-lg mb-10">
          Su reclamo ha sido registrado con el código <span className="font-mono font-bold text-rose-600">LR-{Math.floor(Math.random() * 100000)}</span>. 
          Le responderemos en el plazo establecido por ley.
        </p>
        <button 
          onClick={() => setSubmitted(false)}
          className="bg-slate-900 text-white px-8 py-3 rounded-2xl font-bold hover:bg-slate-800 transition-all"
        >
          Volver al formulario
        </button>
      </section>
    );
  }

  return (
    <section className="py-12 bg-white min-h-screen">
      <div className="max-w-4xl mx-auto px-4">
        {/* Toggle Switcher matching screenshot */}
        <div className="flex justify-center mb-10">
          <div className="inline-flex p-1 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <button 
              onClick={onSwitchToContact}
              className="px-6 py-2 text-sm font-bold text-slate-600 hover:text-emerald-600 transition-all"
            >
              Formulario de Contacto
            </button>
            <button 
              className="px-6 py-2 text-sm font-bold text-white bg-rose-600 rounded-lg shadow-md"
            >
              Libro de Reclamaciones
            </button>
          </div>
        </div>

        {/* Header matching screenshot */}
        <div className="bg-rose-600 text-white rounded-t-[20px] p-8 md:p-10">
          <h2 className="text-3xl font-black mb-2">Libro de Reclamaciones</h2>
          <p className="text-rose-100 opacity-90 text-sm">
            Registra aquí tu reclamo o queja formal. Daremos seguimiento a tu caso según la normativa vigente.
          </p>
        </div>

        {/* Form Content */}
        <div className="bg-white border-x border-b border-slate-100 shadow-2xl rounded-b-[20px] p-8 md:p-12">
          <form onSubmit={handleSubmit} className="space-y-12">
            
            {/* Consumer Data Section */}
            <div>
              <h3 className="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span className="w-1 h-6 bg-rose-600 rounded-full"></span>
                Datos del consumidor
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-2">
                  <label className="text-sm font-bold text-slate-700">Nombre completo *</label>
                  <input 
                    required 
                    type="text"
                    value={formData.nombre}
                    onChange={e => setFormData({...formData, nombre: e.target.value})}
                    placeholder="Tu nombre completo"
                    className="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none transition-all placeholder:text-slate-400"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-bold text-slate-700">DNI/CE/Pasaporte *</label>
                  <input 
                    required 
                    type="text"
                    value={formData.documento}
                    onChange={e => setFormData({...formData, documento: e.target.value})}
                    placeholder="Número de documento"
                    className="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none transition-all placeholder:text-slate-400"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-bold text-slate-700">Teléfono *</label>
                  <input 
                    required 
                    type="tel"
                    value={formData.telefono}
                    onChange={e => setFormData({...formData, telefono: e.target.value})}
                    placeholder="Tu número telefónico"
                    className="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none transition-all placeholder:text-slate-400"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-bold text-slate-700">Correo electrónico *</label>
                  <input 
                    required 
                    type="email"
                    value={formData.email}
                    onChange={e => setFormData({...formData, email: e.target.value})}
                    placeholder="ejemplo@correo.com"
                    className="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none transition-all placeholder:text-slate-400"
                  />
                </div>
              </div>
              <div className="mt-8 space-y-2">
                <label className="text-sm font-bold text-slate-700">Dirección *</label>
                <input 
                  required 
                  type="text"
                  value={formData.direccion}
                  onChange={e => setFormData({...formData, direccion: e.target.value})}
                  placeholder="Tu dirección completa"
                  className="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none transition-all placeholder:text-slate-400"
                />
              </div>
            </div>

            {/* Complaint Details Section */}
            <div>
              <h3 className="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span className="w-1 h-6 bg-rose-600 rounded-full"></span>
                Detalles de la reclamación
              </h3>
              
              <div className="space-y-6">
                <div className="space-y-3">
                  <label className="text-sm font-bold text-slate-700">Tipo *</label>
                  <div className="flex gap-10">
                    <label className="flex items-center gap-3 cursor-pointer group">
                      <div className="relative flex items-center justify-center">
                        <input 
                          type="radio" 
                          name="tipo" 
                          checked={formData.tipo === 'Queja'}
                          onChange={() => setFormData({...formData, tipo: 'Queja'})}
                          className="w-5 h-5 appearance-none border-2 border-slate-300 rounded-full checked:border-rose-600 transition-all cursor-pointer"
                        />
                        <div className={`absolute w-2.5 h-2.5 bg-rose-600 rounded-full transition-all scale-0 ${formData.tipo === 'Queja' ? 'scale-100' : ''}`}></div>
                      </div>
                      <span className="text-sm font-medium text-slate-700 group-hover:text-rose-600 transition-colors">Queja</span>
                    </label>
                    <label className="flex items-center gap-3 cursor-pointer group">
                      <div className="relative flex items-center justify-center">
                        <input 
                          type="radio" 
                          name="tipo"
                          checked={formData.tipo === 'Reclamo'}
                          onChange={() => setFormData({...formData, tipo: 'Reclamo'})}
                          className="w-5 h-5 appearance-none border-2 border-slate-300 rounded-full checked:border-rose-600 transition-all cursor-pointer"
                        />
                        <div className={`absolute w-2.5 h-2.5 bg-rose-600 rounded-full transition-all scale-0 ${formData.tipo === 'Reclamo' ? 'scale-100' : ''}`}></div>
                      </div>
                      <span className="text-sm font-medium text-slate-700 group-hover:text-rose-600 transition-colors">Reclamo</span>
                    </label>
                  </div>
                  <div className="text-[11px] text-slate-400 leading-relaxed font-medium">
                    <p>Queja: Disconformidad no relacionada con productos o servicios.</p>
                    <p>Reclamo: Disconformidad relacionada con productos o servicios.</p>
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-bold text-slate-700">Descripción detallada del incidente *</label>
                  <textarea 
                    required
                    rows={6}
                    value={formData.detalle}
                    onChange={e => setFormData({...formData, detalle: e.target.value})}
                    placeholder="Escriba aquí los detalles..."
                    className="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none transition-all placeholder:text-slate-400"
                  ></textarea>
                </div>
              </div>
            </div>

            <div className="pt-8 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
              <p className="text-[11px] text-slate-400 max-w-sm italic">
                De conformidad con lo establecido en el Código de Protección y Defensa del Consumidor, esta institución cuenta con un Libro de Reclamaciones a su disposición.
              </p>
              <button 
                type="submit"
                className="w-full md:w-auto bg-rose-600 text-white px-12 py-5 rounded-2xl font-black text-lg hover:bg-rose-500 transition-all shadow-xl shadow-rose-200 flex items-center justify-center gap-3"
              >
                Enviar Hoja de Reclamación
                <span>➔</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  );
};

export default ComplaintsSection;
