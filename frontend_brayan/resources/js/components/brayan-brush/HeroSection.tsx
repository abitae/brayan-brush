import { Link } from '@inertiajs/react';
import { ICONS } from '@/constants/brayan';
import { usePageContent } from '@/hooks/use-page-content';

export interface HeroConfig {
  company_name: string;
  hero_title: string;
  hero_subtitle: string;
  banner_bg_url?: string | null;
  banner_url?: string | null;
}

const DEFAULT_BANNER =
  'https://images.unsplash.com/photo-1501700493788-fa1a4fc9fe62?auto=format&fit=crop&q=80&w=1200';

interface HeroSectionProps {
  config: HeroConfig;
}

export default function HeroSection({ config }: HeroSectionProps) {
  const content = usePageContent();
  const hero = content.hero;
  const stats = [
    { value: hero.stat_1_value, label: hero.stat_1_label, sub: hero.stat_1_sub },
    { value: hero.stat_2_value, label: hero.stat_2_label, sub: hero.stat_2_sub },
    { value: hero.stat_3_value, label: hero.stat_3_label, sub: hero.stat_3_sub },
  ];
  const TruckIcon = ICONS.Truck;
  const SearchIcon = ICONS.Search;
  const MapPinIcon = ICONS.MapPin;
  const bannerImage = config.banner_url || DEFAULT_BANNER;

  return (
    <section className="relative min-h-[92vh] flex items-center overflow-hidden">
      {/* Fondo */}
      <div className="absolute inset-0 z-0">
        {config.banner_bg_url ? (
          <img
            src={config.banner_bg_url}
            alt=""
            className="w-full h-full object-cover scale-105"
            aria-hidden
          />
        ) : (
          <div className="w-full h-full bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950" />
        )}
        <div className="absolute inset-0 bg-gradient-to-r from-white via-white/98 to-white/40 lg:to-transparent" />
        <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_80%_20%,rgba(5,150,105,0.12),transparent_50%)]" />
        <div
          className="absolute inset-0 opacity-[0.03]"
          style={{
            backgroundImage:
              'linear-gradient(rgba(15,23,42,0.8) 1px, transparent 1px), linear-gradient(90deg, rgba(15,23,42,0.8) 1px, transparent 1px)',
            backgroundSize: '48px 48px',
          }}
          aria-hidden
        />
      </div>

      <div className="max-w-7xl mx-auto px-4 relative z-10 w-full pt-28 pb-16 lg:pt-32 lg:pb-24">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
          {/* Contenido */}
          <div className="lg:col-span-6 xl:col-span-5">
            <div className="inline-flex items-center gap-3 py-2.5 px-5 rounded-full bg-white/80 backdrop-blur-md border border-emerald-200/60 text-emerald-800 font-black text-[10px] uppercase tracking-[0.35em] mb-8 shadow-sm">
              <span className="relative flex h-2.5 w-2.5">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500" />
              </span>
              {hero.badge}
            </div>

            <h1 className="text-5xl sm:text-6xl md:text-7xl xl:text-[5.5rem] font-black text-slate-900 leading-[0.9] tracking-tighter mb-6">
              {config.company_name}
              <span className="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-emerald-700 via-emerald-500 to-teal-400">
                {config.hero_title}
              </span>
            </h1>

            <p className="text-lg md:text-xl text-slate-600 leading-relaxed max-w-xl font-medium mb-10">
              {config.hero_subtitle}
            </p>

            <div className="flex flex-col sm:flex-row gap-4 mb-12">
              <Link
                href="/cotizar"
                className="inline-flex items-center justify-center gap-3 bg-emerald-600 text-white px-10 py-5 rounded-2xl font-black text-lg transition-all shadow-[0_20px_50px_-12px_rgba(5,150,105,0.45)] hover:bg-emerald-500 hover:shadow-[0_24px_56px_-12px_rgba(5,150,105,0.5)] active:scale-[0.98] group"
              >
                Cotizar envío
                <span className="group-hover:translate-x-1 transition-transform">→</span>
              </Link>
              <Link
                href="/rastreo"
                className="inline-flex items-center justify-center gap-3 bg-white text-slate-800 px-10 py-5 rounded-2xl font-black text-lg border-2 border-slate-200 hover:border-emerald-500 hover:text-emerald-700 transition-all active:scale-[0.98]"
              >
                <SearchIcon />
                Rastrear guía
              </Link>
            </div>

            <div className="grid grid-cols-3 gap-4 max-w-md">
              {stats.map((stat) => (
                <div
                  key={stat.label}
                  className="bg-white/70 backdrop-blur-sm rounded-2xl px-4 py-4 border border-slate-100 shadow-sm"
                >
                  <p className="text-2xl font-black text-emerald-600 leading-none mb-1">{stat.value}</p>
                  <p className="text-[10px] font-black text-slate-800 uppercase tracking-wider">{stat.label}</p>
                  <p className="text-[9px] text-slate-400 font-medium mt-0.5 hidden sm:block">{stat.sub}</p>
                </div>
              ))}
            </div>
          </div>

          {/* Imagen principal */}
          <div className="lg:col-span-6 xl:col-span-7 relative">
            <div className="absolute -top-8 -right-8 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none" />
            <div className="absolute -bottom-12 -left-8 w-48 h-48 bg-teal-500/15 rounded-full blur-3xl pointer-events-none" />

            <div className="relative rounded-[2.5rem] overflow-hidden shadow-[0_40px_80px_-20px_rgba(15,23,42,0.35)] border border-white/50 aspect-[4/3] lg:aspect-[5/4] max-w-2xl lg:ml-auto">
              <img
                src={bannerImage}
                alt={`Flota ${config.company_name}`}
                className="w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/20 to-transparent" />
              <div className="absolute inset-0 bg-gradient-to-r from-emerald-900/30 to-transparent" />

              <div className="absolute bottom-0 left-0 right-0 p-8 md:p-10">
                <div className="flex items-center gap-3 mb-4">
                  <div className="p-2.5 bg-emerald-500 rounded-xl text-white shadow-lg">
                    <TruckIcon />
                  </div>
                  <div>
                    <p className="text-[10px] font-black text-emerald-400 uppercase tracking-[0.25em]">
                      {hero.image_caption_title}
                    </p>
                    <p className="text-white font-black text-lg leading-tight">{hero.image_caption_cities}</p>
                  </div>
                </div>
                <p className="text-white/90 text-sm md:text-base font-medium max-w-sm leading-relaxed">
                  {hero.image_caption_text}
                </p>
              </div>
            </div>

            {/* Tarjeta flotante */}
            <div className="absolute -left-2 sm:left-0 top-8 lg:-left-6 bg-white rounded-2xl p-4 shadow-xl border border-slate-100 flex items-center gap-3 max-w-[200px] animate-[float_6s_ease-in-out_infinite]">
              <div className="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <MapPinIcon />
              </div>
              <div>
                <p className="text-[9px] font-black text-slate-400 uppercase tracking-wider">{hero.float_card_title}</p>
                <p className="text-sm font-black text-slate-900">{hero.float_card_text}</p>
              </div>
            </div>

            <div className="absolute -right-2 sm:right-0 bottom-24 lg:-right-4 bg-slate-900 rounded-2xl px-5 py-4 shadow-2xl border border-slate-700 flex items-center gap-3">
              <div className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              <p className="text-white text-xs font-bold">{hero.float_badge}</p>
            </div>
          </div>
        </div>
      </div>

      <style>{`
        @keyframes float {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-8px); }
        }
      `}</style>
    </section>
  );
}
