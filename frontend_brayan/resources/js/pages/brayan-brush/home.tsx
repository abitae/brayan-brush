import { Head, usePage } from '@inertiajs/react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import AboutSection from '@/components/brayan-brush/AboutSection';
import AgenciesSection from '@/components/brayan-brush/AgenciesSection';
import ContactSection from '@/components/brayan-brush/ContactSection';
import HeroSection, { type HeroConfig } from '@/components/brayan-brush/HeroSection';
import StrategicPortfolioSection from '@/components/brayan-brush/StrategicPortfolioSection';

interface HomeProps {
  services: { id: string; title: string; description: string; icon_type: string }[];
  prohibitedItems: { title: string; items: string[] }[];
}

export default function Home({ services }: HomeProps) {
  const siteConfig = usePage().props.siteConfig as HeroConfig | null;
  const config: HeroConfig = siteConfig ?? {
    company_name: 'Brayan Brush',
    hero_title: 'Logística Inteligente.',
    hero_subtitle:
      'Especialistas en transporte terrestre nacional con la flota más segura del Perú. Envíos, rastreo y entrega a todo el país.',
    banner_bg_url: null,
    banner_url: 'https://images.unsplash.com/photo-1501700493788-fa1a4fc9fe62?auto=format&fit=crop&q=80&w=1200',
  };

  return (
    <BrayanBrushLayout>
      <Head title="Inicio - Brayan Brush" />
      <HeroSection config={config} />
      <StrategicPortfolioSection items={services} />
      <div id="nosotros">
        <AboutSection companyName={config.company_name} />
      </div>
      <div id="agencias">
        <AgenciesSection />
      </div>
      <div id="contacto-home">
        <ContactSection />
      </div>
    </BrayanBrushLayout>
  );
}
