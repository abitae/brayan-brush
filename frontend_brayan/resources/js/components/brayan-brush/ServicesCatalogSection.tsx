import { Link } from '@inertiajs/react';
import { ServiceCoverImage, ServiceIconBox, type ServiceVisual } from '@/components/brayan-brush/ServiceVisuals';

export interface CatalogServiceItem extends ServiceVisual {
  id: string;
  description: string;
}

interface ServicesCatalogSectionProps {
  items: CatalogServiceItem[];
}

function CatalogCard({ service, index }: { service: CatalogServiceItem; index: number }) {
  const excerpt =
    service.description.length > 120 ? `${service.description.slice(0, 120)}…` : service.description;

  return (
    <Link
      href={`/servicios/${service.id}`}
      className="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-emerald-500/40 hover:shadow-xl hover:shadow-emerald-500/10 sm:rounded-[1.75rem]"
    >
      <div className="relative aspect-[4/3] w-full shrink-0 overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 sm:aspect-[16/11]">
        {service.image_url ? (
          <ServiceCoverImage
            service={service}
            className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
        ) : (
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(5,150,105,0.35),transparent_55%)]" />
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/10 to-transparent" />

        <span className="absolute right-3 top-3 rounded-full bg-black/30 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-white backdrop-blur-sm sm:right-4 sm:top-4 sm:px-3 sm:text-[10px]">
          0{index + 1}
        </span>

        <div className="absolute bottom-3 left-3 flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl border-2 border-white/20 bg-white text-slate-700 shadow-lg transition-transform duration-300 group-hover:scale-105 sm:bottom-4 sm:left-4 sm:h-12 sm:w-12 sm:rounded-2xl">
          <ServiceIconBox service={service} />
        </div>
      </div>

      <div className="flex flex-1 flex-col p-4 sm:p-5 md:p-6">
        <h3 className="mb-2 line-clamp-2 text-base font-black leading-snug tracking-tight text-slate-900 transition-colors group-hover:text-emerald-700 sm:mb-3 sm:text-lg md:text-xl">
          {service.title}
        </h3>
        <p className="line-clamp-3 flex-grow text-sm leading-relaxed text-slate-500">{excerpt}</p>

        <div className="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 transition-colors group-hover:border-emerald-100 sm:mt-5 sm:pt-5">
          <span className="text-[10px] font-black uppercase tracking-widest text-emerald-600 sm:text-xs">
            Ver detalle
          </span>
          <span className="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-sm text-emerald-600 transition-all group-hover:bg-emerald-600 group-hover:text-white sm:h-9 sm:w-9">
            →
          </span>
        </div>
      </div>
    </Link>
  );
}

export default function ServicesCatalogSection({ items }: ServicesCatalogSectionProps) {
  if (items.length === 0) {
    return null;
  }

  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
      {items.map((service, index) => (
        <CatalogCard key={service.id} service={service} index={index} />
      ))}
    </div>
  );
}
