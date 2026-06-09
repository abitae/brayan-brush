import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import ServicesCatalogSection, {
  type CatalogServiceItem,
} from '@/components/brayan-brush/ServicesCatalogSection';
import { ICONS } from '@/constants/brayan';
import { usePageContent } from '@/hooks/use-page-content';

interface ServiciosProps {
  services: CatalogServiceItem[];
}

function renderHighlightedTitle(title: string, highlight: string) {
  if (!title.includes(highlight)) {
    return (
      <>
        {title}{' '}
        <span className="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-300">
          {highlight}
        </span>
      </>
    );
  }

  return title.split(highlight).map((part, i, arr) => (
    <span key={i}>
      {part}
      {i < arr.length - 1 && (
        <span className="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-300">
          {highlight}
        </span>
      )}
    </span>
  ));
}

export default function Servicios({ services }: ServiciosProps) {
  const page = usePageContent().services_page;
  const [searchTerm, setSearchTerm] = useState('');

  const filteredServices = useMemo(() => {
    const q = searchTerm.trim().toLowerCase();
    if (!q) return services;
    return services.filter(
      (s) => s.title.toLowerCase().includes(q) || s.description.toLowerCase().includes(q)
    );
  }, [services, searchTerm]);

  return (
    <BrayanBrushLayout>
      <Head title="Servicios - Brayan Brush" />

      {/* Hero */}
      <section className="relative overflow-hidden bg-slate-950 pt-28 pb-20 sm:pt-36 sm:pb-28 md:pt-44 md:pb-36">
        <div className="pointer-events-none absolute inset-0">
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_20%_0%,rgba(5,150,105,0.25),transparent_50%)]" />
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_80%_100%,rgba(5,150,105,0.15),transparent_45%)]" />
          <div
            className="absolute inset-0 opacity-[0.04]"
            style={{
              backgroundImage:
                'linear-gradient(rgba(255,255,255,0.9) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.9) 1px, transparent 1px)',
              backgroundSize: '56px 56px',
            }}
          />
        </div>

        <div className="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 items-end gap-8 md:gap-10 lg:grid-cols-12 lg:gap-12">
            <div className="lg:col-span-7">
              <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-500/25 bg-emerald-500/10 px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.25em] text-emerald-400 sm:mb-6 sm:px-4 sm:py-2 sm:text-[10px]">
                <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-400" />
                {page.eyebrow}
              </div>
              <h1 className="mb-4 text-3xl font-black leading-[1.08] tracking-tight text-white sm:mb-6 sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl">
                {renderHighlightedTitle(page.title, page.title_highlight)}
              </h1>
              <p className="max-w-2xl text-base font-medium leading-relaxed text-slate-400 sm:text-lg md:text-xl">
                {page.subtitle}
              </p>
            </div>

            <div className="w-full lg:col-span-5">
              <div className="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md sm:rounded-[2rem] sm:p-6">
                <div className="mb-4 grid grid-cols-3 gap-2 text-center sm:mb-5 sm:gap-3">
                  <div className="rounded-xl bg-white/5 px-2 py-3 sm:rounded-2xl sm:px-3 sm:py-4">
                    <p className="text-xl font-black text-white sm:text-2xl">{services.length}</p>
                    <p className="mt-1 text-[8px] font-bold uppercase tracking-wider text-slate-400 sm:text-[9px]">Servicios</p>
                  </div>
                  <div className="rounded-xl bg-white/5 px-2 py-3 sm:rounded-2xl sm:px-3 sm:py-4">
                    <p className="text-xl font-black text-emerald-400 sm:text-2xl">24/7</p>
                    <p className="mt-1 text-[8px] font-bold uppercase tracking-wider text-slate-400 sm:text-[9px]">Rastreo</p>
                  </div>
                  <div className="rounded-xl bg-white/5 px-2 py-3 sm:rounded-2xl sm:px-3 sm:py-4">
                    <p className="text-xl font-black text-white sm:text-2xl">PE</p>
                    <p className="mt-1 text-[8px] font-bold uppercase tracking-wider text-slate-400 sm:text-[9px]">Cobertura</p>
                  </div>
                </div>

                <label className="relative block">
                  <span className="sr-only">Buscar servicio</span>
                  <div className="pointer-events-none absolute left-4 top-1/2 z-10 -translate-y-1/2 text-emerald-400/80">
                    <ICONS.Search />
                  </div>
                  <input
                    type="search"
                    placeholder="Buscar por nombre o descripción…"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full rounded-xl border border-white/10 bg-slate-900/60 py-3.5 pl-11 pr-16 text-sm text-white placeholder:text-slate-500 outline-none transition focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 sm:rounded-2xl sm:py-4 sm:pl-12 sm:pr-4 sm:text-base"
                  />
                  {searchTerm && (
                    <button
                      type="button"
                      onClick={() => setSearchTerm('')}
                      className="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-bold text-slate-400 hover:text-white"
                    >
                      Limpiar
                    </button>
                  )}
                </label>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Catálogo */}
      <section className="relative bg-slate-50 py-12 sm:py-16 md:py-24">
        <div className="pointer-events-none absolute inset-x-0 top-0 h-16 -translate-y-full bg-gradient-to-b from-slate-950 to-slate-50 sm:h-24" />

        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="mb-8 flex flex-col gap-4 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p className="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600">Catálogo completo</p>
              <h2 className="mt-2 text-2xl font-black text-slate-900 md:text-3xl">
                {filteredServices.length === services.length
                  ? 'Todos nuestros servicios'
                  : `${filteredServices.length} resultado${filteredServices.length === 1 ? '' : 's'}`}
              </h2>
            </div>
            <div className="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap sm:gap-3">
              <Link
                href="/cotizar"
                className="rounded-xl bg-emerald-600 px-5 py-2.5 text-center text-sm font-black text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-500"
              >
                Cotizar envío
              </Link>
              <Link
                href="/rastreo"
                className="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-center text-sm font-black text-slate-700 transition hover:border-slate-300"
              >
                Rastrear guía
              </Link>
            </div>
          </div>

          {filteredServices.length > 0 ? (
            <ServicesCatalogSection items={filteredServices} />
          ) : (
            <div className="rounded-[2rem] border-2 border-dashed border-slate-200 bg-white px-8 py-24 text-center">
              <div className="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                <svg className="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 className="mb-3 text-2xl font-black text-slate-900">Sin coincidencias</h3>
              <p className="mx-auto mb-8 max-w-md text-slate-500">
                No hay servicios que coincidan con &quot;{searchTerm}&quot;. Prueba con otro término.
              </p>
              <button
                type="button"
                onClick={() => setSearchTerm('')}
                className="rounded-xl bg-slate-900 px-6 py-3 font-bold text-white transition hover:bg-slate-800"
              >
                Ver todos los servicios
              </button>
            </div>
          )}
        </div>
      </section>

      {/* Ventajas */}
      <section className="border-t border-slate-100 bg-white py-14 sm:py-20 md:py-28">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="mb-10 text-center sm:mb-14">
            <p className="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600">Por qué elegirnos</p>
            <h2 className="mt-3 text-2xl font-black text-slate-900 sm:text-3xl md:text-4xl">Logística con respaldo real</h2>
          </div>

          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            {[
              { title: page.feature_1_title, text: page.feature_1_text, Icon: ICONS.Box, accent: 'from-emerald-500/10' },
              { title: page.feature_2_title, text: page.feature_2_text, Icon: ICONS.Package, accent: 'from-teal-500/10' },
              { title: page.feature_3_title, text: page.feature_3_text, Icon: ICONS.Home, accent: 'from-cyan-500/10' },
            ].map((item, idx) => {
              const Icon = item.Icon;
              return (
                <div
                  key={item.title}
                  className={`relative overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-br ${item.accent} to-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:rounded-[2rem] sm:p-8`}
                >
                  <span className="absolute right-6 top-6 text-4xl font-black text-slate-100">0{idx + 1}</span>
                  <div className="relative mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-emerald-600 shadow-sm">
                    <Icon />
                  </div>
                  <h3 className="relative mb-3 text-xl font-black text-slate-900">{item.title}</h3>
                  <p className="relative text-sm leading-relaxed text-slate-600">{item.text}</p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="relative overflow-hidden bg-slate-950 py-16 sm:py-24 md:py-32">
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(5,150,105,0.2),transparent_60%)]" />
        <div className="relative z-10 mx-auto max-w-3xl px-4 text-center sm:px-6">
          <h2 className="mb-4 text-2xl font-black tracking-tight text-white sm:mb-5 sm:text-3xl md:text-4xl lg:text-5xl">
            {page.cta_title}
          </h2>
          <p className="mb-8 text-base text-slate-400 sm:mb-10 sm:text-lg md:text-xl">{page.cta_text}</p>
          <div className="flex flex-col justify-center gap-3 sm:flex-row sm:gap-4">
            <Link
              href="/cotizar"
              className="rounded-xl bg-emerald-600 px-8 py-4 text-base font-black text-white shadow-xl shadow-emerald-900/30 transition hover:bg-emerald-500 hover:scale-[1.02] sm:rounded-2xl sm:px-10 sm:py-5 sm:text-lg"
            >
              {page.cta_button_primary}
            </Link>
            <Link
              href="/contacto"
              className="rounded-xl border border-white/15 bg-white/5 px-8 py-4 text-base font-black text-white backdrop-blur-sm transition hover:bg-white hover:text-slate-900 sm:rounded-2xl sm:px-10 sm:py-5 sm:text-lg"
            >
              {page.cta_button_secondary}
            </Link>
          </div>
        </div>
      </section>
    </BrayanBrushLayout>
  );
}
