import { Link } from '@inertiajs/react';
import { ServiceCoverImage, ServiceIconBox, type ServiceVisual } from '@/components/brayan-brush/ServiceVisuals';

interface ServiceItem extends ServiceVisual {
  id: string;
  description: string;
}

interface ServicesSectionProps {
  items: ServiceItem[];
}

export default function ServicesSection({ items }: ServicesSectionProps) {
  return (
    <section className="py-24" id="servicios-completo">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {items.map((service) => (
            <Link
              href={`/servicios/${service.id}`}
              key={service.id}
              className="group relative bg-white rounded-[40px] border-2 border-slate-100 hover:border-emerald-500/30 shadow-md hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-3 transition-all duration-500 overflow-hidden flex flex-col"
            >
              <div className="absolute -right-16 -top-16 w-48 h-48 bg-emerald-50 rounded-full blur-3xl group-hover:bg-emerald-100/60 transition-colors pointer-events-none" />

              {service.image_url && (
                <div className="relative z-10 h-40 w-full overflow-hidden">
                  <ServiceCoverImage
                    service={service}
                    className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-white/80 to-transparent" />
                </div>
              )}

              <div className="relative z-10 p-8 md:p-10 flex flex-col flex-1">
                <div className="w-16 h-16 bg-slate-50 text-slate-600 rounded-3xl flex items-center justify-center mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm overflow-hidden shrink-0 group-hover:scale-110">
                  <ServiceIconBox service={service} />
                </div>
                <h3 className="text-2xl font-black text-slate-900 mb-4 group-hover:text-emerald-700 transition-colors tracking-tight">
                  {service.title}
                </h3>
                <p className="text-slate-500 leading-relaxed mb-8 flex-grow">
                  {service.description.length > 120 ? `${service.description.substring(0, 120)}...` : service.description}
                </p>

                <div className="mt-auto pt-6 border-t border-slate-100 group-hover:border-emerald-100 transition-colors">
                  <span className="text-emerald-600 font-black text-sm uppercase tracking-widest flex items-center gap-3 group-hover:gap-5 transition-all">
                    Ver Detalles
                    <span className="bg-emerald-50 group-hover:bg-emerald-600 group-hover:text-white w-8 h-8 rounded-full flex items-center justify-center transition-all">
                      →
                    </span>
                  </span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}
