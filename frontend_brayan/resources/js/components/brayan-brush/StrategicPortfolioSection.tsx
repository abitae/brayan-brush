import { Link } from '@inertiajs/react';
import { ServiceCoverImage, ServiceIconBox, type ServiceVisual } from '@/components/brayan-brush/ServiceVisuals';
import { usePageContent } from '@/hooks/use-page-content';

interface ServiceItem extends ServiceVisual {
  id: string;
  description: string;
}

interface StrategicPortfolioSectionProps {
  items: ServiceItem[];
}

function ServiceCard({
  service,
  featured = false,
  index,
}: {
  service: ServiceItem;
  featured?: boolean;
  index: number;
}) {
  return (
    <Link
      href={`/servicios/${service.id}`}
      className={`group relative flex flex-col overflow-hidden rounded-[2rem] border-2 border-slate-100 bg-white shadow-md transition-all duration-500 hover:-translate-y-2 hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 ${
        featured ? 'lg:row-span-2' : ''
      }`}
    >
      <div className="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-emerald-50 blur-3xl transition-colors group-hover:bg-emerald-100/70 pointer-events-none" />

      {featured && (
        <div className="absolute right-6 top-6 z-20 rounded-full bg-emerald-600 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg">
          Más solicitado
        </div>
      )}

      {service.image_url && (
        <div className={`relative z-10 overflow-hidden ${featured ? 'h-48' : 'h-32'}`}>
          <ServiceCoverImage
            service={service}
            className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-white via-white/20 to-transparent" />
        </div>
      )}

      <div className={`relative z-10 flex flex-col flex-1 ${featured ? 'p-8 md:p-10' : 'p-7 md:p-8'}`}>
        <div className="mb-6 flex items-start justify-between gap-4">
          <div
            className={`flex shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-50 text-slate-600 shadow-sm transition-all duration-500 group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white ${
              featured ? 'h-20 w-20' : 'h-16 w-16'
            }`}
          >
            <ServiceIconBox service={service} />
          </div>
          <span className="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-slate-500">
            0{index + 1}
          </span>
        </div>

        <h3
          className={`font-black text-slate-900 tracking-tight transition-colors group-hover:text-emerald-700 ${
            featured ? 'mb-4 text-2xl md:text-3xl' : 'mb-3 text-xl'
          }`}
        >
          {service.title}
        </h3>

        <p
          className={`flex-grow leading-relaxed text-slate-500 ${
            featured ? 'mb-8 text-base md:text-lg' : 'mb-6 text-sm md:text-base'
          }`}
        >
          {featured
            ? service.description
            : service.description.length > 100
              ? `${service.description.slice(0, 100)}…`
              : service.description}
        </p>

        <div className="mt-auto flex items-center justify-between border-t border-slate-100 pt-5 transition-colors group-hover:border-emerald-100">
          <span className="flex items-center gap-2 text-sm font-black uppercase tracking-widest text-emerald-600">
            Ver detalles
            <span className="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 transition-all group-hover:translate-x-1 group-hover:bg-emerald-600 group-hover:text-white">
              →
            </span>
          </span>
        </div>
      </div>

      {featured && (
        <div className="relative z-10 border-t border-emerald-100 bg-gradient-to-r from-emerald-50 to-white px-8 py-5 md:px-10">
          <div className="flex flex-wrap gap-3">
            {['Rápido', 'Seguro', 'Rastreable'].map((tag) => (
              <span
                key={tag}
                className="rounded-full border border-emerald-200 bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700"
              >
                {tag}
              </span>
            ))}
          </div>
        </div>
      )}
    </Link>
  );
}

export default function StrategicPortfolioSection({ items }: StrategicPortfolioSectionProps) {
  const content = usePageContent();
  const portfolio = content.portfolio;
  const highlights = [
    { label: portfolio.highlight_1_label, value: portfolio.highlight_1_value },
    { label: portfolio.highlight_2_label, value: portfolio.highlight_2_value },
    { label: portfolio.highlight_3_label, value: portfolio.highlight_3_value },
  ];
  const [featured, ...rest] = items;
  const hasFeatured = Boolean(featured);

  return (
    <section id="servicios" className="relative overflow-hidden bg-slate-50 py-20 md:py-28">
      <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(5,150,105,0.06),transparent_55%)]" />

      <div className="relative mx-auto max-w-7xl px-4">
        {/* Encabezado */}
        <div className="mb-14 grid grid-cols-1 items-end gap-10 lg:grid-cols-12 lg:gap-16">
          <div className="lg:col-span-7">
            <span className="mb-4 block text-[10px] font-black uppercase tracking-[0.35em] text-emerald-600">
              {portfolio.eyebrow}
            </span>
            <h2 className="mb-5 text-4xl font-black tracking-tight text-slate-900 md:text-5xl lg:text-6xl">
              {portfolio.title}
            </h2>
            <p className="max-w-xl text-lg font-medium leading-relaxed text-slate-500">{portfolio.subtitle}</p>
          </div>

          <div className="lg:col-span-5">
            <div className="grid grid-cols-3 gap-3 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
              {highlights.map((item) => (
                <div key={item.label} className="text-center">
                  <p className="text-[9px] font-black uppercase tracking-wider text-slate-400">{item.label}</p>
                  <p className="mt-1 text-sm font-black text-slate-900">{item.value}</p>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Grid de servicios */}
        {items.length === 0 ? (
          <div className="rounded-[2rem] border-2 border-dashed border-slate-200 bg-white px-8 py-20 text-center">
            <p className="text-lg font-bold text-slate-600">Próximamente publicaremos nuestros servicios.</p>
          </div>
        ) : items.length === 1 && featured ? (
          <div className="max-w-2xl">
            <ServiceCard service={featured} featured index={0} />
          </div>
        ) : (
          <div
            className={`grid gap-6 ${
              hasFeatured && rest.length > 0
                ? 'grid-cols-1 lg:grid-cols-2 lg:grid-rows-2'
                : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
            }`}
          >
            {hasFeatured && <ServiceCard service={featured} featured index={0} />}
            {rest.map((service, idx) => (
              <ServiceCard key={service.id} service={service} index={idx + 1} />
            ))}
          </div>
        )}

        {/* CTA inferior */}
        <div className="mt-14 flex flex-col items-center justify-between gap-6 rounded-[2rem] border border-slate-200 bg-slate-900 px-8 py-10 md:flex-row md:px-12">
          <div>
            <p className="mb-2 text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">
              {portfolio.cta_eyebrow}
            </p>
            <h3 className="text-2xl font-black text-white md:text-3xl">{portfolio.cta_title}</h3>
            <p className="mt-2 max-w-lg text-sm text-slate-400">{portfolio.cta_text}</p>
          </div>
          <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
            <Link
              href="/servicios"
              className="inline-flex items-center justify-center rounded-2xl border-2 border-slate-600 px-8 py-4 font-black text-white transition-all hover:border-emerald-500 hover:text-emerald-400"
            >
              Ver todos los servicios
            </Link>
            <Link
              href="/cotizar"
              className="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-8 py-4 font-black text-white shadow-lg shadow-emerald-900/30 transition-all hover:bg-emerald-500"
            >
              Cotizar ahora
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}
