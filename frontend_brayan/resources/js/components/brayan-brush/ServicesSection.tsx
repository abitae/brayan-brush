import { ICONS } from '@/constants/brayan';

interface ServiceItem {
  id: string;
  title: string;
  description: string;
  icon_type: string;
  image_url?: string | null;
}

interface ServicesSectionProps {
  items: ServiceItem[];
}

export default function ServicesSection({ items }: ServicesSectionProps) {
  return (
    <section className="py-24 bg-slate-50" id="servicios-completo">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-16">
          <span className="text-emerald-600 font-bold uppercase tracking-widest text-sm mb-4 block">
            Nuestro Portafolio Administrable
          </span>
          <h2 className="text-5xl font-black text-slate-900 mb-4 tracking-tight">Servicios Especializados</h2>
          <p className="text-slate-600 max-w-2xl mx-auto text-lg">
            Soluciones logísticas adaptadas dinámicamente desde el panel central.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {items.map((service) => {
            const Icon =
              service.icon_type === 'Box' ? ICONS.Box : service.icon_type === 'Home' ? ICONS.Home : ICONS.Package;
            return (
              <div
                key={service.id}
                className="bg-white p-8 rounded-[35px] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500 group"
              >
                <div className="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 overflow-hidden">
                  {service.image_url ? (
                    <img src={service.image_url} alt="" className="w-full h-full object-cover" />
                  ) : (
                    <Icon />
                  )}
                </div>
                <h3 className="text-xl font-bold text-slate-900 mb-3 group-hover:text-emerald-600 transition-colors">
                  {service.title}
                </h3>
                <p className="text-slate-500 text-sm leading-relaxed mb-6">{service.description}</p>
                <span className="text-emerald-600 font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:gap-4 transition-all">
                  Más información <span>→</span>
                </span>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
