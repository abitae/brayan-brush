import { Head, Link } from '@inertiajs/react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import ServicesSection from '@/components/brayan-brush/ServicesSection';
import { ServiceCoverImage, ServiceIconBox, type ServiceVisual } from '@/components/brayan-brush/ServiceVisuals';

interface ServiceDetail extends ServiceVisual {
  id: string;
  description: string;
}

interface ServiceDetailProps {
  service: ServiceDetail;
  otherServices: ServiceDetail[];
}

export default function ServicioDetalle({ service, otherServices }: ServiceDetailProps) {
  return (
    <BrayanBrushLayout>
      <Head title={`${service.title} - Brayan Brush`} />

      <section className="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden items-center flex bg-slate-900 min-h-[50vh]">
        {service.image_url && (
          <div className="absolute inset-0 z-0">
            <ServiceCoverImage service={service} className="w-full h-full object-cover opacity-30" />
            <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent" />
          </div>
        )}
        <div className="max-w-7xl mx-auto px-4 relative z-10 w-full flex flex-col md:flex-row items-center gap-12">
          <div className="w-24 h-24 md:w-32 md:h-32 bg-emerald-500 rounded-3xl flex items-center justify-center text-white shrink-0 shadow-2xl shadow-emerald-500/20 rotate-3 overflow-hidden">
            <ServiceIconBox service={service} className="flex h-full w-full items-center justify-center text-white" />
          </div>
          <div className="text-center md:text-left">
            <span className="text-emerald-400 font-black uppercase tracking-[0.3em] text-xs mb-4 block">
              Servicio Logístico
            </span>
            <h1 className="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight leading-tight">
              {service.title}
            </h1>
            <p className="text-lg md:text-2xl text-slate-300 font-medium max-w-3xl leading-relaxed">
              Entendemos la importancia de tu carga. Operamos con los más altos estándares.
            </p>
          </div>
        </div>
      </section>

      <section className="py-24 bg-white relative">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-black text-slate-900 mb-8 border-b-2 border-slate-100 pb-8 tracking-tight">
            Descripción del Servicio
          </h2>
          <p className="text-xl text-slate-600 leading-loose mx-auto">{service.description}</p>

          <div className="mt-16 flex flex-col sm:flex-row items-center justify-center gap-6">
            <Link
              href="/cotizar"
              className="w-full sm:w-auto px-10 py-5 bg-emerald-600 text-white rounded-full font-black text-lg hover:bg-emerald-500 hover:scale-105 transition-all shadow-xl shadow-emerald-500/20"
            >
              Cotizar este servicio
            </Link>
            <Link
              href="/contacto"
              className="w-full sm:w-auto px-10 py-5 bg-slate-100 text-slate-700 rounded-full font-black text-lg hover:bg-slate-200 hover:text-slate-900 transition-all border border-slate-200"
            >
              Contactar a un asesor
            </Link>
          </div>
        </div>
      </section>

      {otherServices?.length > 0 && (
        <div className="bg-slate-50 border-t border-slate-100">
          <div className="pt-24 pb-8 text-center px-4">
            <h3 className="text-sm font-black text-emerald-600 uppercase tracking-widest mb-4">Descubre Más</h3>
            <h2 className="text-4xl font-black text-slate-900 tracking-tight">Otros Servicios Especializados</h2>
          </div>
          <ServicesSection items={otherServices} />
        </div>
      )}
    </BrayanBrushLayout>
  );
}
