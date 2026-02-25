
import React, { useState } from 'react';

interface ContactProps {
  onQuoteSubmit: (q: any) => void;
}

const ContactSection: React.FC<ContactProps> = ({ onQuoteSubmit }) => {
  const [formData, setFormData] = useState({
    nombre: '',
    email: '',
    telefono: '',
    servicio: 'Courier Local',
    mensaje: ''
  });
  const [submitted, setSubmitted] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onQuoteSubmit(formData);
    setSubmitted(true);
    setTimeout(() => {
        setSubmitted(false);
        setFormData({ nombre: '', email: '', telefono: '', servicio: 'Courier Local', mensaje: '' });
    }, 5000);
  };

  return (
    <section className="py-24 bg-slate-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div>
            <h2 className="text-5xl font-black text-slate-900 mb-8 leading-tight">
              Solicita tu <span className="text-emerald-600">Cotización</span> en línea
            </h2>
            <p className="text-slate-600 text-lg mb-10 leading-relaxed font-medium">
              Completa el formulario y uno de nuestros asesores expertos te enviará una propuesta personalizada de inmediato. Tu requerimiento aparecerá directamente en nuestro panel de gestión.
            </p>
          </div>

          <div className="bg-white rounded-[40px] p-8 md:p-12 border border-slate-100 shadow-2xl relative">
            {submitted ? (
              <div className="text-center py-20 animate-in zoom-in duration-500">
                <div className="text-6xl mb-6">✅</div>
                <h3 className="text-2xl font-black text-slate-900 mb-4">¡Solicitud Registrada!</h3>
                <p className="text-slate-600">El administrador está revisando tu cotización. Te contactaremos pronto.</p>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-6">
                <input 
                  required placeholder="Nombre" value={formData.nombre}
                  onChange={e => setFormData({...formData, nombre: e.target.value})}
                  className="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                />
                <div className="grid grid-cols-2 gap-4">
                  <input 
                    required type="email" placeholder="Email" value={formData.email}
                    onChange={e => setFormData({...formData, email: e.target.value})}
                    className="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                  />
                  <input 
                    required type="tel" placeholder="Teléfono" value={formData.telefono}
                    onChange={e => setFormData({...formData, telefono: e.target.value})}
                    className="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                  />
                </div>
                <select 
                  value={formData.servicio}
                  onChange={e => setFormData({...formData, servicio: e.target.value})}
                  className="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none appearance-none"
                >
                  <option>Courier Local</option>
                  <option>Mudanza Nacional</option>
                  <option>Carga Consolidada</option>
                </select>
                <textarea 
                  required rows={4} placeholder="Detalles de su envío..." value={formData.mensaje}
                  onChange={e => setFormData({...formData, mensaje: e.target.value})}
                  className="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-emerald-500 outline-none"
                ></textarea>
                <button type="submit" className="w-full bg-emerald-600 text-white py-5 rounded-2xl font-black text-lg hover:bg-emerald-500 transition-all shadow-xl">
                  Enviar Cotización al Administrador
                </button>
              </form>
            )}
          </div>
        </div>
      </div>
    </section>
  );
};

export default ContactSection;
